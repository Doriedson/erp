<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\PurchaseOrder;
use database\Product;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$data = ['data' => date('Y-m-d')];

		$tplStockin = new View('templates/report_stockin');

		Send($tplStockin->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_stockin_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		
		$purchase = new PurchaseOrder();

		if ($intervalo) {

			$purchase->SearchStockInByDateInterval($dataini, $datafim);

		} else {

			$purchase->SearchStockInByDate($dataini);
		}

		$tplStockin = new View('templates/report_stockin');

		if ($row = $purchase->getResult()) {

			$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
			$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

			if ($intervalo) {

				$header = "$dataini_formatted a $datafim_formatted";

			} else {

				$header = "$dataini_formatted";
			}
						
			$setor = "";
			$tr = "";
		 	$extra_block_produto = "";
			$extra_block_setor_grupo = "";

			$total = 0;
			$subtotal = 0;

			do {

				if ($setor != $row['produtosetor']) {

					if ($setor != "") {

						$data = ['subtotal_formatted' => number_format($subtotal, 2, ",", ".")];

						$data['produtosetor'] = $setor;

						$data['extra_block_produto'] = $extra_block_produto;

						$extra_block_setor_grupo.= $tplStockin->getContent($data, "EXTRA_BLOCK_SETOR_GRUPO");

						$extra_block_produto = "";

					}
					
					$setor = $row['produtosetor'];

					$total+= $subtotal;
					$subtotal = 0;
				}
	
				$subtotal+= $row['subtotal'];

				$row['subtotal_formatted'] = number_format($row['subtotal'], 2, ',', '.');

				$row['custo_formatted'] = number_format(round($row['subtotal'] / $row['qtd'], 2), 2, ',', '.');

				$row['qtd_formatted'] = number_format($row['qtd'], 3, ',', '.');

				// $row['data_formatted'] = date_format(date_create($row['data']), 'd/m/Y');

				$row = Product::FormatFields($row);

				$extra_block_produto.= $tplStockin->getContent($row, "EXTRA_BLOCK_PRODUTO");

			} while ($row = $purchase->getResult());


			$data = ['subtotal_formatted' => number_format($subtotal, 2, ",", ".")];

			$data['produtosetor'] = $setor;
			
			$data['extra_block_produto'] = $extra_block_produto;

			$extra_block_setor_grupo.= $tplStockin->getContent($data, "EXTRA_BLOCK_SETOR_GRUPO");

			$total+= $subtotal;
			$data['total_formatted'] = number_format($total, 2, ",", ".");

			$data['extra_block_setor_grupo'] = $extra_block_setor_grupo;

			$data['header'] = $header;

			$content = [
				'data' => $tplStockin->getContent($data, "EXTRA_BLOCK_CONTENT"),
				'total_formatted' => $data['total_formatted']
			];

			Send($content);

		} else {
			
			$content = [
				'data' => $tplStockin->getContent([], "EXTRA_BLOCK_REPORTSTOCKIN_NOTFOUND"),
				'total_formatted' => "0,00"
			];

			Notifier::Add("Nenhum relatório encontrado para a data informada!", Notifier::NOTIFIER_INFO);
			Send($content);
		}
	break;
}