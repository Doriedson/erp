<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\BillsToPay;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplBillspay = new View("report_billspay");

		$data = [
			'data' => date('Y-m-d'),
		];

		Send($tplBillspay->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_billspay_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$procura = $_POST['procura'];

		$tplBillspay = new View('report_billspay');
		$tplBillsToPay = new View('bills_to_pay');

		$bills = new BillsToPay();

		if ($intervalo) {

			$bills->SearchByDateInterval($dataini, $datafim, $procura, null, null, true);

		} else {

			$bills->SearchByDate($dataini, $procura, null, null, true);
		}

		if ($row = $bills->getResult()) {

			$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
			$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

			$type = [
				0 => "Cadastradas",
				1 => "Pagas",
				2 => "Vencimento",
			];

			if ($intervalo) {

				$content['header'] = "$type[$procura] entre $dataini_formatted e $datafim_formatted";

			} else {

				$content['header'] = "$type[$procura] em $dataini_formatted";
			}

			$line = "";
			$setor='';
			$subtotal = 0;
			$total = 0;
			$extra_block_setor = "";

			do {

				if ($row['datapago']) {
					if ($setor != $row['contasapagarsetor']) {

						if ($setor != '') {

							$data['subtotal_formatted'] = number_format($subtotal, 2, ",", ".");

							// $line .= $tplBillspay->getContent($data, "EXTRA_BLOCK_TR_SUBTOTAL");

							$data['contasapagarsetor'] = $setor;
							$data['id_contasapagarsetor'] = $id_setor;
							$data['extra_block_tr'] = $line;

							$extra_block_setor .= $tplBillspay->getContent($data, "EXTRA_BLOCK_SETOR");
						}

						$setor = $row['contasapagarsetor'];
						$id_setor = $row['id_contasapagarsetor'];
						$line = "";
						$total += $subtotal;
						$subtotal = 0;
					}



					$subtotal += $row['valorpago'];

					$row = BillsToPay::FormatFields($row);

					// $line .= $tplBillspay->getContent($row, "EXTRA_BLOCK_TR");

					// if ($row['datapago'] != null) {

						$line .= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

					// } else {

					// 	if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

					// 		$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

					// 	} else {

					// 		$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
					// 	}
					// }
				}

			} while ($row = $bills->getResult());

			$data['subtotal_formatted'] = number_format($subtotal, 2, ",", ".");
			// $line .= $tplBillspay->getContent($data, "EXTRA_BLOCK_TR_SUBTOTAL");

			$data['contasapagarsetor'] = $setor;
			$data['id_contasapagarsetor'] = $id_setor;
			$data['extra_block_tr'] = $line;

			$extra_block_setor .= $tplBillspay->getContent($data, "EXTRA_BLOCK_SETOR");

			$total += $subtotal;

			// $content['total_formatted'] = number_format($total, 2, ",", ".");

			$content['extra_block_setor'] = $extra_block_setor;

			Send([
				"data" => $tplBillspay->getContent($content, "EXTRA_BLOCK_CONTENT"),
				"total" => number_format($total, 2, ",", ".")
			]);

		} else {

			Notifier::Add("Nenhum relatório encontrado para data informada!", Notifier::NOTIFIER_INFO);
			Send([
				"data" => $tplBillspay->getContent([], "EXTRA_BLOCK_REPORT_BILLSPAY_NOT_FOUND"),
				"total"	 => "0,00"
			]);
		}

	break;
}