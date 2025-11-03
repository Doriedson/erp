<?php

namespace App\Http\Controllers;

use App\Auth\AuthService;
use App\Http\Response;
use App\Legacy\Config as LegacyConfig;
use App\Legacy\Product as LegacyProduct;
use App\Legacy\ProductExpDate as LegacyProductExpDate;
use App\Support\Notifier;
use App\View\View;

final class ProductExpiryController
{
    public function popup(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $productId = (int)($_POST['id_produto'] ?? 0);

        if ($productId <= 0) {
            Notifier::Add('Produto inválido.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $product = new LegacyProduct();
            $product->Read($productId);

            if (!($productRow = $product->getResult())) {
                Notifier::Add('Produto não encontrado.', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $productRow = LegacyProduct::FormatFields($productRow);

            $tplProduct = new View('product');
            $tplHome    = new View('home');

            $expDate = new LegacyProductExpDate();
            $expDate->getList($productId);

            $linesHtml = '';
            foreach ($expDate->getResults() as $row) {
                $row = LegacyProductExpDate::FormatFields($row);
                $row = LegacyProduct::FormatFields($row);

                if ((int)$row['dias'] <= 0) {
                    $row['extra_block_productexpdate_days'] = $tplHome->getContent([], 'EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED');
                } else {
                    $row['extra_block_productexpdate_days'] = $tplHome->getContent($row, 'EXTRA_BLOCK_PRODUCTEXPDATE_DAYS');
                }

                $linesHtml .= $tplProduct->getContent($row, 'EXTRA_BLOCK_PRODUCT_EXPDATE_TR');
            }

            $data = [
                'id_produto'                      => (string)$productId,
                'produto'                         => $productRow['produto'] ?? '',
                'produtotipo'                     => $productRow['produtotipo'] ?? '',
                'extra_block_product_button_status' => $productRow['extra_block_product_button_status'] ?? '',
                'product_expdate_notfound'        => $linesHtml === '' ? '' : 'hidden',
                'extra_block_product_expdate_tr'  => $linesHtml,
                'data'                            => date('Y-m-d'),
            ];

            $html = $tplProduct->getContent($data, 'EXTRA_BLOCK_POPUP_VALIDADE');

            return Response::json($html);

        } catch (\Throwable $e) {
            error_log('[ProductExpiryController@popup] ' . $e->getMessage());
            Notifier::Add('Falha ao carregar validade do produto.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    public function add(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $productId = (int)($_POST['id_produto'] ?? 0);
        $validity  = trim((string)($_POST['validade'] ?? ''));

        if ($productId <= 0 || $validity === '') {
            Notifier::Add('Dados inválidos para cadastrar validade.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $expDate = new LegacyProductExpDate();
            $expDate->Search($productId, $validity);

            if ($expDate->getResult()) {
                Notifier::Add('Data já cadastrada!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $idProdVal = $expDate->Create($productId, $validity);

            if (!$idProdVal) {
                Notifier::Add('Erro ao cadastrar data de validade!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $expDate->Read($idProdVal);

            if (!($row = $expDate->getResult())) {
                Notifier::Add('Erro ao carregar data de validade!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $tplProduct = new View('product');
            $tplHome    = new View('home');

            $row = LegacyProductExpDate::FormatFields($row);
            $row = LegacyProduct::FormatFields($row);

            if ((int)$row['dias'] <= 0) {
                $row['extra_block_productexpdate_days'] = $tplHome->getContent([], 'EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED');
            } else {
                $row['extra_block_productexpdate_days'] = $tplHome->getContent($row, 'EXTRA_BLOCK_PRODUCTEXPDATE_DAYS');
            }

            $response = [
                'product_expdate_tr' => $tplProduct->getContent($row, 'EXTRA_BLOCK_PRODUCT_EXPDATE_TR'),
            ];

            $config = new LegacyConfig();
            $config->Read();

            if (($rowConfig = $config->getResult()) && (int)$row['dias'] <= (int)$rowConfig['product_expirate_days']) {
                $response['cp_expdate_tr'] = $tplHome->getContent($row, 'EXTRA_BLOCK_CP_EXPDATE_TR');
            }

            [, $expirated, $toexpirate] = LegacyProductExpDate::getListHUD();

            $response['expirated']  = $expirated;
            $response['toexpirate'] = $toexpirate;

            Notifier::Add('Data de validade cadastrada com sucesso!', Notifier::NOTIFIER_DONE);

            return Response::json($response);

        } catch (\Throwable $e) {
            error_log('[ProductExpiryController@add] ' . $e->getMessage());
            Notifier::Add('Falha ao cadastrar validade.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    public function delete(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $id = (int)($_POST['id_produtovalidade'] ?? 0);

        if ($id <= 0) {
            Notifier::Add('Identificador inválido.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $expDate = new LegacyProductExpDate();

            if ($expDate->Delete($id) === 0) {
                Notifier::Add('Erro ao excluir data de validade!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            [, $expirated, $toexpirate] = LegacyProductExpDate::getListHUD();

            Notifier::Add('Data de validade removida com sucesso!', Notifier::NOTIFIER_DONE);

            return Response::json([
                'expirated'  => $expirated,
                'toexpirate' => $toexpirate,
            ]);

        } catch (\Throwable $e) {
            error_log('[ProductExpiryController@delete] ' . $e->getMessage());
            Notifier::Add('Falha ao remover validade.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }
}
