<?php
use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\DeliveryDireto;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST['action']) {

    case "load":

		$tplIntegrations = new View('templates/integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplIntegrations->getContent($row, "BLOCK_PAGE"));

        } else {

            Notifier::Add("Erro ao carregar dados de integração do Delivery Direto.", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}