<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\PrinterConfig;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR);

function PrintingFormEdit($block, $message_error) {

	$id_impressao = $_POST['id_impressao'];

	$tplPrinting = new View('templates/printing');

	$printer = new PrinterConfig();
	$printer->PrintingRead($id_impressao);

	if ($row = $printer->getResult()) {

		$row = PrinterConfig::PrintingFormatFields($row);

		Send($tplPrinting->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function PrintingFormCancel($block, $message_error) {

	$id_impressao = $_POST['id_impressao'];

	$tplPrinting = new View('templates/printing');

	$printer = new PrinterConfig();
	$printer->PrintingRead($id_impressao);

	if ($row = $printer->getResult()) {

		$row = PrinterConfig::PrintingFormatFields($row);

		Send($tplPrinting->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PrintingFormSave($field, $block, $message_error) {

	$id_impressao = $_POST['id_impressao'];
	$value = $_POST['value'];

	if($field == "id_impressora" && $value == "") {

		$value = null;
	}

	$data = [
		'id_impressao' => (int) $id_impressao,
		'field' => $field,
		'value' => $value,
	];

	$printer = new PrinterConfig();

	$printer->PrintingUpdate($data);

	$tplPrinting = new View('templates/printing');

	$printer->PrintingRead($id_impressao);

	if ($row = $printer->getResult()) {

		$row = PrinterConfig::PrintingFormatFields($row);

		Send($tplPrinting->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$printer = new PrinterConfig();

		$tplPrinting = new View('templates/printing');

        $printer->getPrintingList();

        $extra_block_printing = "";

        if ($row = $printer->getResult()) {

            $data['hidden'] = "hidden";

            do {

				$row = PrinterConfig::PrintingFormatFields($row);

                $extra_block_printing .= $tplPrinting->getContent($row, "EXTRA_BLOCK_PRINTING");

            } while ($row = $printer->getResult());
        }

        $data['extra_block_printing'] = $extra_block_printing;

		Send($tplPrinting->getContent($data, "BLOCK_PAGE"));

		break;

	case "printing_impressora_edit":

		PrintingFormEdit('EXTRA_BLOCK_IMPRESSORA_FORM', 'Erro ao carregar impressora!');
	break;

	case "printing_impressora_cancel":

		PrintingFormCancel('BLOCK_IMPRESSORA', 'Erro ao carregar impressora!');
	break;

	case "printing_impressora_save":

		PrintingFormSave('id_impressora', 'BLOCK_IMPRESSORA', 'Erro ao carregar impressora!');
	break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}