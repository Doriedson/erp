<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\BillsToPay;
use database\BillsToPaySector;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR);

switch ($_POST['action']) {

	case "load":

		$billsSector = new BillsToPaySector();
		$billsSector->getList();

		$tplSector = new View('templates/bills_to_pay_sector');

		$tr = "";

		if ($row = $billsSector->getResult()) {

			do {

				$tr.= $tplSector->getContent($row, "EXTRA_BLOCK_BILLSTOPAY");

			} while ($row = $billsSector->getResult());

		} else {

			$tr = $tplSector->getContent([], 'EXTRA_BLOCK_BILLSTOPAY_NONE');
		}

		Send($tplSector->getContent(["extra_block_billstopay" => $tr], "BLOCK_PAGE"));
	break;

	case "billstopay_popup_newsector":

        $tplSector = new View('templates/bills_to_pay_sector');

		Send($tplSector->getContent($row, "EXTRA_BLOCK_POPUP_BILLSTOPAY_NEWSECTOR"));

		break;

	case "billstopaysector_setor_new":

		$billsSector = new BillsToPaySector();

		$id_contasapagarsetor = $billsSector->Create($_POST['value']);

		$billsSector->Read($id_contasapagarsetor);

		if ($row = $billsSector->getResult()) {

            $tplSector = new View('templates/bills_to_pay_sector');

			Send($tplSector->getContent($row, "EXTRA_BLOCK_BILLSTOPAY"));

		} else {

			Notifier::Add("Erro ao cadastrar setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "billstopaysector_contasapagarsetor_edit":

        $billsSector = new BillsToPaySector();

        $billsSector->Read($_POST['id_contasapagarsetor']);

        if ($row = $billsSector->getResult()) {

            $tplSector = new View('templates/bills_to_pay_sector');

            Send($tplSector->getContent($row, "EXTRA_BLOCK_CONTASAPAGARSETOR_FORM"));

        } else {

            Notifier::Add("Error ao carregar setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }
	break;

	case "billstopaysector_contasapagarsetor_cancel":

		$billsSector = new BillsToPaySector();

		$billsSector->Read($_POST['id_contasapagarsetor']);

		if ($row = $billsSector->getResult()) {

            $tplSector = new View('templates/bills_to_pay_sector');

			Send($tplSector->getContent($row, "BLOCK_CONTASAPAGARSETOR"));

		} else {

			Notifier::Add("Setor não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "billstopaysector_contasapagar_save":

		$id_contasapagarsetor = $_POST['id_contasapagarsetor'];
		$contasapagarsetor = $_POST['contasapagarsetor'];

		$billsSector = new BillsToPaySector();

		$billsSector->Update([
			'id_contasapagarsetor' => $id_contasapagarsetor,
            "field" => 'contasapagarsetor',
    		'value' => $contasapagarsetor
		]);

		$billsSector->Read($id_contasapagarsetor);

		if ($row = $billsSector->getResult()) {

			$tplSector = new View("templates/bills_to_pay_sector");

			Send($tplSector->getContent($row, "BLOCK_CONTASAPAGARSETOR"));

		} else {

			Notifier::Add("Erro a cadastrar setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "billstopaysector_setor_delete":

		$id_contasapagarsetor = $_POST['id_contasapagarsetor'];

        $billsToPay = new BillsToPay();

        if ($billsToPay->hasSectorInUse($id_contasapagarsetor)) {

			Notifier::Add("Setor em uso não pode ser excluído!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$billsSector = new BillsToPaySector();

			if ($billsSector->Delete($id_contasapagarsetor)) {

				$billsSector->getList();

				if ($billsSector->getResult()) {

					Notifier::Add("Setor removido com sucesso!", Notifier::NOTIFIER_DONE);
					Send("");

				} else {

					$tplSector = new View("templates/bills_to_pay_sector");

					Notifier::Add("Setor removido com sucesso!", Notifier::NOTIFIER_DONE);
					Send($tplSector->getContent([], 'EXTRA_BLOCK_BILLSTOPAY_NONE'));
				}

			} else {

                Notifier::Add("Erro ao remover setor!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}
	break;

	default:

		Notifier::Add("Requisição inválida", Notifier::NOTIFIER_ERROR);
		Send(null);
	break;
}