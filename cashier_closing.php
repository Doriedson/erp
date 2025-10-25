<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Config;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST["action"]) {

	case "load":

		$tplPdv = new View("cashier_closing");

        $config = new Config();

        $config->Read();

        if ($row = $config->getResult()) {

            $row = Config::FormatFields($row);

            Send($tplPdv->getContent($row, "BLOCK_PAGE"));

        } else {

            Notifier::Add("Erro ao carregar configurações de fechamento de caixa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;


    case "cashierclosing":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

        $fieldX = $_POST['field'];
        $value = ($_POST['value'] == "true")? 1: 0;
        // $return = ($_POST['value'] == "true")? true: false;

        $config = new Config();

        switch ($fieldX) {

            case "taxagarcom":

                $field = "fc_waitertip_print";
                break;

            case "produtos":

                $field = "fc_reverseitem_print";
                break;

            case "vendas":

                $field = "fc_reversesale_print";
                break;

            case "produtosvendidos":

                $field = "fc_productssold_print";
                break;

            case "vendaprazo":

                $field = "fc_forwardsale_print";
                break;

            case "vendaprazopaga":

                $field = "fc_forwardsalepaid_print";
                break;

            case "pedidopago":

                $field = "fc_orderpaid_print";
                break;

            case "reprint":

                $field = "fc_reprint_print";
                break;

            case "mesas":

                $field = "fc_table";
                break;

        }

        $config->Update([
            "field" => $field,
            "value" => $value
        ]);

        $config->Read();

        if ($row = $config->getResult()) {

            $row = Config::FormatFields($row);

            Send($row[$field]);

        } else {

            Notifier::Add("Ocorreu um erro ao carregar os dados de configurações!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "cashierclosing_product_edit":

        $config = new Config();

        $config->Read();

        if ($row = $config->getResult()) {

            $row = Config::FormatFields($row);

            $tplCashierClosing = new View("cashier_closing");

            Send($tplCashierClosing->getContent($row, "EXTRA_BLOCK_CASHIERCLOSING_PRODUCT_FORM"));

        } else {

            Notifier::Add("Erro ao carregar dados de configurações.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

    break;

    case "cashierclosing_product_cancel":

        $config = new Config();

        $config->Read();

        if ($row = $config->getResult()) {

            $row = Config::FormatFields($row);

            $tplCashierClosing = new View("cashier_closing");

            Send($tplCashierClosing->getContent($row, "BLOCK_CASHIERCLOSING_PRODUCT"));

        } else {

            Notifier::Add("Erro ao carregar dados de configurações.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

    break;

    case "cashierclosing_product_save":

        $value = $_POST["value"] == "1"?1:0;

        $config = new Config();

        $config->Update([
            "field" => "fc_productssoldoption_print",
            "value" => $value
        ]);

        $config->Read();

        if ($row = $config->getResult()) {

            $row = Config::FormatFields($row);

            $tplCashierClosing = new View("cashier_closing");

            Send($tplCashierClosing->getContent($row, "BLOCK_CASHIERCLOSING_PRODUCT"));

        } else {

            Notifier::Add("Erro ao carregar dados de configurações.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

    break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

    break;
}