<?php

use App\View\View;
use database\Wallet;
use database\WalletCashType;
use database\Notifier;

require "inc/config.inc.php";
require "inc/authorization.php";

function WalletcashtypeFormEdit($block, $message_error) {

	$id_walletcashtype = $_POST['id_walletcashtype'];

	$tplWalletcashtype = new View('templates/wallet');

	$walletcashtype = new WalletCashType();
	$walletcashtype->Read($id_walletcashtype);

	if ($row = $walletcashtype->getResult()) {

		// $row = WalletCashType::FormatFields($row);

		Send($tplWalletcashtype->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WalletcashtypeFormCancel($block, $message_error) {

	$id_walletcashtype = $_POST['id_walletcashtype'];

	$tplWalletcashtype = new View('templates/wallet');

	$walletcashtype = new WalletCashType();
	$walletcashtype->Read($id_walletcashtype);

	if ($row = $walletcashtype->getResult()) {

		// $row = WalletCashType::FormatFields($row);

		Send($tplWalletcashtype->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletcashtypeFormSave($field, $block, $message_error) {

	$id_walletcashtype = $_POST['id_walletcashtype'];
	$value = $_POST['value'];

	$data = [
		'id_walletcashtype' => (int) $id_walletcashtype,
		'field' => $field,
		'value' => $value,
	];

	$walletcashtype = new WalletCashType();

	$walletcashtype->Read($id_walletcashtype);

	if ($row = $walletcashtype->getResult()) {

		$id_wallet = $row['id_wallet'];

	} else {

		Notifier::Add("Erro ao obter dados do setor.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$wallet = new Wallet();

	if ($wallet->isMyWallet($id_wallet) == false) {

		Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$walletcashtype->Update($data);

	$tplWalletcashtype = new View('templates/wallet');

	$walletcashtype->Read($id_walletcashtype);

	if ($row = $walletcashtype->getResult()) {

		$walletcashtype->getList($row['id_wallet']);

		$extra_block_walletdespesa_cashtype_option = "";

		$tplWallet = new View('templates/wallet');

		while ($row_list = $walletcashtype->getResult()) {

			$row_list['selected'] = "";

			if ($row_list['id_walletcashtype'] == $id_walletcashtype) {

				$row_list['selected'] = "selected";
			}

			$extra_block_walletdespesa_cashtype_option .= $tplWallet->getContent($row_list, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
		}

		Send([
			"data" => $tplWalletcashtype->getContent($row, $block),
			"list" => $extra_block_walletdespesa_cashtype_option
		]);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$id_wallet = $_POST['id_wallet'];

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$walletcashtype = new WalletCashType();
		$walletcashtype->getList($id_wallet);

		$tplWallet = new View('templates/wallet');

		$cashtype = "";

		$notfound = "";

		if ($row = $walletcashtype->getResult()) {

			$notfound = "hidden";

			do {

				$cashtype.= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE");

			} while ($row = $walletcashtype->getResult());
		}

		$data = [
			"id_wallet" => $id_wallet,
			"extra_block_walletdespesa_cashtype" => $cashtype,
			"notfound" => $notfound
		];

		// Send($cashtype);
		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE"));

		break;

	case "walletcashtype_add":

		$id_wallet = $_POST['id_wallet'];
		$walletcashtype = $_POST['walletcashtype'];
		// $source = $_POST['source'];

		$tplWallet = new View('templates/wallet');
		$cashtype = new WalletCashType();

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$id_walletcashtype = $cashtype->Create($id_wallet, $walletcashtype);

        if ($id_walletcashtype) {

			$cashtype->getList($id_wallet);

			$extra_block_walletdespesa_cashtype_option = "";

			while ($row = $cashtype->getResult()) {

				$row['selected'] = "";

				if ($row['id_walletcashtype'] == $id_walletcashtype) {

					$row['selected'] = "selected";
				}

				$extra_block_walletdespesa_cashtype_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
			}

			// switch($source) {

			// 	case 'wallet':

					// Send($extra_block_walletdespesa_cashtype_option);

			// 		break;

			// 	case 'wallet_cashtype':

					$cashtype->Read($id_walletcashtype);

					if ($row = $cashtype->getResult()) {

						$data = [
							"list" => $extra_block_walletdespesa_cashtype_option,
							"walletcashtype" => $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE")
						];

						Send($data);

					} else {

						Notifier::Add("Erro ao carregar os dados da espécie.", Notifier::NOTIFIER_ERROR);
						Send(null);
					}

			// 		break;
			// }

        } else {

            Notifier::Add("Erro ao cadastrar espécie!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }
	break;

	case "walletcashtype_edit":

		WalletcashtypeFormEdit('EXTRA_BLOCK_CASHTYPE_FORM', 'Erro ao carregar espécie!');
	break;

	case "walletcashtype_cancel":

		WalletcashtypeFormCancel('BLOCK_CASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "walletcashtype_save":

		WalletcashtypeFormSave('walletcashtype', 'BLOCK_CASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "walletcashtype_delete":

		$id_walletcashtype = $_POST['id_walletcashtype'];

		$walletcashtype = new WalletCashType();

		if ($walletcashtype->isCashtypeInUse($id_walletcashtype)) {

			Notifier::Add("Espécie em uso não pode ser removido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$walletcashtype->Read($id_walletcashtype);

		if ($row = $walletcashtype->getResult()) {

			$wallet = new Wallet();

			$id_wallet = $row['id_wallet'];

			if ($wallet->isMyWallet($id_wallet) == false) {

				Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			if ($walletcashtype->Delete($id_walletcashtype)) {

				$walletcashtype->getList($id_wallet);

				$extra_block_walletdespesa_cashtype_option = "";

				$tplWallet = new View('templates/wallet');

				while ($row = $walletcashtype->getResult()) {

					$row['selected'] = "";

					if ($row['id_walletcashtype'] == $id_walletcashtype) {

						$row['selected'] = "selected";
					}

					$extra_block_walletdespesa_cashtype_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
				}

				Notifier::Add("Espécie excluída com sucesso!", Notifier::NOTIFIER_DONE);
				Send($extra_block_walletdespesa_cashtype_option);

			} else {

				Notifier::Add("Erro ao excluir espécie!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "walletcashtype_popup_new":

		$id_wallet = $_POST["id_wallet"];

		$tplWallet = new View("templates/wallet");

		$data = [
			"id_wallet" => $id_wallet
		];

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE_NEW"));

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}