<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\SaleOrder;
use App\Legacy\Product;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplSaleProduct = new View('report_sale_product');

		$data = ['data' => date('Y-m-d')];

		Send($tplSaleProduct->getContent($data, "BLOCK_PAGE"));

	break;

	case "report_sale_product_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;

		$tplSaleProduct = new View("report_sale_product");

		$sale = new SaleOrder();
		$saleDiscount = new SaleOrder();

		if ($intervalo) {

			$sale->getSaleProductByDateInterval($dataini . " 00:00:00", $datafim . " 23:59:59");

		} else {

			$sale->getSaleProductByDate($dataini);
		}

		if ($row = $sale->getResult()) {

			$dates = [
				"dataini_formatted" => date_format(date_create($dataini), 'd/m/Y'),
				"datafim_formatted" => date_format(date_create($datafim), 'd/m/Y'),
			];

			$subtotal = 0;
			$total = 0;
			$setor = "";
			$extra_block_produto = "";
			$extra_block_setor_grupo = "";

			do {

				if ($setor != $row['produtosetor']) {

					if ($setor != "") {

						$content['subtotal_formatted'] = number_format($subtotal, 2, ",", ".");

						$content['produtosetor'] = $setor;

						// $extra_block_produto.= $tplSaleProduct->getContent($content, "EXTRA_BLOCK_SUBTOTAL");
						$content['extra_block_produto'] = $extra_block_produto;

						$extra_block_setor_grupo.= $tplSaleProduct->getContent($content, "EXTRA_BLOCK_SETOR_GRUPO");

						$extra_block_produto = "";
					}

					$setor = $row['produtosetor'];

					$total+= $subtotal;
					$subtotal = 0;
				}

				$subtotal+= $row['subtotal'];
				$row['valor_medio_formatted'] = number_format(round($row['subtotal'] / $row['qtd'], 2), 2, ",", ".");
				$row['subtotal_formatted'] = number_format($row['subtotal'], 2, ",", ".");
				$row['qtd_formatted'] = number_format($row['qtd'], 3, ",", ".");

				$row = Product::FormatFields($row);

				$extra_block_produto.= $tplSaleProduct->getContent($row, "EXTRA_BLOCK_PRODUTO");

			} while ($row = $sale->getResult());

			$content['subtotal_formatted'] = number_format($subtotal, 2, ",", ".");

			// $extra_block_produto.= $tplSaleProduct->getContent($content, "EXTRA_BLOCK_SUBTOTAL");

			$content['produtosetor'] = $setor;

			$content['extra_block_produto'] = $extra_block_produto;

			$extra_block_setor_grupo.= $tplSaleProduct->getContent($content, "EXTRA_BLOCK_SETOR_GRUPO");

			$total+= $subtotal;
			$data['total_formatted'] = number_format($total, 2, ",", ".");

			if ($intervalo) {

				$data['extra_block_table_header'] = $tplSaleProduct->getContent($dates, "EXTRA_BLOCK_TABLE_HEADER_INTERVAL");

			} else {

				$data['extra_block_table_header'] = $tplSaleProduct->getContent($dates, "EXTRA_BLOCK_TABLE_HEADER");
			}

			$data['extra_block_setor_grupo'] = $extra_block_setor_grupo;

			$content = [
				'data' => $tplSaleProduct->getContent($data, "EXTRA_BLOCK_CONTAINER"),
				'total_formatted' => $data['total_formatted']
			];

			Send($content);

		} else {

			$content = [
				'data' => $tplSaleProduct->getContent([], "EXTRA_BLOCK_REPORTSALE_NOTFOUND"),
				'total_formatted' => "0,00"
			];

			Notifier::Add("Nenhum relatório encontrado para a data informada!", Notifier::NOTIFIER_INFO);
			Send($content);
		}
	break;
}