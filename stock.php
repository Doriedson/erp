<?php

use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Config;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST['action']) {

	case "load":

		$config = new Config();
		$config->Read();

		$tplStock = new View('stock');

		$cashtype = "";

		if ($row = $config->getResult()) {

			if ($row['estoque_secundario'] == 1) {

				$data['extra_block_button_ativo'] = $tplStock->getContent($row, "EXTRA_BLOCK_BUTTON_ATIVO");

			} else {

				$data['extra_block_button_ativo'] = $tplStock->getContent($row, "EXTRA_BLOCK_BUTTON_INATIVO");
			}

			Send($tplStock->getContent($data, "BLOCK_PAGE"));

		} else {

			Notifier::Add("Erro ao carregar configuração de estoque,", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

    case "stock_toggle_active":

        $config = new Config();

        $config->ToggleActiveSecondaryStock();

        $config->Read();

        if ($row = $config->getResult()) {

			$tplStock = new View('stock');

			if ($row['estoque_secundario'] == 1) {

				Send($tplStock->getContent($row, "EXTRA_BLOCK_BUTTON_ATIVO"));

			} else {

				Send($tplStock->getContent($row, "EXTRA_BLOCK_BUTTON_INATIVO"));
			}

        } else {

            Notifier::Add("Erro ao ativar/desativar estoque secundário!", null);
			Send(null);
        }

        break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

        break;
}