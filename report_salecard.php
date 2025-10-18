<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\SaleOrder;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplSalecard = new View('templates/report_salecard');

		$data = ['data' => date('Y-m-d')];

		Send($tplSalecard->getContent($data, "BLOCK_PAGE"));

		break;

	case "report_salecard_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$procura = $_POST['procura'];

		$sale = new SaleOrder();

		$ratio = 0.6;

		$tplSalecard = new View('templates/report_salecard');

		$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
		$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

		if ($intervalo) {

			$content['header'] = "Vendas em cartão de $dataini_formatted até $datafim_formatted";

			$sale->getSaleProductByDateInterval($dataini . " 00:00:00", $datafim . " 23:59:59");

		} else {

			$content['header'] = "Vendas em cartão em $dataini_formatted";

			$sale->getSaleProductByDate($dataini);
		}

		if ($row = $sale->getResult()) {

			$line = "";
			$total = 0;

			do {

				$total += ($row['subtotal'] * $ratio);

				$row['qtd_formatted'] = number_format($row['qtd'] * $ratio, 3, ",", ".");
				$row['subtotal_formatted'] = number_format($row['subtotal'] * $ratio, 2, ",", ".");

				$line .= $tplSalecard->getContent($row, "EXTRA_BLOCK_TR");

			} while ($row = $sale->getResult());

			$content['extra_block_tr'] = $line;
			$content['total_formatted'] = number_format($total, 2, ",", ".");

			Send($tplSalecard->getContent($content, "EXTRA_BLOCK_CONTENT"));

		} else {

			Notifier::Add("Nenhum produto encontrado no período informado.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;
}