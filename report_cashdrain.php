<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\CashDrain;
use database\CashAdd;
use database\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

function CashdrainFormEdit($block, $message_error) {

	$id_caixasangria = $_POST['id_caixasangria'];

	$tplCashdrain = new View('templates/report_cashdrain');

	$cashdrain = new CashDrain();
	$cashdrain->Read($id_caixasangria);

	if ($row = $cashdrain->getResult()) {

		$row = CashDrain::FormatFields($row);

		Send($tplCashdrain->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function CashdrainFormCancel($block, $message_error) {

	$id_caixasangria = $_POST['id_caixasangria'];

	$tplCashdrain = new View('templates/report_cashdrain');

	$cashdrain = new CashDrain();
	$cashdrain->Read($id_caixasangria);

	if ($row = $cashdrain->getResult()) {

		$row = CashDrain::FormatFields($row);

		Send($tplCashdrain->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function CashdrainFormSave($field, $block, $message_error) {

	$id_caixasangria = $_POST['id_caixasangria'];
	$value = $_POST['value'];

	$data = [
		'id_caixasangria' => (int) $id_caixasangria,
		'field' => $field,
		'value' => $value,
	];

	$cashdrain = new CashDrain();

	$cashdrain->Update($data);

	$tplCashdrain = new View('templates/report_cashdrain');

	$cashdrain->Read($id_caixasangria);

	if ($row = $cashdrain->getResult()) {

		$row = CashDrain::FormatFields($row);

		$data = [
			"data" => $tplCashdrain->getContent($row, $block),
			"cashdrain" => $tplCashdrain->getContent($row, "EXTRA_BLOCK_CASHDRAIN_TR")
		];

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function CashaddFormEdit($block, $message_error) {

	$id_caixareforco = $_POST['id_caixareforco'];

	$tplCashadd = new View('templates/report_cashdrain');

	$cashadd = new CashAdd();
	$cashadd->Read($id_caixareforco);

	if ($row = $cashadd->getResult()) {

		$row = CashAdd::FormatFields($row);

		Send($tplCashadd->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function CashaddFormCancel($block, $message_error) {

	$id_caixareforco = $_POST['id_caixareforco'];

	$tplCashadd = new View('templates/report_cashdrain');

	$cashadd = new CashAdd();
	$cashadd->Read($id_caixareforco);

	if ($row = $cashadd->getResult()) {

		$row = CashAdd::FormatFields($row);

		Send($tplCashadd->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function CashaddFormSave($field, $block, $message_error) {

	$id_caixareforco = $_POST['id_caixareforco'];
	$value = $_POST['value'];

	$data = [
		'id_caixareforco' => (int) $id_caixareforco,
		'field' => $field,
		'value' => $value,
	];

	$cashadd = new CashAdd();

	$cashadd->Update($data);

	$tplCashadd = new View('templates/report_cashdrain');

	$cashadd->Read($id_caixareforco);

	if ($row = $cashadd->getResult()) {

		$row = CashAdd::FormatFields($row);

		$data = [
			"data" => $tplCashadd->getContent($row, $block),
			"cashadd" => $tplCashadd->getContent($row, "EXTRA_BLOCK_CASHADD_TR")
		];

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$tplCashdrain = new View('templates/report_cashdrain');

		$data = [
			'data' => date('Y-m-d'),
		];

		Send($tplCashdrain->getContent($data, "BLOCK_PAGE"));

		break;

	case "report_cashdrain_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;

		$cashDrain = new CashDrain();

		if ($intervalo) {

			$cashDrain->SearchByDateInterval($dataini, $datafim);

		} else {

			$cashDrain->SearchByDate($dataini);
		}

		$tplCashdrain = new View('templates/report_cashdrain');

		$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
		$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

		if ($intervalo) {

			$header = "$dataini_formatted a $datafim_formatted";

		} else {

			$header = "$dataini_formatted";
		}

		$cash_drain = "";
		$cashdrain_total = 0;

		if ($row = $cashDrain->getResult()) {

			do {

				$row = CashDrain::FormatFields($row);

				$cashdrain_total = Calc::Sum([
					$cashdrain_total,
					$row['valor']
				]);

				$cash_drain .= $tplCashdrain->getContent($row, "EXTRA_BLOCK_CASHDRAIN_TR");

			} while ($row = $cashDrain->getResult());

		} else {

			$cash_drain .= $tplCashdrain->getContent([], "EXTRA_BLOCK_CASHDRAIN_TR_NONE");
		}

		$data = [
			"cashdrain_container_header" => $header,
			"extra_block_cashdrain_tr" => $cash_drain,
			"cashdrain_total_formatted" => number_format($cashdrain_total, 2, ',', '.'),
		];

		$cash_drain = $tplCashdrain->getContent($data, "EXTRA_BLOCK_CASHDRAIN_CONTENT");

		$cashAdd = new CashAdd();

		if ($intervalo) {

			$cashAdd->SearchByDateInterval($dataini, $datafim);

		} else {

			$cashAdd->SearchByDate($dataini);
		}

		$cash_add = "";
		$cashadd_total = 0;

		if ($row = $cashAdd->getResult()) {

			do {

				$row = CashAdd::FormatFields($row);

				$cashadd_total = Calc::Sum([
					$cashadd_total,
					$row['valor']
				]);

				$cash_add .= $tplCashdrain->getContent($row, "EXTRA_BLOCK_CASHADD_TR");

			} while ($row = $cashAdd->getResult());

		} else {

			$cash_add .= $tplCashdrain->getContent([], "EXTRA_BLOCK_CASHADD_TR_NONE");
		}

		if (empty($cash_drain) && empty($cash_add)) {

			Send(null);
		}

		$data = [
			"cashadd_container_header" => $header,
			"extra_block_cashadd_tr" => $cash_add,
			"cashadd_total_formatted" => number_format($cashadd_total, 2, ',', '.')
		];

		$cash_add = $tplCashdrain->getContent($data, "EXTRA_BLOCK_CASHADD_CONTENT");

		Send($cash_drain . $cash_add);

		break;

	case "cashdrain_obs_edit":

		CashdrainFormEdit("EXTRA_BLOCK_FORM_OBS", "Erro ao carregar observação da sangria!");
	break;

	case "cashdrain_obs_cancel":

		CashdrainFormCancel("BLOCK_OBS", "Erro ao carregar observação!");
	break;

	case "cashdrain_obs_save":

		CashdrainFormSave('obs', "BLOCK_OBS", "Erro ao salvar observação!");
	break;

	case "cashdrain_especie_edit":

		CashdrainFormEdit("EXTRA_BLOCK_FORM_ESPECIE", "Erro ao carregar espécie da sangria!");
	break;

	case "cashdrain_especie_cancel":

		CashdrainFormCancel("BLOCK_ESPECIE", "Erro ao carregar espécie");
	break;

	case "cashdrain_especie_save":

		CashdrainFormSave('id_especie', "BLOCK_ESPECIE", "Erro ao salvar espécie!");
	break;

	case "cashdrain_valor_edit":

		CashdrainFormEdit("EXTRA_BLOCK_FORM_VALOR", "Erro ao carregar valor da sangria!");
	break;

	case "cashdrain_valor_cancel":

		CashdrainFormCancel("BLOCK_VALOR", "Erro ao carregar valor da sangria!");
	break;

	case "cashdrain_valor_save":

		CashdrainFormSave('valor', "BLOCK_VALOR", "Erro ao salvar valor da sangria!");
	break;

	case "cashadd_valor_edit":

		CashaddFormEdit("EXTRA_BLOCK_FORM_CASHADD_VALOR", "Erro ao carregar valor do reforço!");
		break;

	case "cashadd_valor_cancel":

		CashaddFormCancel("BLOCK_CASHADD_VALOR", "Erro ao carregar valor do reforço!");
		break;

	case "cashadd_valor_save":

		CashaddFormSave('valor', "BLOCK_CASHADD_VALOR", "Erro ao salvar valor do reforço!");
		break;

	case "cashdrain_edit":

		$id_caixasangria = $_POST['id_caixasangria'];

		$cashDrain = new CashDrain();

		$cashDrain->Read($id_caixasangria);

		if ($row = $cashDrain->getResult()) {

			$row = CashDrain::FormatFields($row);

			$tplCashdrain = new View("templates/report_cashdrain");

			Send($tplCashdrain->getContent($row, "EXTRA_BLOCK_POPUP_CASHDRAIN"));

		} else {

			Notifier::Add("Erro ao carregar dados da sangria", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		break;

	case "cashadd_edit":

		$id_caixareforco = $_POST['id_caixareforco'];

		$cashAdd = new CashAdd();

		$cashAdd->Read($id_caixareforco);

		if ($row = $cashAdd->getResult()) {

			$row = CashAdd::FormatFields($row);

			$tplCashdrain = new View("templates/report_cashdrain");

			Send($tplCashdrain->getContent($row, "EXTRA_BLOCK_POPUP_CASHADD"));

		} else {

			Notifier::Add("Erro ao carregar dados do reforço", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "cashdrain_check":

		$id_caixasangria = $_POST["id_caixasangria"];

		$cashDrain = new CashDrain();

		$cashDrain->Check($id_caixasangria);

		$cashDrain->Read($id_caixasangria);

		if ($row = $cashDrain->getResult()) {

			$row = CashDrain::FormatFields($row);

			$tplCashdrain = new View("templates/report_cashdrain");

			Notifier::Add("Sangria conferida!", Notifier::NOTIFIER_DONE);
			Send($tplCashdrain->getContent($row, "EXTRA_BLOCK_CASHDRAIN_TR"));

		} else {

			Notifier::Add("Erro ao carregar dados do reforço", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "cashadd_check":

		$id_caixareforco = $_POST["id_caixareforco"];

		$cashAdd = new CashAdd();

		$cashAdd->Check($id_caixareforco);

		$cashAdd->Read($id_caixareforco);

		if ($row = $cashAdd->getResult()) {

			$row = CashAdd::FormatFields($row);

			$tplCashdrain = new View("templates/report_cashdrain");

			Notifier::Add("Reforço conferido!", Notifier::NOTIFIER_DONE);
			Send($tplCashdrain->getContent($row, "EXTRA_BLOCK_CASHADD_TR"));

		} else {

			Notifier::Add("Erro ao carregar dados do reforço", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
		break;
}