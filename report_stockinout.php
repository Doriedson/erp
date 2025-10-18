<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\SaleOrder;
use database\Product;
use database\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplStock = new View('templates/report_stockinout');

		$data = ['data' => date('Y-m-d')];

		Send($tplStock->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_stockinout_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;

		$sale = new SaleOrder();

		if ($intervalo) {

			$sale->SearchStockInOutByDateInterval($dataini, $datafim);

		} else {

			$sale->SearchStockInOutByDate($dataini);
		}

		$tplStockinout = new View('templates/report_stockinout');

		if ($row = $sale->getResult()) {

			$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
			$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

			if ($intervalo) {

				$header = "$dataini_formatted a $datafim_formatted";

			} else {

				$header = "$dataini_formatted";
			}

			$setor = "";
			$totalcusto = 0;
			$total = 0;
			$totalvenda = 0;
			$line = "";
			$extra_block_produto = "";
			$subtotalcusto = 0;
			$subtotalvenda = 0;
			$extra_block_setor_grupo = "";

			do {

				if ($setor != $row['produtosetor']) {

					if ($setor != "") {

						$data = [
							'subtotalcusto_formatted' => number_format($subtotalcusto, 2, ",", "."),
							'subtotalvenda_formatted' => number_format($subtotalvenda, 2, ",", "."),
							'subtotal_formatted' => number_format($subtotalvenda - $subtotalcusto, 2, ",", "."),
							'produtosetor' => $setor,
							'extra_block_produto' => $extra_block_produto,
						];

						$extra_block_setor_grupo.= $tplStockinout->getContent($data, "EXTRA_BLOCK_SETOR_GRUPO");

						$extra_block_produto = "";
					}

					$setor = $row['produtosetor'];

					$totalcusto+= $subtotalcusto;
					$totalvenda+= $subtotalvenda;
					$subtotalcusto = 0;
					$subtotalvenda = 0;
				}

				$subtotalcusto+= $row['compra_subtotal'];
				$subtotalvenda+= $row['venda_subtotal'];

				if ($row['compra_qtd'] == 0) {

					$row['venda_qtd_percent'] = "100";
					$custo_un = 0;
					// $row['custo_un_formatted'] = '0,00';

				} else {

					$row['venda_qtd_percent'] = number_format(($row['venda_qtd'] / $row['compra_qtd']) * 100, 0, ",", ".");
					$custo_un = $row['compra_subtotal'] / $row['compra_qtd'];
					// $row['custo_un_formatted'] = number_format($custo_un, 2, ",", ".");
				}

				$row['custo_un_formatted'] = number_format($custo_un, 2, ",", ".");

				if ($row['venda_qtd'] == 0) {

					$venda_un = 0;
					// $row['venda_un_formatted'] = '0,00';
					$row['venda_percent'] = "0";

				} else {

					$venda_un = $row['venda_subtotal'] / $row['venda_qtd'];
					// $row['venda_un_formatted'] = number_format($venda_un, 2, ",", ".");

					if ($row['compra_subtotal'] > 0) {

						$row['venda_percent'] = number_format(($row['venda_subtotal'] / $row['compra_subtotal']) * 100, 0, ",", ".");;

					} else {

						$row['venda_percent'] = "100";
					}
				}

				$row['venda_un_formatted'] = number_format($venda_un, 2, ",", ".");

				if ($custo_un == 0) {// || $venda_un == 0) {

					// $row['lucro_un_formatted'] = '';
					$row['lucro_un_percent'] = '100';

				} else {

					// $row['lucro_un_formatted'] = number_format($venda_un - $custo_un, 2, ",", ".");
					$row['lucro_un_percent'] = number_format((($venda_un / $custo_un) - 1) * 100, 0, ",", ".");
				}

				$row['lucro_un_formatted'] = number_format($venda_un - $custo_un, 2, ",", ".");

				if ($row['compra_subtotal'] == 0) {

					$row['lucro_total_formatted'] = number_format($row['venda_subtotal'], 2, ",", ".");
					$row['lucro_percent'] = "100";

				} else if ($row['venda_subtotal'] == 0) {

					$row['lucro_total_formatted'] = number_format(- $row['compra_subtotal'], 2, ",", ".");
					$row['lucro_percent'] = "-100";

				} else {

					$row['lucro_total_formatted'] = number_format($row['venda_subtotal'] - $row['compra_subtotal'], 2, ",", ".");
					$row['lucro_percent'] = number_format((($row['venda_subtotal'] / $row['compra_subtotal']) - 1) * 100, 0, ",", ".");
				}

				// if ($row['compra_subtotal'] == 0) {

				// 	$row['compra_subtotal_formatted'] = '0,00';

				// } else {

					$row['compra_subtotal_formatted'] = number_format($row['compra_subtotal'], 2, ",", ".");
				// }

				// if ($row['venda_subtotal'] == 0) {

				// 	$row['venda_subtotal_formatted'] = '0,00';

				// } else {

					$row['venda_subtotal_formatted'] = number_format($row['venda_subtotal'], 2, ",", ".");
				// }

				$row['compra_qtd_formatted'] = number_format($row['compra_qtd'], 3, ",", ".");
				$row['venda_qtd_formatted'] = number_format($row['venda_qtd'], 3, ",", ".");

				$row = Product::FormatFields($row);

				$extra_block_produto.= $tplStockinout->getContent($row, "EXTRA_BLOCK_PRODUTO");

				} while ($row = $sale->getResult());

			$totalcusto+= $subtotalcusto;
			$totalvenda+= $subtotalvenda;

			$data = [
				'subtotalcusto_formatted' => number_format($subtotalcusto, 2, ",", "."),
				'subtotalvenda_formatted' => number_format($subtotalvenda, 2, ",", "."),
				'subtotal_formatted' => number_format($subtotalvenda - $subtotalcusto, 2, ",", "."),
				'produtosetor' => $setor,
				'extra_block_produto' => $extra_block_produto,
			];

			$extra_block_setor_grupo.= $tplStockinout->getContent($data, "EXTRA_BLOCK_SETOR_GRUPO");

			$data = [
				'total_formatted' => number_format($total, 2, ",", "."),
				'extra_block_setor_grupo' => $extra_block_setor_grupo,
				'header' => $header
			];

			$totallucro = Calc::Sum([
				$totalvenda,
				-$totalcusto
			]);

			$totalvenda_percent_formatted = "";

			if ($totalcusto > 0) {

				$totalvenda_percent = Calc::Mult(Calc::Div($totalvenda, $totalcusto), 100, 2);

				$totalvenda_percent_formatted = number_format($totalvenda_percent, 2, ",", ".") . "%";
			}



			$content = [
				'data' => $tplStockinout->getContent($data, "EXTRA_BLOCK_CONTENT"),
				'totalcompra_formatted' => number_format($totalcusto, 2, ",", "."),
				'totalvenda_formatted' => number_format($totalvenda, 2, ",", "."),
				"totalvenda_percent_formatted" => $totalvenda_percent_formatted,
				'totallucro_formatted' => number_format($totallucro, 2, ",", "."),
			];

			Send($content);

		} else {

			$content = [
				'data' => $tplStockinout->getContent([], "EXTRA_BLOCK_REPORTSTOCKINOUT_NOTFOUND"),
				'totalcompra_formatted' => "0,00",
				'totalvenda_formatted' => "0,00",
				"totalvenda_percent_formatted" => "",
				'totallucro_formatted' => "0,00",
			];

			Notifier::Add("Nenhum relatório encontrado para a data informada!", Notifier::NOTIFIER_INFO);
			Send($content);
		}
	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}