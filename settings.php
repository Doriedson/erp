<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\BlackFriday;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

switch ($_POST['action']) {

	case "load":

		$tplSettings = new View("settings");

        Send($tplSettings->getContent([], "BLOCK_PAGE"));

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}