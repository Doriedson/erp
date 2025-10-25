<?php

use App\Legacy\DeliveryDireto;
use App\Legacy\Notifier;
use App\View\View;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST['action']) {

    case "deliverydireto_ativo":

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            if($row['ativo'] == 0) {

                if(!DeliveryDireto::Authenticate()) {

                    Notifier::Add("Ocorreu um erro na autenticação!<br>Verifique os dados de acesso.", Notifier::NOTIFIER_ERROR);
                    Send("");
                }
            }

        } else {

            Notifier::Add("Erro ao carregar dados do X-DeliveryDireto-ID!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $dd->ToggleAtivo();

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($row['ativo']);

        } else {

            Notifier::Add("Erro ao carregar dados do X-DeliveryDireto-ID!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_storeid_edit":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            if ($row['ativo'] == 1) {

                Notifier::Add("Desative a integração para alterar os dados de acesso!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'EXTRA_BLOCK_STOREID_FORM'));

        } else {

            Notifier::Add('Erro ao carregar dados do X-DeliveryDireto-ID!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_storeid_cancel":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_STOREID'));

        } else {

            Notifier::Add('Erro ao carregar dados do X-DeliveryDireto-ID!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_storeid_save":

        $value = $_POST['value'];

        $data = [
            'field' => 'store_id',
            'value' => $value,
        ];

        $dd = new DeliveryDireto();

        $dd->Update($data);

        $tplDeliveryDireto = new View('integrations');

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_STOREID'));

        } else {

            Notifier::Add('Erro ao carregar dados do X-DeliveryDireto-ID!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_usuario_edit":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            if ($row['ativo'] == 1) {

                Notifier::Add("Desative a integração para alterar os dados de acesso!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'EXTRA_BLOCK_USUARIO_FORM'));

        } else {

            Notifier::Add('Erro ao carregar dados acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_usuario_cancel":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_USUARIO'));

        } else {

            Notifier::Add('Erro ao carregar dados para acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_usuario_save":

        $value = $_POST['value'];

        $data = [
            'field' => 'usuario',
            'value' => $value,
        ];

        $dd = new DeliveryDireto();

        $dd->Update($data);

        $tplDeliveryDireto = new View('integrations');

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_USUARIO'));

        } else {

            Notifier::Add('Erro ao carregar dados acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_senha_edit":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            if ($row['ativo'] == 1) {

                Notifier::Add("Desative a integração para alterar os dados de acesso!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'EXTRA_BLOCK_SENHA_FORM'));

        } else {

            Notifier::Add('Erro ao carregar dados acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_senha_cancel":

        $tplDeliveryDireto = new View('integrations');

        $dd = new DeliveryDireto();

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_SENHA'));

        } else {

            Notifier::Add('Erro ao carregar dados para acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_senha_save":

        $value = $_POST['value'];

        $data = [
            'field' => 'senha',
            'value' => $value,
        ];

        $dd = new DeliveryDireto();

        $dd->Update($data);

        $tplDeliveryDireto = new View('integrations');

        $dd->Read();

        if ($row = $dd->getResult()) {

            $row = DeliveryDireto::FormatFields($row);

            Send($tplDeliveryDireto->getContent($row, 'BLOCK_SENHA'));

        } else {

            Notifier::Add('Erro ao carregar dados acesso ao Delivery Direto!', Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "deliverydireto_create_address":

        $dd = new DeliveryDireto();

        $ret = $dd->getAddressFee();

        if ($ret) {

            Notifier::Add($ret, Notifier::NOTIFIER_DONE);
            Send(null);

        } else {

            Notifier::Add("Erro ao consultar endereço!", Notifier::NOTIFIER_DONE);
            Send(null);
        }

        break;

    case "deliverydireto_calculate_fee":

        $dd = new DeliveryDireto();

        $ret = $dd->CalculateFee(52658901);

        Notifier::Add($ret, Notifier::NOTIFIER_DONE);
        Send(null);

        break;

    case "deliverydireto_get_orders":

        $dd = new DeliveryDireto();

        $ret = $dd->getOrders();

        Notifier::Add($ret, Notifier::NOTIFIER_DONE);
        Send(null);

        break;

    default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}