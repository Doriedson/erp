<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\BlackFriday;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

switch ($_POST['action']) {

	case "load":

		$tplBlackFriday = new View("templates/black_friday");

		// $data = ['data' => date('Y-m-d')];

        $blackfriday = new BlackFriday();

        $blackfriday->getList();

        $data['extra_block_blackfriday'] = "";
        $data['blackfriday_notfound'] = "";

        if ($row = $blackfriday->getResult()) {

            $data['blackfriday_notfound'] = "hidden";

            do {

                $row = BlackFriday::FormatFields($row);

                $data['extra_block_blackfriday'].= $tplBlackFriday->getContent($row, "EXTRA_BLOCK_BLACKFRIDAY");

            } while ($row = $blackfriday->getResult());
        }
		
        Send($tplBlackFriday->getContent($data, "BLOCK_PAGE"));

	break;

    case "blackfriday_popupnew":

        $data = ['data' => date('Y-m-d')];

        $tplBlackFriday = new View("templates/black_friday");

        Send($tplBlackFriday->getContent($data, "EXTRA_BLOCK_POPUP_BLACKFRIDAY_NEW"));

        break;

	case "blackfriday_add":

		$data = $_POST['data'];
		$desconto = $_POST['desconto'];
        $acumulativo = ($_POST['acumulativo'] == "false")? 0 : 1;
		
		$blackfriday = new BlackFriday();

        if ($blackfriday->hasDate($data)) {

            Notifier::Add("Data já cadastrada!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $data = [
            "data" => $data,
            "desconto" => $desconto,
            "acumulativo" => $acumulativo
        ];

        $id_blackfriday = $blackfriday->Create($data);

        $blackfriday->Read($id_blackfriday);

        if ($row = $blackfriday->getResult()) {

            $tplBlackFriday = new View("templates/black_friday");

            $row = BlackFriday::FormatFields($row);

            Send($tplBlackFriday->getContent($row, "EXTRA_BLOCK_BLACKFRIDAY"));

        } else {

            Notifier::Add("Erro ao cadastrar nova data!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
	break;

	case "blackfriday_del":

		$id_blackfriday = $_POST['id_blackfriday'];

		$blackfriday = new BlackFriday();

        if ($blackfriday->Delete($id_blackfriday)) {

            Notifier::Add("Data removida com sucesso!", Notifier::NOTIFIER_DONE);
            Send([]);

        } else {

            Notifier::Add("Erro ao remover data!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}