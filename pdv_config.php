<?php


use App\View\View;
use App\Legacy\Pdv;
use App\Legacy\Notifier;

require "inc/config.inc.php";
require "inc/authorization.php";

function PDVFormEdit($block, $message_error) {

	$id_pdv = $_POST['id_pdv'];

	$tplPdv = new View('pdv_config');

	$pdv = new Pdv();
	$pdv->Read($id_pdv);

	if ($row = $pdv->getResult()) {

		$row = Pdv::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PDVFormCancel($block, $message_error) {

	$id_pdv = $_POST['id_pdv'];

	$tplPdv = new View('pdv_config');

	$pdv = new Pdv();
	$pdv->Read($id_pdv);

	if ($row = $pdv->getResult()) {

		$row = Pdv::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PDVFormSave($field, $block, $message_error) {

	$id_pdv = $_POST['id_pdv'];
	$value = $_POST['value'];

	if(($field == "id_impressora" || $field == "id_gaveteiro") && $value == "") {

		$value = null;
	}

	$data = [
		'id_pdv' => (int) $id_pdv,
		'field' => $field,
		'value' => $value,
	];

	$pdv = new Pdv();

    $pdv->Update($data);

	$tplPdv = new View('pdv_config');

	$pdv->Read($id_pdv);

	if ($row = $pdv->getResult()) {

		$row = Pdv::FormatFields($row);

		Send($tplPdv->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$tplPdv = new View("pdv_config");

		$data = ['data' => date('Y-m-d')];

        $pdv = new Pdv();

        $pdv->getList();

		$data['extra_block_pdv'] = "";

        if ($row = $pdv->getResult()) {

            do {

                $row = Pdv::FormatFields($row);

                $data['extra_block_pdv'] .= $tplPdv->getContent($row, "EXTRA_BLOCK_PDV");

            } while ($row = $pdv->getResult());

        } else {

            $data['extra_block_pdv'] = $tplPdv->getContent($data, "EXTRA_BLOCK_PDV_NONE");
        }

        Send($tplPdv->getContent($data, "BLOCK_PAGE"));

	break;

    case "pdv_descricao_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormEdit("EXTRA_BLOCK_FORM_DESCRICAO", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_descricao_cancel":

		PDVFormCancel("BLOCK_DESCRICAO", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_descricao_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormSave('descricao', "BLOCK_DESCRICAO", "Erro ao salvar descrição do PDV!");
	break;

    case "pdv_balanca":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$id_pdv = $_POST['id_pdv'];

		$pdv = new Pdv();
		$pdv->BalancaToggleActive($id_pdv);

		$pdv->Read($id_pdv);

		if ($row = $pdv->getResult()) {

			$row = Pdv::FormatFields($row);

			Send($row['bt_balanca']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação da balança!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "pdv_trocoini":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$id_pdv = $_POST['id_pdv'];

		$pdv = new Pdv();
		$pdv->TrocoiniToggleActive($id_pdv);

		$pdv->Read($id_pdv);

		if ($row = $pdv->getResult()) {

			$row = Pdv::FormatFields($row);

			Send($row['bt_trocoini']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação da balança!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

    case "pdv_charwrite_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormEdit("EXTRA_BLOCK_FORM_CHARWRITE", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_charwrite_cancel":

		PDVFormCancel("BLOCK_CHARWRITE", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_charwrite_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormSave('balanca_charwrite', "BLOCK_CHARWRITE", "Erro ao salvar descrição do PDV!");
	break;

    case "pdv_charend_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormEdit("EXTRA_BLOCK_FORM_CHAREND", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_charend_cancel":

		PDVFormCancel("BLOCK_CHAREND", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_charend_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormSave('balanca_charend', "BLOCK_CHAREND", "Erro ao salvar descrição do PDV!");
	break;

    case "pdv_impressora":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$id_pdv = $_POST['id_pdv'];

		$pdv = new Pdv();
		$pdv->ImpressoraToggleActive($id_pdv);

		$pdv->Read($id_pdv);

		if ($row = $pdv->getResult()) {

			$row = Pdv::FormatFields($row);

			Send($row['bt_impressora']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação da balança!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

    case "pdv_gaveta":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$id_pdv = $_POST['id_pdv'];

		$pdv = new Pdv();
		$pdv->GavetaToggleActive($id_pdv);

		$pdv->Read($id_pdv);

		if ($row = $pdv->getResult()) {

			$row = Pdv::FormatFields($row);

			Send($row['bt_gaveta']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação da gaveta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

    case "pdv_impressora_path_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormEdit("EXTRA_BLOCK_IMPRESSORA_FORM", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_impressora_path_cancel":

		PDVFormCancel("BLOCK_IMPRESSORA", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_impressora_path_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormSave('id_impressora', "BLOCK_IMPRESSORA", "Erro ao salvar descrição do PDV!");
	break;

	case "pdv_cashdrawertype_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormEdit("EXTRA_BLOCK_CASHDRAWERTYPE_FORM", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_cashdrawertype_cancel":

		PDVFormCancel("BLOCK_CASHDRAWERTYPE", "Erro ao carregar descrição do PDV!");
	break;

	case "pdv_cashdrawertype_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PDVFormSave('id_gaveteiro', "BLOCK_CASHDRAWERTYPE", "Erro ao salvar descrição do PDV!");
	break;

	case "pdv_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$pdv = new Pdv();

		if (isset($_COOKIE['id_pdv'])) {

			$pdv->get($_COOKIE['id_pdv']);

			if ($row = $pdv->getResult()) {

				Notifier::Add("Já existe um pdv configurado neste computador!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			setcookie("id_pdv", password_hash(rand(), PASSWORD_BCRYPT), time() + 3600 * 24 * 7);
		}

		$id_pdv = $_COOKIE['id_pdv'];

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}