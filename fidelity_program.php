<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\FidelityProgram;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

function FidelityFormEdit($block, $message_error) {

	$id_fidelidaderegra = $_POST['id_fidelidaderegra'];

	$tplFidelity = new View('templates/fidelity_program');

	$fidelity = new FidelityProgram();
	$fidelity->Read($id_fidelidaderegra);

	if ($row = $fidelity->getResult()) {

		$row = FidelityProgram::FormatFields($row);

		Send($tplFidelity->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function FidelityFormCancel($block, $message_error) {

	$id_fidelidaderegra = $_POST['id_fidelidaderegra'];

	$tplFidelity = new View('templates/fidelity_program');

	$fidelity = new FidelityProgram();
	$fidelity->Read($id_fidelidaderegra);

	if ($row = $fidelity->getResult()) {

		$row = FidelityProgram::FormatFields($row);

		Send($tplFidelity->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function FidelityFormSave($field, $block, $message_error) {

	$id_fidelidaderegra = $_POST['id_fidelidaderegra'];
	$value = $_POST['value'];

	$data = [
		'id_fidelidaderegra' => (int) $id_fidelidaderegra,
		'field' => $field,
		'value' => $value,
	];

	$fidelity = new FidelityProgram();

	$fidelity->Update($data);

	$tplFidelity = new View('templates/fidelity_program');

	$fidelity->Read($id_fidelidaderegra);

	if ($row = $fidelity->getResult()) {

		$row = FidelityProgram::FormatFields($row);

		Send($tplFidelity->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function FidelityGetRules() {

	$tplFidelity = new View('templates/fidelity_program');

	$fidelity = new FidelityProgram();

	$fidelity->getList();

	$extra_block_fidelity = "";

	if ($row = $fidelity->getResult()) {

		do {
			$row = FidelityProgram::FormatFields($row);

			$extra_block_fidelity.= $tplFidelity->getContent($row, "EXTRA_BLOCK_FIDELITY");

		} while ($row = $fidelity->getResult());

	// } else {

	// 	$extra_block_fidelity = $tplFidelity->getContent([], "EXTRA_BLOCK_FIDELITY_NONE");
	}

	return $extra_block_fidelity;
}

switch ($_POST['action']) {

	case "load":

		$tplFidelity = new View('templates/fidelity_program');

		$fidelity = new FidelityProgram();

		$fidelity->getDays();

		if ($row = $fidelity->getResult()) {

			$data['dias_compra'] = $row['dias_compra'];

			$data['extra_block_fidelity'] = FidelityGetRules();

			if (empty($data['extra_block_fidelity'])) {

				$data['fidelity_notfound'] = "";

			} else {

				$data['fidelity_notfound'] = "hidden";
			}

			Send($tplFidelity->getContent($data, "BLOCK_PAGE"));

		} else {

			Notifier::Add("Erro ao carregar dados do programa fidelidade.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "fidelity_new":

		$fidelity = new FidelityProgram();

		$id_fidelity = $fidelity->Create();

		$fidelity->Read($id_fidelity);

		if ($row = $fidelity->getResult()) {

			$tplFidelity = new View("templates/fidelity_program");

			$row = FidelityProgram::FormatFields($row);

			Send($tplFidelity->getContent($row, "EXTRA_BLOCK_FIDELITY"));

		} else {

			Notifier::Add("Erro ao criar regra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "fidelity_delete":

		$id_fidelidaderegra = $_POST['id_fidelidaderegra'];

		$fidelity = new FidelityProgram();

		if ($fidelity->Delete($id_fidelidaderegra) > 0) {

			Notifier::Add("Regra excluída com sucesso.", Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Erro ao remover regra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "fidelity_up":

		$id_fidelidaderegra = $_POST['id_fidelidaderegra'];

		$fidelity = new FidelityProgram();

		$fidelity->RuleUp($id_fidelidaderegra);

		Notifier::Add("Prioridade alterada com sucesso!", Notifier::NOTIFIER_DONE);
		Send(FidelityGetRules());

		break;

	case "fidelity_down":

		$id_fidelidaderegra = $_POST['id_fidelidaderegra'];

		$fidelity = new FidelityProgram();

		$fidelity->RuleDown($id_fidelidaderegra);

		Notifier::Add("Prioridade alterada com sucesso!", Notifier::NOTIFIER_DONE);
		Send(FidelityGetRules());

		break;

	case "fidelity_condicao_edit":

		FidelityFormEdit("EXTRA_BLOCK_CONDICAO_FORM", "Erro ao carregar regra!");
	break;

	case "fidelity_condicao_cancel":

		FidelityFormCancel("BLOCK_CONDICAO", "Erro ao carregar regra!");
	break;

	case "fidelity_condicao_save":

		FidelityFormSave('condicao', "BLOCK_CONDICAO", "Erro ao carregar regra!");
	break;

	case "fidelity_valor_edit":

		FidelityFormEdit("EXTRA_BLOCK_VALOR_FORM", "Erro ao carregar valor!");
	break;

	case "fidelity_valor_cancel":

		FidelityFormCancel("BLOCK_VALOR", "Erro ao carregar valor!");
	break;

	case "fidelity_valor_save":

		FidelityFormSave('valor', "BLOCK_VALOR", "Erro ao carregar valor!");
	break;

	case "fidelity_desconto_edit":

		FidelityFormEdit("EXTRA_BLOCK_DESCONTO_FORM", "Erro ao carregar desconto!");
	break;

	case "fidelity_desconto_cancel":

		FidelityFormCancel("BLOCK_DESCONTO", "Erro ao carregar desconto!");
	break;

	case "fidelity_desconto_save":

		FidelityFormSave('desconto', "BLOCK_DESCONTO", "Erro ao carregar desconto!");
	break;

	case "fidelity_dias_edit":

		$fidelity = new FidelityProgram();

		$fidelity->getDays();

		if ($row = $fidelity->getResult()) {

			$tplFidelity = new View("templates/fidelity_program");

			Send($tplFidelity->getContent($row, "EXTRA_BLOCK_DIAS_FORM"));

		} else {

			Notifier::Add("Erro ao carregar formulário para alteração de dias.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "fidelity_dias_cancel":

		$fidelity = new FidelityProgram();

		$fidelity->getDays();

		if ($row = $fidelity->getResult()) {

			$tplFidelity = new View("templates/fidelity_program");

			Send($tplFidelity->getContent($row, "BLOCK_DIAS"));

		} else {

			Notifier::Add("Erro ao carregar número de dias.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "fidelity_dias_save":

		$dias_compra = $_POST['dias_compra'];

		if (intval($dias_compra) == 0) {

			Notifier::Add("Número de dias tem que ser maior que zero.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$fidelity = new FidelityProgram();

		$fidelity->setDays($dias_compra);

		$fidelity->getDays();

		if ($row = $fidelity->getResult()) {

			$tplFidelity = new View("templates/fidelity_program");

			Send($tplFidelity->getContent($row, "BLOCK_DIAS"));

		} else {

			Notifier::Add("Erro ao carregar número de dias.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	default:

		Notifier::Add("Requisição inválida", Notifier::NOTIFIER_ERROR);
		Send(null);

		break;
}