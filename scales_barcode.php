<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Config;

require "inc/config.inc.php";
require "inc/authorization.php";

function ScalesBarcodeFormEdit($block, $message_error) {

	$tplScaleBarcode = new View('scales_barcode');

	$config = new Config();
	$config->Read();

	if ($row = $config->getResult()) {

		$row = Config::FormatFields($row);

		if ($block == "EXTRA_BLOCK_FORM_WEIGHTORPRICE") {

			if ($row['scalesbarcode_weightorprice'] == 0) {

				$row['setor_option'] = '<option value="0" selected>Peso</option><option value="1">Valor</option>';

			} else {

				$row['setor_option'] = '<option value="0">Peso</option><option value="1" selected>Valor</option>';
			}
		}

		Send($tplScaleBarcode->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ScalesBarcodeFormCancel($block, $message_error) {

	$tplScaleBarcode = new View('scales_barcode');

	$config = new Config();
	$config->Read();

	if ($row = $config->getResult()) {

		$row = Config::FormatFields($row);

		Send($tplScaleBarcode->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ScalesBarcodeFormSave($field, $block, $message_error) {

	$value = $_POST['value'];

	$data = [
		'field' => $field,
		'value' => $value,
	];

	$config = new Config();

    $config->Update($data);

	$tplScaleBarcode = new View('scales_barcode');

	$config->Read();

	if ($row = $config->getResult()) {

		$row = Config::FormatFields($row);

		Send($tplScaleBarcode->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$tplScaleBarcode = new View("scales_barcode");

        $config = new Config();

        $config->Read();

        if ($row = $config->getResult()) {

			$row = Config::FormatFields($row);

            Send($tplScaleBarcode->getContent($row, "BLOCK_PAGE"));

        } else {

            Notifier::Add("Erro ao carregar configurações de código de barras da balança!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }

        break;

    case "scalesbarcode":

        ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

        $value = ($_POST['value'] == "true")? 1: 0;

        $return = ($_POST['value'] == "true")? true: false;

        $config = new Config();

        $config->Update([
            "field" => "scalesbarcode",
            "value" => $value
        ]);

        Send($return);

    break;

    case "scalesbarcode_startnumber_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_STARTNUMBER", "Erro ao carregar dígito inicial do código de barras!");
	break;

	case "scalesbarcode_startnumber_cancel":

		ScalesBarcodeFormCancel("BLOCK_STARTNUMBER", "Erro ao carregar dígito inicial do código de barras!");
	break;

	case "scalesbarcode_startnumber_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_startnumber', "BLOCK_STARTNUMBER", "Erro ao salvar dígito inicial do código de barras!");
	break;

    case "scalesbarcode_sizecode_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_SIZECODE", "Erro ao carregar tamanho do código de barras!");
	break;

	case "scalesbarcode_sizecode_cancel":

		ScalesBarcodeFormCancel("BLOCK_SIZECODE", "Erro ao carregar tamanho do código de barras!");
	break;

	case "scalesbarcode_sizecode_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_sizecode', "BLOCK_SIZECODE", "Erro ao salvar tamanho do código de barras!");
	break;

    case "scalesbarcode_productstartposition_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_PRODUCTSTARTPOSITION", "Erro ao carregar posição do dígito que identifica o final do código do produto!");
	break;

	case "scalesbarcode_productstartposition_cancel":

		ScalesBarcodeFormCancel("BLOCK_PRODUCTSTARTPOSITION", "Erro ao carregar posição do dígito que identifica o final do código do produto!");
	break;

	case "scalesbarcode_productstartposition_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_productstartposition', "BLOCK_PRODUCTSTARTPOSITION", "Erro ao salvar posição do dígito que identifica o final do código do produto!");
	break;

    case "scalesbarcode_productendposition_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_PRODUCTENDPOSITION", "Erro ao carregar posição do dígito que identifica o final do código do produto!");
	break;

	case "scalesbarcode_productendposition_cancel":

		ScalesBarcodeFormCancel("BLOCK_PRODUCTENDPOSITION", "Erro ao carregar posição do dígito que identifica o final do código do produto!");
	break;

	case "scalesbarcode_productendposition_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_productendposition', "BLOCK_PRODUCTENDPOSITION", "Erro ao salvar posição do dígito que identifica o final do código do produto!");
	break;

    case "scalesbarcode_weightstartposition_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_WEIGHTSTARTPOSITION", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightstartposition_cancel":

		ScalesBarcodeFormCancel("BLOCK_WEIGHTSTARTPOSITION", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightstartposition_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_weightstartposition', "BLOCK_WEIGHTSTARTPOSITION", "Erro ao salvar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

    case "scalesbarcode_weightendposition_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_WEIGHTENDPOSITION", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightendposition_cancel":

		ScalesBarcodeFormCancel("BLOCK_WEIGHTENDPOSITION", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightendposition_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_weightendposition', "BLOCK_WEIGHTENDPOSITION", "Erro ao salvar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightorprice_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_WEIGHTORPRICE", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightorprice_cancel":

		ScalesBarcodeFormCancel("BLOCK_WEIGHTORPRICE", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightorprice_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_weightorprice', "BLOCK_WEIGHTORPRICE", "Erro ao salvar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightdecimals_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormEdit("EXTRA_BLOCK_FORM_WEIGHTDECIMALS", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightdecimals_cancel":

		ScalesBarcodeFormCancel("BLOCK_WEIGHTDECIMALS", "Erro ao carregar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	case "scalesbarcode_weightdecimals_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		ScalesBarcodeFormSave('scalesbarcode_weightdecimals', "BLOCK_WEIGHTDECIMALS", "Erro ao salvar posição do dígito que identifica o final do peso ou valor do produto!");
	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

        break;
}