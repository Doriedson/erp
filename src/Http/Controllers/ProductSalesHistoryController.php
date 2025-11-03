<?php

namespace App\Http\Controllers;

use App\Auth\AuthService;
use App\Http\Response;
use App\Legacy\Calc;
use App\Legacy\Product as LegacyProduct;
use App\Legacy\ProductComposition;
use App\Legacy\ProductKit;
use App\Legacy\PurchaseOrderItem;
use App\Legacy\SaleOrderItem;
use App\Support\Notifier;
use App\View\View;
use DateInterval;
use DatePeriod;
use DateTime;

final class ProductSalesHistoryController
{
    public function lastEntry(): string
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

            if (!($row = $product->getResult())) {
                Notifier::Add('Erro ao carregar dados do produto.', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $targetProductId = $row['id_produto'];

            if ((int)$row['id_produtotipo'] !== LegacyProduct::PRODUTO_TIPO_NORMAL) {
                switch ((int)$row['id_produtotipo']) {
                    case LegacyProduct::PRODUTO_TIPO_COMPOSICAO:
                        $composition = new ProductComposition();
                        $composition->getList($targetProductId);
                        if ($compositionRow = $composition->getResult()) {
                            $targetProductId = $compositionRow['id_produto'];
                        } else {
                            Notifier::Add('Erro ao carregar dados da composição.', Notifier::NOTIFIER_ERROR);
                            return Response::json(null);
                        }
                        break;

                    case LegacyProduct::PRODUTO_TIPO_KIT:
                        $kit = new ProductKit();
                        $kit->getList($targetProductId);
                        if ($kitRow = $kit->getResult()) {
                            $targetProductId = $kitRow['id_produto'];
                        } else {
                            Notifier::Add('Erro ao carregar dados do kit.', Notifier::NOTIFIER_ERROR);
                            return Response::json(null);
                        }
                        break;
                }
            }

            $purchaseItem = new PurchaseOrderItem();
            $purchaseItem->getLastProductEntry($targetProductId);

            if ($entry = $purchaseItem->getResult()) {
                return Response::json([
                    'datestart'    => date('Y-m-d', strtotime($entry['data'])),
                    'dateend'      => date('Y-m-d'),
                    'dateend_sel'  => true,
                ]);
            }

            return Response::json([
                'datestart'   => date('Y-m-d'),
                'dateend'     => date('Y-m-d'),
                'dateend_sel' => false,
            ]);

        } catch (\Throwable $e) {
            error_log('[ProductSalesHistoryController@lastEntry] ' . $e->getMessage());
            Notifier::Add('Falha ao consultar última compra.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    public function popup(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $productId   = (int)($_POST['id_produto'] ?? 0);
        $dateStart   = (string)($_POST['datestart'] ?? '');
        $dateEnd     = (string)($_POST['dateend'] ?? '');
        $dateEndSel  = filter_var($_POST['dateend_sel'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $dateLock    = filter_var($_POST['datelock'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($productId <= 0) {
            Notifier::Add('Produto inválido.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $product = new LegacyProduct();
            $product->Read($productId);

            if (!($row = $product->getResult())) {
                Notifier::Add('Erro ao carregar dados do produto!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $row = LegacyProduct::FormatFields($row);

            $tplReport = new View('report_sale_one_product');

            $row['datestart']        = $dateStart ?: date('Y-m-d');
            $row['dateend']          = $dateEnd ?: date('Y-m-d');
            $row['dateend_sel']      = $dateEndSel ? 'checked' : '';
            $row['dateend_disabled'] = $dateEndSel ? '' : 'disabled';

            if ($dateLock) {
                $row['extra_block_button_datelock'] = $tplReport->getContent($row, 'EXTRA_BLOCK_BUTTON_DATELOCK');
            } else {
                $row['extra_block_button_datelock'] = $tplReport->getContent($row, 'EXTRA_BLOCK_BUTTON_DATEUNLOCK');
            }

            $html = $tplReport->getContent($row, 'EXTRA_BLOCK_POPUP_REPORTSALEONEPRODUCT');

            return Response::json($html);

        } catch (\Throwable $e) {
            error_log('[ProductSalesHistoryController@popup] ' . $e->getMessage());
            Notifier::Add('Falha ao montar histórico de vendas.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    public function search(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $dateStart = (string)($_POST['dataini'] ?? '');
        $dateEnd   = (string)($_POST['datafim'] ?? '');
        $interval  = filter_var($_POST['intervalo'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $productRef = (string)($_POST['produto'] ?? '');

        if ($productRef === '' || $dateStart === '') {
            Notifier::Add('Parâmetros inválidos para o relatório.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $product = new LegacyProduct();

            if (is_numeric($productRef)) {
                $product->SearchByCode($productRef);
            } else {
                $product->SearchByString($productRef);
            }

            if (!($row = $product->getResult())) {
                Notifier::Add('Produto não localizado!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $productId   = (int)$row['id_produto'];
            $productName = $row['produto'];
            $unit        = $row['produtounidade'];
            $productType = (int)$row['id_produtotipo'];

            $chartData = [];
            $total     = 0.0;

            if ($interval) {
            if ($dateEnd === '') {
                $dateEnd = $dateStart;
            }

            $period = new DatePeriod(
                new DateTime($dateStart),
                new DateInterval('P1D'),
                (new DateTime($dateEnd))->modify('+1 day')
            );

            $dates  = [];
            $labels = [];

            foreach ($period as $value) {
                $label          = $value->format('d/m/Y');
                $dates[$label]  = 0;
                $labels[]       = $label;
            }

            [$dataset, $datasetTotal] = $this->searchByDateInterval($productId, $productName, $unit, $dateStart, $dateEnd, $dates);
            $labelsInterval = array_keys($dates);
            $chartData[] = [
                'labels'   => $labelsInterval,
                'datasets' => [$dataset],
            ];
            $total = Calc::Sum([$total, $datasetTotal]);

            if ($productType === LegacyProduct::PRODUTO_TIPO_NORMAL) {
                $this->appendCompositionAndKitInterval($chartData, $total, $productId, $dateStart, $dateEnd, $dates, $labelsInterval);
            }

            $filter = sprintf('%s a %s', $this->formatDate($dateStart), $this->formatDate($dateEnd));
        } else {
            $period = new DatePeriod(
                new DateTime($dateStart),
                new DateInterval('PT1H'),
                (new DateTime($dateStart))->modify('+1 day')
            );

            $hours  = [];
            $labels = [];

            foreach ($period as $value) {
                $label         = $value->format('H');
                $hours[$label] = 0;
                $labels[]      = $label;
            }

            [$dataset, $datasetTotal] = $this->searchByHours($productId, $productName, $unit, $dateStart, $hours);
            $chartData[] = [
                'labels'   => $labels,
                'datasets' => [$dataset],
            ];
            $total = Calc::Sum([$total, $datasetTotal]);

            if ($productType === LegacyProduct::PRODUTO_TIPO_NORMAL) {
                $this->appendCompositionAndKitHours($chartData, $total, $productId, $dateStart, $hours, $labels);
            }

            $filter = $this->formatDate($dateStart);
        }

            return Response::json([
                'filter' => $filter,
                'chart'  => $chartData,
                'total'  => number_format($total, 3, ',', '.'),
            ]);

        } catch (\Throwable $e) {
            error_log('[ProductSalesHistoryController@search] ' . $e->getMessage());
            Notifier::Add('Falha ao gerar histórico de vendas.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    public function popupData(): string
    {
        if (!(new AuthService())->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $productRef = (string)($_POST['produto'] ?? '');

        if ($productRef === '') {
            Notifier::Add('Produto inválido.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }

        try {
            $product = new LegacyProduct();

            if (is_numeric($productRef)) {
                $product->SearchByCode($productRef);
            } else {
                $product->SearchByString($productRef);
            }

            if (!($row = $product->getResult())) {
                Notifier::Add('Erro ao carregar dados do produto!', Notifier::NOTIFIER_ERROR);
                return Response::json(null);
            }

            $row        = LegacyProduct::FormatFields($row);
            $tplReport  = new View('report_sale_one_product');

            $html = $tplReport->getContent($row, 'EXTRA_BLOCK_REPORTSALEONEPRODUCT_CONTAINER');

            return Response::json($html);

        } catch (\Throwable $e) {
            error_log('[ProductSalesHistoryController@popupData] ' . $e->getMessage());
            Notifier::Add('Falha ao montar detalhes do produto.', Notifier::NOTIFIER_ERROR);
            return Response::json(null);
        }
    }

    private function searchByHours(int $productId, string $productName, string $unit, string $dateStart, array $hours): array
    {
        $saleItem = new SaleOrderItem();
        $saleItem->ReportItemByDate($productId, $dateStart);

        $total = 0.0;

        foreach ($saleItem->getResults() as $row) {
            $hour             = str_pad((string)$row['hora'], 2, '0', STR_PAD_LEFT);
            $hours[$hour]     = $row['qtd'];
            $total            = Calc::Sum([$total, $row['qtd']]);
        }

        $datasetData = array_values($hours);

        return [[
            'label' => sprintf('%s [ %s %s ]', $productName, number_format($total, 3, ',', '.'), $unit),
            'data'  => $datasetData,
        ], $total];
    }

    private function searchByDateInterval(int $productId, string $productName, string $unit, string $dateStart, string $dateEnd, array $dates): array
    {
        $saleItem = new SaleOrderItem();
        $saleItem->ReportItemByDateInterval($productId, $dateStart, $dateEnd);

        $total = 0.0;

        foreach ($saleItem->getResults() as $row) {
            $key            = $this->formatDate(sprintf('%04d-%02d-%02d', $row['year'], $row['month'], $row['day']));
            $dates[$key]    = $row['qtd'];
            $total          = Calc::Sum([$total, $row['qtd']]);
        }

        $datasetData = array_values($dates);

        return [[
            'label' => sprintf('%s [ %s %s ]', $productName, number_format($total, 3, ',', '.'), $unit),
            'data'  => $datasetData,
        ], $total];
    }

    private function appendCompositionAndKitInterval(array &$chartData, float &$total, int $productId, string $dateStart, string $dateEnd, array $datesTemplate, array $labels): void
    {
        $composition = new ProductComposition();
        $composition->having($productId);

        foreach ($composition->getResults() as $row) {
            [$dataset, $datasetTotal] = $this->searchByDateInterval(
                (int)$row['id_composicao'],
                $row['produto'],
                $row['produtounidade'],
                $dateStart,
                $dateEnd,
                $datesTemplate
            );

            $chartData[] = [
                'labels'   => $labels,
                'datasets' => [$dataset],
            ];

            $total = Calc::Sum([$total, Calc::Mult($datasetTotal, $row['qtd'])]);
        }

        $kit = new ProductKit();
        $kit->having($productId);

        foreach ($kit->getResults() as $row) {
            [$dataset, $datasetTotal] = $this->searchByDateInterval(
                (int)$row['id_kit'],
                $row['produto'],
                $row['produtounidade'],
                $dateStart,
                $dateEnd,
                $datesTemplate
            );

            $chartData[] = [
                'labels'   => $labels,
                'datasets' => [$dataset],
            ];

            $total = Calc::Sum([$total, Calc::Mult($datasetTotal, $row['qtd'])]);
        }
    }

    private function appendCompositionAndKitHours(array &$chartData, float &$total, int $productId, string $dateStart, array $hoursTemplate, array $labels): void
    {
        $composition = new ProductComposition();
        $composition->having($productId);

        foreach ($composition->getResults() as $row) {
            [$dataset, $datasetTotal] = $this->searchByHours(
                (int)$row['id_composicao'],
                $row['produto'],
                $row['produtounidade'],
                $dateStart,
                $hoursTemplate
            );

            $chartData[] = [
                'labels'   => $labels,
                'datasets' => [$dataset],
            ];

            $total = Calc::Sum([$total, Calc::Mult($datasetTotal, $row['qtd'])]);
        }

        $kit = new ProductKit();
        $kit->having($productId);

        foreach ($kit->getResults() as $row) {
            [$dataset, $datasetTotal] = $this->searchByHours(
                (int)$row['id_kit'],
                $row['produto'],
                $row['produtounidade'],
                $dateStart,
                $hoursTemplate
            );

            $chartData[] = [
                'labels'   => $labels,
                'datasets' => [$dataset],
            ];

            $total = Calc::Sum([$total, Calc::Mult($datasetTotal, $row['qtd'])]);
        }
    }

    private function formatDate(string $value): string
    {
        return date('d/m/Y', strtotime($value));
    }
}
