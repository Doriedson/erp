<?php

use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\PaymentKind;

require "inc/config.inc.php";
require "inc/authorization.php";

function PaymentKindFormEdit($block, $message_error) {

	$id_especie = $_POST['id_especie'];

	$tplPaymentKind = new View('sale_cashtype');

	$paymentKind = new PaymentKind();
	$paymentKind->Read($id_especie);

	if ($row = $paymentKind->getResult()) {

		$row = PaymentKind::FormatFields($row);

		Send($tplPaymentKind->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function PaymentKindFormCancel($block, $message_error) {

	$id_especie = $_POST['id_especie'];

	$tplPaymentKind = new View('sale_cashtype');

	$paymentKind = new PaymentKind();
	$paymentKind->Read($id_especie);

	if ($row = $paymentKind->getResult()) {

		$row = PaymentKind::FormatFields($row);

		Send($tplPaymentKind->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PaymentKindFormSave($field, $block, $message_error) {

	$id_especie = $_POST['id_especie'];
	$especie = $_POST['especie'];

    //Dinheiro or client credit cannot be edited
    if ($id_especie == 1 || $id_especie == 2) {

        Notifier::Add("Esta espécie não pode ser editada!<br>Padrão do sistema.", Notifier::NOTIFIER_ERROR);
		Send(null);
    }

	$paymentKind = new PaymentKind();

	$paymentKind->Update($id_especie, $especie);

	$tplPaymentKind = new View('sale_cashtype');

	$paymentKind->Read($id_especie);

	if ($row = $paymentKind->getResult()) {

		$row = PaymentKind::FormatFields($row);

		Send($tplPaymentKind->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$paymentKind = new PaymentKind();
		$paymentKind->getList();

		$tplCashtype = new View('sale_cashtype');

		$cashtype = "";

		while ($row = $paymentKind->getResult()) {

			$row = PaymentKind::FormatFields($row);

			$cashtype.= $tplCashtype->getContent($row, "EXTRA_BLOCK_SALECASHTYPE");
		}

		$data['extra_block_salecashtype'] = $cashtype;

		Send($tplCashtype->getContent($data, "BLOCK_PAGE"));
	break;

	case "salecashtype_add":

		$paymentKind = new PaymentKind();

		$especie = $_POST['especie'];

		$id_especie = $paymentKind->Create($especie);

        if ($id_especie) {

            $paymentKind->Read($id_especie);

            $tplCashtype = new View('sale_cashtype');

            if ($row = $paymentKind->getResult()) {

                $row = PaymentKind::FormatFields($row);

                Send($tplCashtype->getContent($row, "EXTRA_BLOCK_SALECASHTYPE"));

            } else {

                Notifier::Add("Não foi possível encontrar o espécie!", Notifier::NOTIFIER_ERROR);
				Send(null);
            }

        } else {

            Notifier::Add("Erro ao cadastrar espécie!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }
	break;

	case "salecashtype_edit":

		PaymentKindFormEdit('EXTRA_BLOCK_CASHTYPE_FORM', 'Erro ao carregar espécie!');
	break;

	case "salecashtype_cancel":

		PaymentKindFormCancel('BLOCK_CASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "salecashtype_save":

		PaymentKindFormSave('especie', 'BLOCK_CASHTYPE', 'Erro ao carregar espécie!');

        break;

	case "salecashtype_delete":

		$id_especie = $_POST['id_especie'];

        //Dinheiro or client credit cannot be removed
        if ($id_especie == 1 || $id_especie == 2) {

            Notifier::Add("Esta espécie não pode ser removida!<br>Padrão do sistema.", Notifier::NOTIFIER_ERROR);
			Send(null);
        }

		$paymentKind = new PaymentKind();

		if ($paymentKind->isInUse($id_especie)) {

			Notifier::Add("Espécie em uso não pode ser removida!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			if ($paymentKind->Delete($id_especie)) {

				Notifier::Add("Espécie excluída com sucesso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				Notifier::Add("Erro ao excluir espécie!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

        break;

    case "salecashtype_toggle_active":

        $id_especie = $_POST['id_especie'];

		$paymentKind = new PaymentKind();

        //Dinheiro cannot be inactived
        if ($id_especie == 1) {

			Notifier::Add("Esta espécie não pode ser desativada!<br>Padrão do sistema.", Notifier::NOTIFIER_ERROR);
			Send("checked");
        }

        $paymentKind->ToggleActive($id_especie);

        $paymentKind->Read($id_especie);

        if ($row = $paymentKind->getResult()) {

			$row = PaymentKind::FormatFields($row);

			Send($row['ativo']);

        } else {

            Notifier::Add("Erro ao ativar/desativar espécie!", null);
			Send("");
        }

        break;

	case "salecashtype_popup_new":

		$tplPaymentKind = new View('sale_cashtype');

		Send($tplPaymentKind->getContent([], "EXTRA_BLOCK_POPUP_SALECASHTYPE_NEW"));

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

        break;
}