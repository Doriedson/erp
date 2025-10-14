<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\CashChange;
use database\Cashier;
use database\CashAdd;
use database\CashDrain;
use database\SaleOrder;
use database\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

function ReportCashChangeFormEdit($block, $message_error) {

	$id_caixa = $_POST['id_caixa'];

	$tplPdv = new View('templates/report_sale_total');

	$cashChange = new CashChange();
	$cashChange->Read($id_caixa);

	if ($row = $cashChange->getResult()) {

		$row = CashChange::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ReportCashChangeFormCancel($block, $message_error) {

	$id_caixa = $_POST['id_caixa'];

	$tplPdv = new View('templates/report_sale_total');

	$cashChange = new CashChange();
	$cashChange->Read($id_caixa);

	if ($row = $cashChange->getResult()) {

		$row = CashChange::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ReportCashChangeFormSave($field, $block, $message_error) {

	$id_caixa = $_POST['id_caixa'];
	$value = $_POST['value'];

	$cash = [
		"moeda_1" => 0.01,
		"moeda_5" => 0.05,
		"moeda_10" => 0.10,
		"moeda_25" => 0.25,
		"moeda_50" => 0.50,
		"cedula_1" => 1,
		"cedula_2" => 2,
		"cedula_5" => 5,
		"cedula_10" => 10,
		"cedula_20" => 20,
		"cedula_50" => 50,
		"cedula_100" => 100,
		"cedula_200" => 200,
	];

	if (Calc::Mod($value, $cash[$field]) > 0) {

		Notifier::Add("Valor deve ser múltiplo de R$ " . number_format($cash[$field], 2, ",", "."), Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$data = [
		'id_caixa' => (int) $id_caixa,
		'field' => $field,
		'value' => $value,
	];

	$cashChange = new CashChange();

	$cashChange->Update($data);

	$tplPdv = new View('templates/report_sale_total');

	$sale = new SaleOrder();

	$sale->SearchTotalSaleByPDV($id_caixa);

	$report = "";

	if ($rowSale = $sale->getResult()) {

		$report = getSaleTotalReport($sale, $rowSale, true, false, $rowSale['dataini'], null);
	}

	$cashChange->Read($id_caixa);

	if ($row = $cashChange->getResult()) {

		$row = CashChange::FormatFields($row);

		$data = [
			"data" => $tplPdv->getContent($row, $block),
			"report" => $report,
			"trocofim_formatted" => $row['troco_total_formatted']
		];

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ReportPDVFormEdit($block, $message_error) {

	$id_caixa = $_POST['id_caixa'];

	$tplPdv = new View('templates/report_sale_total');

	$pdv = new Cashier();
	$pdv->Read($id_caixa);

	if ($row = $pdv->getResult()) {

		$row = Cashier::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ReportPDVFormCancel($block, $message_error) {

	$id_caixa = $_POST['id_caixa'];

	$tplPdv = new View('templates/report_sale_total');

	$pdv = new Cashier();
	$pdv->Read($id_caixa);

	if ($row = $pdv->getResult()) {

		$row = Cashier::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ReportPDVFormSave($field, $block, $message_error) {

	$id_caixa = $_POST['id_caixa'];
	$value = $_POST['value'];

	$pdv = new Cashier();
	$troco = new CashChange();

	if ($field == "trocofim") {

		$cedula_200 = Calc::Mult(intdiv(floor($value), 200), 200, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_200
		]);

		$cedula_100 = Calc::Mult(intdiv(floor($value), 100), 100, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_100
		]);

		$cedula_50 = Calc::Mult(intdiv(floor($value), 50), 50, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_50
		]);

		$cedula_20 = Calc::Mult(intdiv(floor($value), 20), 20, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_20
		]);

		$cedula_10 = Calc::Mult(intdiv(floor($value), 10), 10, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_10
		]);

		$cedula_5 = Calc::Mult(intdiv(floor($value), 5), 5, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_5
		]);

		$cedula_2 = Calc::Mult(intdiv(floor($value), 2), 2, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_2
		]);

		$cedula_1 = Calc::Mult(intdiv(floor($value), 1), 1, 0);
		$value = Calc::Sum([
			$value,
			-$cedula_1
		]);

		$moeda_50 = Calc::Mult(intdiv(Calc::Mult($value, 100, 0), 50), 0.50, 2);
		$value = Calc::Sum([
			$value,
			-$moeda_50
		]);

		$moeda_25 = Calc::Mult(intdiv(Calc::Mult($value, 100, 0), 25), 0.25, 2);
		$value = Calc::Sum([
			$value,
			-$moeda_25
		]);

		$moeda_10 = Calc::Mult(intdiv(Calc::Mult($value, 100, 0), 10), 0.10, 2);
		$value = Calc::Sum([
			$value,
			-$moeda_10
		]);

		$moeda_5 = Calc::Mult(intdiv(Calc::Mult($value, 100, 0), 5), 0.05, 2);
		$value = Calc::Sum([
			$value,
			-$moeda_5
		]);

		$moeda_1 = Calc::Mult(intdiv(Calc::Mult($value, 100, 0), 1), 0.01, 2);
		$value = Calc::Sum([
			$value,
			-$moeda_1
		]);

		$troco->UpdateAll($id_caixa, $moeda_1, $moeda_5, $moeda_10, $moeda_25, $moeda_50, $cedula_1, $cedula_2, $cedula_5, $cedula_10, $cedula_20, $cedula_50, $cedula_100, $cedula_200);

	} elseif ($field == "trocoini") {

		$pdv->UpdateTrocoIni($id_caixa, $value);

	}

	$tplPdv = new View('templates/report_sale_total');

	$sale = new SaleOrder();

	$sale->SearchTotalSaleByPDV($id_caixa);

	$report = "";

	if ($rowSale = $sale->getResult()) {

		$report = getSaleTotalReport($sale, $rowSale, true, false, $rowSale['dataini'], null);
	}

	$pdv->Read($id_caixa);

	if ($row = $pdv->getResult()) {

		$row = Cashier::FormatFields($row);

		Send([
			"data" => $tplPdv->getContent($row, $block),
			"report" => $report
		]);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function getCashChange($id_caixa) {

	$extra_block_troco = "";
	$nome = "";

	$cashChange = new CashChange();

	$cashChange->Read($id_caixa);

	$tplSalesTotal = new View("templates/report_sale_total");

	if ($row = $cashChange->getResult()) {

		$row = CashChange::FormatFields($row);

		$extra_block_troco = $tplSalesTotal->getContent($row, "EXTRA_BLOCK_TROCO");
	}

	$cashier = new Cashier();

	$cashier->getOperator($id_caixa);

	if ($row = $cashier->getResult()) {

		$nome = $row['nome'];
	}

	$data = [
		"extra_block_troco" => $extra_block_troco,
		"nome" => $nome
	];

	return $tplSalesTotal->getContent($data, "EXTRA_BLOCK_POPUP_REPORTSALE_CASHCHANGE");
}

function getSaleTotalReport(SaleOrder $sale, $row, $pdv, $intervalo, $dataini, $datafim) {

	$tplSalesTotal = new View("templates/report_sale_total");

	$saleDiscount = new SaleOrder();
	$saleCashBreak = new SaleOrder();
	$cashDrain = new CashDrain();
	$cashAdd = new CashAdd();

	$total = 0;
	$quebra = 0;
	$credito_cliente = 0;

	$id_caixa = $pdv? $row['id_caixa']: 0;
	$report = "";
	$title = "";
	$pdv_opened = false;

	$data = [
		'extra_block_especie' => ""
	];

	do {

		$total += $row['valor'];

		$row['valor_formatted'] = number_format($row['valor'],2,',','.');

		$data['extra_block_especie'] .= $tplSalesTotal->getContent($row, "EXTRA_BLOCK_ESPECIE");

		if ($row['id_especie'] == 2) { // 2 => crédito cliente

			$credito_cliente = $row['valor'];
		}

		$data["extra_block_pdvreport_obs"] = "";

		if ($pdv == true) {

			$row = Cashier::FormatFields($row);

			// $data["obs"] = $row["obs"];
			// $data["tooltip"] = $row["tooltip"];
			// $data["icon_tooltip"] = $row["icon_tooltip"];
			$row["bt_pdvreport_closeview"] = "";

			if (is_null($row['datafim'])) {

				$row["bt_pdvreport_closeview"] = "hidden";
			}

			$data["extra_block_pdvreport_obs"] = $tplSalesTotal->getContent($row, "EXTRA_BLOCK_PDVREPORT_OBS");

			$trocoini = $row['trocoini'];
			$trocofim = $row['trocofim'];
			// $trocoini_formatted = number_format($row['trocoini'], 2, ",", ".");
			// $trocofim_formatted = number_format($row['trocofim'], 2, ",", ".");
			$trocoini_formatted = $row['trocoini_formatted'];
			$trocofim_formatted = $row['trocofim_formatted'];

			$dataini_formatted = date_format( date_create($row['dataini']), 'd/m/Y H:i');

			if ($row['datafim']) {

				$pdv_opened = false;
				$datafim_formatted = date_format( date_create($row['datafim']), 'd/m/Y H:i');

			} else {

				$pdv_opened = true;
				$datafim_formatted = " - PDV aberto";
			}

			$nome = $row['nome'];
		}

		$row = $sale->getResult();

		if (!$row || ($pdv && $id_caixa != $row['id_caixa']) ) {

			$data['extra_block_troco_container'] = "";
			$data['id_caixa'] = $id_caixa;

			if ($pdv) {

				$saleDiscount->getTotalDiscountByPDV($id_caixa);

				$title = [
					'id_caixa' => $id_caixa,
					'nome' => $nome,
					'dataini_formatted' => $dataini_formatted,
					'datafim_formatted' => $datafim_formatted
				];

				$data['extra_block_title'] = $tplSalesTotal->getContent($title, "EXTRA_BLOCK_TITLE_PDV");

				$result = [
					'id_caixa' => $id_caixa,
					'trocoini_formatted' => $trocoini_formatted,
					'trocofim_formatted' => $trocofim_formatted,
				];

				if ($pdv_opened == true) {

					$result['extra_block_trocofim'] = $tplSalesTotal->getContent([], "EXTRA_BLOCK_TROCOFIM_PDVABERTO");

				} else {

					$result['extra_block_trocofim'] = $tplSalesTotal->getContent($result, "EXTRA_BLOCK_TROCOFIM");
				}

				$data['extra_block_troco_container'] = $tplSalesTotal->getContent($result, 'EXTRA_BLOCK_TROCO_CONTAINER');

				$saleCashBreak->getTotalPaymentsByCashier($id_caixa);

				if ($rowCashBreak = $saleCashBreak->getResult()) {

					$cashDrain->getTotalByCashier($id_caixa);

					$quebra = Calc::Sum([
						$rowCashBreak['total'],
						- $trocofim,
						$trocoini
					]);

					if ($rowCashDrain = $cashDrain->getResult()) {

						$quebra = Calc::Sum([
							$quebra,
							- $rowCashDrain['total']
						]);
					}

					$cashAdd->getTotalByCashier($id_caixa);

					if ($rowCashAdd = $cashAdd->getResult()) {

						$quebra = Calc::Sum([
							$quebra,
							$rowCashAdd['total']
						]);
					}
				}

			} else {

				if ($intervalo) {

					$saleDiscount->getTotalDiscountByDateInterval($dataini, $datafim);

					$title = [
						'dataini_formatted' => date_format( date_create($dataini), 'd/m/Y'),
						'datafim_formatted' => date_format( date_create($datafim), 'd/m/Y'),
					];

					$data['extra_block_title'] = $tplSalesTotal->getContent($title, "EXTRA_BLOCK_TITLE_INTERVAL");

				} else {

					$saleDiscount->getTotalDiscountByDateInterval($dataini, $dataini);

					$title = [
						'dataini_formatted' => date_format( date_create($dataini), 'd/m/Y'),
					];

					$data['extra_block_title'] = $tplSalesTotal->getContent($title, "EXTRA_BLOCK_TITLE");
				}
			}

			$rowDiscount = $saleDiscount->getResult();

			$desconto = $rowDiscount['desconto'];

			$rowDiscount['especie'] = "Desconto";
			$rowDiscount['valor_formatted'] = number_format($desconto,2,',','.');

			$data['extra_block_especie'] .= $tplSalesTotal->getContent($rowDiscount, "EXTRA_BLOCK_ESPECIE");

			$total_formatted = number_format($total,2,',','.');

			$data['extra_block_especie'] .= $tplSalesTotal->getContent(["total_formatted" => $total_formatted], "EXTRA_BLOCK_TOTAL");

			if ($pdv == true) {

				$quebra = Calc::Sum([
					$quebra,
					-$credito_cliente
				]);

				if ($quebra < 0) {

					$data['extra_block_especie'] .= $tplSalesTotal->getContent(['quebra_formatted' => number_format(-$quebra, 2, ",", ".")], "EXTRA_BLOCK_CASHBREAKPOSITIVE");

				} else {

					$data['extra_block_especie'] .= $tplSalesTotal->getContent(['quebra_formatted' => number_format($quebra, 2, ",", ".")], "EXTRA_BLOCK_CASHBREAKNEGATIVE");
				}
			}

			$report .= $tplSalesTotal->getContent($data, "EXTRA_BLOCK_REPORT");

			$data = [
				'extra_block_especie' => ""
			];

			$total = 0;
			$credito_cliente = 0;

			if ($row) {

				$id_caixa = $row['id_caixa'];
			}
		}

	} while ($row);

	return $report;
}

switch ($_POST['action']) {

	case "load":

		$tplSalesTotal = new View("templates/report_sale_total");

		$data = ['data' => date('Y-m-d')];

		Send($tplSalesTotal->getContent($data, "BLOCK_PAGE"));
		break;

	case "report_sale_total_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$pdv = false;

		if ($intervalo == false) {

			$pdv = ($_POST['pdv'] == "false")? false : true;
		}

		$sale = new SaleOrder();

		if ($intervalo) {

			$sale->SearchTotalSaleByDateInterval($dataini, $datafim, false);

		} else {

			$sale->SearchTotalSaleByDate($dataini, $pdv);
		}

		if ($row = $sale->getResult()) {

			$report = getSaleTotalReport($sale, $row, $pdv, $intervalo, $dataini, $datafim);

			Send($report);

		} else {

			Notifier::Add("Nenhum relatório encontrado para data especificada.", Notifier::NOTIFIER_INFO);
			Send(null);
		}


		break;

	case "reportpdv_trocoini_edit":

		ReportPDVFormEdit("EXTRA_BLOCK_TROCOINI_FORM", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_trocoini_cancel":

		ReportPDVFormCancel("BLOCK_TROCOINI", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_trocoini_save":

		ReportPDVFormSave('trocoini', "BLOCK_TROCOINI", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_trocofim_edit":

		ReportPDVFormEdit("EXTRA_BLOCK_TROCOFIM_FORM", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_trocofim_cancel":

		ReportPDVFormCancel("EXTRA_BLOCK_TROCOFIM", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_trocofim_save":

		ReportPDVFormSave('trocofim', "EXTRA_BLOCK_TROCOFIM", "Erro ao carregar informações do troco!");

		break;

	case "reportpdv_moeda_1_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_MOEDA_1_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_1_cancel":

		ReportCashChangeFormCancel("BLOCK_MOEDA_1", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_1_save":

		ReportCashChangeFormSave('moeda_1', "BLOCK_MOEDA_1", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_5_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_MOEDA_5_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_5_cancel":

		ReportCashChangeFormCancel("BLOCK_MOEDA_5", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_5_save":

		ReportCashChangeFormSave('moeda_5', "BLOCK_MOEDA_5", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_10_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_MOEDA_10_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_10_cancel":

		ReportCashChangeFormCancel("BLOCK_MOEDA_10", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_10_save":

		ReportCashChangeFormSave('moeda_10', "BLOCK_MOEDA_10", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_25_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_MOEDA_25_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_25_cancel":

		ReportCashChangeFormCancel("BLOCK_MOEDA_25", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_25_save":

		ReportCashChangeFormSave('moeda_25', "BLOCK_MOEDA_25", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_50_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_MOEDA_50_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_50_cancel":

		ReportCashChangeFormCancel("BLOCK_MOEDA_50", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_moeda_50_save":

		ReportCashChangeFormSave('moeda_50', "BLOCK_MOEDA_50", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_1_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_1_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_1_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_1", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_1_save":

		ReportCashChangeFormSave('cedula_1', "BLOCK_CEDULA_1", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_2_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_2_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_2_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_2", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_2_save":

		ReportCashChangeFormSave('cedula_2', "BLOCK_CEDULA_2", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_5_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_5_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_5_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_5", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_5_save":

		ReportCashChangeFormSave('cedula_5', "BLOCK_CEDULA_5", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_10_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_10_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_10_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_10", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_10_save":

		ReportCashChangeFormSave('cedula_10', "BLOCK_CEDULA_10", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_20_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_20_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_20_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_20", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_20_save":

		ReportCashChangeFormSave('cedula_20', "BLOCK_CEDULA_20", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_50_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_50_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_50_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_50", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_50_save":

		ReportCashChangeFormSave('cedula_50', "BLOCK_CEDULA_50", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_100_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_100_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_100_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_100", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_100_save":

		ReportCashChangeFormSave('cedula_100', "BLOCK_CEDULA_100", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_200_edit":

		ReportCashChangeFormEdit("EXTRA_BLOCK_CEDULA_200_FORM", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_200_cancel":

		ReportCashChangeFormCancel("BLOCK_CEDULA_200", "Erro ao carregar informações do troco!");
	break;

	case "reportpdv_cedula_200_save":

		ReportCashChangeFormSave('cedula_200', "BLOCK_CEDULA_200", "Erro ao carregar informações do troco!");
	break;

	case "report_sale_total_gettrocofim":

		$id_caixa = $_POST['id_caixa'];

		// $tplSalesTotal = new View("templates/report_sale_total");

		// $result = [
		// 	'id_caixa' => $id_caixa,
		// 	'extra_block_troco' => getCashChange($id_caixa),
		// 	'trocoini_formatted' => $trocoini_formatted,
		// 	'trocofim_formatted' => $trocofim_formatted,
		// ];

		// $data['extra_block_troco_container'] = $tplSalesTotal->getContent($result, 'EXTRA_BLOCK_TROCO_CONTAINER');

		Send(getCashChange($id_caixa));

		break;

	case "pdvreport_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

		$id_caixa = $_POST['id_caixa'];
		$obs = $_POST['obs'];

		$pdv = new Cashier();

		$pdv->UpdateObs($id_caixa, $obs);

		$pdv->Read($id_caixa);

		if ($row = $pdv->getResult()) {

			$row = Cashier::FormatFields($row);

			$tplPdvReport = new View("templates/report_sale_total");

			Send($tplPdvReport->getContent($row, "EXTRA_BLOCK_PDVREPORT_OBS"));

		} else {

			Notifier::Add("Erro ao carregar observação do PDV!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
		break;
}