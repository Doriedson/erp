<?php

use database\ControlAccess;
use database\Entity;
use database\Notifier;
use App\View\View;
use database\Log;
use database\Product;
use database\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplStockupdate = new View('templates/report_stockupdate');

		$tplProduct = new View('templates/product');

		$data = [
			'data' => date('Y-m-d'),
			"block_product_autocomplete_search" => $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH")
		];

		Send($tplStockupdate->getContent($data, "BLOCK_PAGE"));

		break;

	case "report_stockupdate_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$byproduct = ($_POST['byproduct'] == "false")? false : true;
		$id_produto = $_POST['produto'];
		$stocktype = $_POST['stocktype'];

		$log = new Log();
		$product = new Product();

		if ($byproduct) {

			$product->SearchByCode($id_produto);

			if (!$product->getResult()) {

				Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			$id_produto = null;
		}

		if ($intervalo == false) {

			$datafim = $dataini;
		}

		$stock_primary = true;

		if ($stocktype == 1) {

			$stock_primary = false;


		// } else {

		// 	$log->getProductStockDateInterval($id_produto, $dataini, $datafim, false);
		}

		$log->getProductStockDateInterval($id_produto, $dataini, $datafim, $stock_primary);

		// } else {

		// 	$log->getProductStockDate($id_produto, $dataini);
		// }

		// } else {

		// 	if ($intervalo) {

		// 		$log->getStockDateInterval($dataini, $datafim);

		// 	} else {

		// 		$log->getStockDate($dataini);
		// 	}
		// }

		$tplStockin = new View('templates/report_stockupdate');

		if ($row = $log->getResult()) {

			$entity = new Entity();

			$tplStockupdate = new View('templates/report_stockupdate');

			$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
			$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

			if ($stocktype == 0) {

				$header = "Estoque Primário - ";

			} else {

				$header = "Estoque Secundário - ";
			}

			if ($intervalo) {

				$header .= "$dataini_formatted até $datafim_formatted";

			} else {

				$header .= "$dataini_formatted";
			}

			$saldo = 0;

			$data = [];

			do {

				$row_decoded = (array) json_decode($row['log']);

				$entity->Read($row_decoded['id_entidade']);

				if ($rowEntity = $entity->getResult()) {

					$row_decoded['colaborador'] = $rowEntity['nome'];
				}

				$subtotal = Calc::Mult($row_decoded["qtd"], $row_decoded['custoun'], 2);

				$row_decoded['qtd_formatted'] = number_format($row_decoded['qtd'], 3, ",", ".");
				$row_decoded['subtotal_formatted'] = number_format($subtotal, 2, ",", ".");
				$row_decoded['custoun_formatted'] = number_format($row_decoded['custoun'], 2, ",", ".");
				$row_decoded['data_formatted'] = date_format(date_create($row['data']),'d/m/Y H:i');
				// $row_decoded['produtounidade'] = $produtounidade;

				if (!array_key_exists($row_decoded['id_produto'], $data)) {
				// if (!$data[$row_decoded['id_produto']]) {

					$data[$row_decoded['id_produto']]['data'] = "";
					$data[$row_decoded['id_produto']]['qtd'] = 0;
					$data[$row_decoded['id_produto']]['total'] = 0;

					$product->Read($row_decoded['id_produto']);

					if($rowProduct = $product->getResult()) {

						$data[$row_decoded['id_produto']]['produto'] = $rowProduct['produto'];
						$data[$row_decoded['id_produto']]['produtounidade'] = $rowProduct['produtounidade'];
					}
				}

				if ($row_decoded['log'] == "produto_estoque_add" || $row_decoded['log'] == "produto_estoque_secundario_add") {

					$data[$row_decoded['id_produto']]['data'] .= $tplStockupdate->getContent($row_decoded, "EXTRA_BLOCK_STOCKUPDATE_TR_ADD");

					$data[$row_decoded['id_produto']]['qtd'] += $row_decoded['qtd'];
					$data[$row_decoded['id_produto']]['total'] += $subtotal;

				} else {

					$data[$row_decoded['id_produto']]['data'] .= $tplStockupdate->getContent($row_decoded, "EXTRA_BLOCK_STOCKUPDATE_TR_DEL");

					$data[$row_decoded['id_produto']]['qtd'] -= $row_decoded['qtd'];
					$data[$row_decoded['id_produto']]['total'] -= $subtotal;
				}

			} while ($row = $log->getResult());

			$data = array_sort($data, 'produto');

			$response = "";

			$total = 0;

			foreach($data as $key => $row) {

				$block = [
					"extra_block_stockupdate_tr" => $row['data'],
					'produto' => $row['produto'],
					// 'dateinterval' => $dateinterval,
					'produtounidade' => $row['produtounidade'],
				];

				$total = Calc::Sum([
					$total,
					$row['total']
				]);

				$data_block = [
					'saldo_formatted' => number_format($row['qtd'], 3, ",", "."),
					'total_formatted' => number_format($row['total'], 2, ",", "."),
					'produtounidade' => $row['produtounidade']
				];

				if ($row['qtd'] > 0) {

					$color = "color-green";
					$block['extra_block_stockupdate_total'] = $tplStockupdate->getContent($data_block, "EXTRA_BLOCK_STOCKUPDATE_TOTALPLUS");

				} else if ($row['qtd'] < 0) {

					$color = "color-red";
					$block['extra_block_stockupdate_total'] = $tplStockupdate->getContent($data_block, "EXTRA_BLOCK_STOCKUPDATE_TOTALMINUS");

				} else {

					$color = "color-blue";
					$block['extra_block_stockupdate_total'] = $tplStockupdate->getContent($data_block, "EXTRA_BLOCK_STOCKUPDATE_TOTALZERO");

				}

				$response .= $tplStockupdate->getContent($block, "EXTRA_BLOCK_STOCKUPDATE_CONTENT");
			}

			if ($total > 0) {

				$color = "color-green";

			} else if ($total < 0) {

				$color = "color-red";

			} else {

				$color = "color-blue";
			}

			$data_header = [
				"header" => $header,
				"total_formatted" => number_format($total, 2, ",", "."),
				"color" => $color
			];

			$response = $tplStockupdate->getContent($data_header, "EXTRA_BLOCK_STOCKUPDATE_HEADER") . $response;

			Send($response);

		} else {

			Notifier::Add("Nenhum ajuste de estoque encontrado!", Notifier::NOTIFIER_INFO);
			Send([]);
		}

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
		break;
}