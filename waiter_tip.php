<?php

use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Config;

require "inc/config.inc.php";
require "inc/authorization.php";

function WaitertipFormEdit($block, $message_error) {

	$tplWaitertip = new View('waiter_tip');

	$waitertip = new Config();
	$waitertip->Read();

	if ($row = $waitertip->getResult()) {

		$row = Config::FormatFields($row);

		Send($tplWaitertip->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WaitertipFormCancel($block, $message_error) {

	$tplWaitertip = new View('waiter_tip');

	$waitertip = new Config();
	$waitertip->Read();

	if ($row = $waitertip->getResult()) {

		$row = Config::FormatFields($row);

		Send($tplWaitertip->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WaitertipFormSave($field, $block, $message_error) {

	$value = $_POST['value'];

	$data = [
		'field' => $field,
		'value' => $value,
	];

	$billstopay = new Config();

	$billstopay->Update($data);

	$tplWaitertip = new View('waiter_tip');

	$billstopay->Read();

	if ($row = $billstopay->getResult()) {

		$row = Config::FormatFields($row);

		Send($tplWaitertip->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$tplWaitertip = new View('waiter_tip');

		$config = new Config();

		$config->Read();

		if ($row = $config->getResult()) {

			$row = Config::FormatFields($row);

			Send($tplWaitertip->getContent($row, "BLOCK_PAGE"));

		} else {

			Notifier::Add("Erro ao carregar informações da taxa de serviço!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "waitertip_taxaservico_edit":

		WaitertipFormEdit("EXTRA_BLOCK_TAXA_FORM", "Erro ao carregar formulário para alteração de taxa de servico!");
	break;

	case "waitertip_taxaservico_cancel":

		WaitertipFormCancel("BLOCK_TAXA", "Erro ao carregar taxa de servico!");
	break;

	case "waitertip_taxaservico_save":

		WaitertipFormSave("taxa_servico", "BLOCK_TAXA", "Erro ao carregar taxa de servico!");
	break;
}