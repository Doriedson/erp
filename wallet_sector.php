<?php

use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Wallet;
use App\Legacy\WalletCashType;
use App\Legacy\WalletSector;

require "inc/config.inc.php";
require "inc/authorization.php";

function WalletsectorFormEdit($block, $message_error) {

	$id_walletsector = $_POST['id_walletsector'];

	$tplWalletsector = new View('wallet');

	$walletsector = new WalletSector();
	$walletsector->Read($id_walletsector);

	if ($row = $walletsector->getResult()) {

		// $row = WalletSector::FormatFields($row);

		Send($tplWalletsector->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WalletsectorFormCancel($block, $message_error) {

	$id_walletsector = $_POST['id_walletsector'];

	$tplWalletsector = new View('wallet');

	$walletsector = new WalletSector();
	$walletsector->Read($id_walletsector);

	if ($row = $walletsector->getResult()) {

		// $row = WalletSector::FormatFields($row);

		Send($tplWalletsector->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletsectorFormSave($field, $block, $message_error) {

	$id_walletsector = $_POST['id_walletsector'];
	$value = $_POST['value'];

	$data = [
		'id_walletsector' => (int) $id_walletsector,
		'field' => $field,
		'value' => $value,
	];

	$walletsector = new WalletSector();

	$walletsector->Read($id_walletsector);

	if ($row = $walletsector->getResult()) {

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

	$walletsector->Update($data);

	$tplWalletsector = new View('wallet');

	$walletsector->Read($id_walletsector);

	if ($row = $walletsector->getResult()) {

		$walletsector->getList($row['id_wallet']);

		$extra_block_walletdespesa_sector_option = "";

		$tplWallet = new View('wallet');

		while ($row_list = $walletsector->getResult()) {

			$row_list['selected'] = "";

			if ($row_list['id_walletsector'] == $id_walletsector) {

				$row_list['selected'] = "selected";
			}

			$extra_block_walletdespesa_sector_option .= $tplWallet->getContent($row_list, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
		}

		Send([
			"data" => $tplWalletsector->getContent($row, $block),
			"list" => $extra_block_walletdespesa_sector_option
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

		$walletsector = new WalletSector();

		$walletsector->getList($id_wallet);

		$tplWallet = new View('wallet');

		$sector = "";

		$walletsector_notfound = "hidden";

		if ($row = $walletsector->getResult()) {

			do {

				$sector.= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR");

			} while ($row = $walletsector->getResult());

		} else {

			$walletsector_notfound = "";
		}

		$data = [
			"id_wallet" => $id_wallet,
			"extra_block_walletdespesa_sector" => $sector,
			"walletsector_notfound" => $walletsector_notfound
		];

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR"));

		break;

	case "walletsector_add":

		$id_wallet = $_POST['id_wallet'];
		$walletsector = $_POST['walletsector'];

		$tplWallet = new View('wallet');
		$sector = new WalletSector();

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado! $id_wallet", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$id_walletsector = $sector->Create($id_wallet, $walletsector);

        if ($id_walletsector) {

			$sector->getList($id_wallet);

			$extra_block_walletdespesa_sector_option = "";

			while ($row = $sector->getResult()) {

				$row['selected'] = "";

				if ($row['id_walletsector'] == $id_walletsector) {

					$row['selected'] = "selected";
				}

				$extra_block_walletdespesa_sector_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
			}

			$sector->Read($id_walletsector);

			if ($row = $sector->getResult()) {

				$data = [
					"list" => $extra_block_walletdespesa_sector_option,
					"walletsector" => $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR")
				];

				Send($data);

			} else {

				Notifier::Add("Erro ao carregar os dados do setor.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

        } else {

            Notifier::Add("Erro ao cadastrar setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }

		break;

	case "walletsector_edit":

		WalletsectorFormEdit('EXTRA_BLOCK_SECTOR_FORM', 'Erro ao carregar setor!');
	break;

	case "walletsector_cancel":

		WalletsectorFormCancel('BLOCK_SECTOR', 'Erro ao carregar setor!');
	break;

	case "walletsector_save":

		WalletsectorFormSave('walletsector', 'BLOCK_SECTOR', 'Erro ao carregar setor!');
	break;

	case "walletsector_delete":

		$id_walletsector = $_POST['id_walletsector'];

		$walletsector = new WalletSector();

		if ($walletsector->isSectorInUse($id_walletsector)) {

			Notifier::Add("Setor em uso não pode ser removido!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$walletsector->Read($id_walletsector);

			if ($row = $walletsector->getResult()) {

				$wallet = new Wallet();

				$id_wallet = $row['id_wallet'];

				if ($wallet->isMyWallet($id_wallet) == false) {

					Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				if ($walletsector->Delete($id_walletsector)) {

					$walletsector->getList($id_wallet);

					$extra_block_walletdespesa_sector_option = "";

					$tplWallet = new View('wallet');

					while ($row = $walletsector->getResult()) {

						$row['selected'] = "";

						if ($row['id_walletsector'] == $id_walletsector) {

							$row['selected'] = "selected";
						}

						$extra_block_walletdespesa_sector_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
					}

					Notifier::Add("Setor excluído com sucesso!", Notifier::NOTIFIER_DONE);
					Send($extra_block_walletdespesa_sector_option);

				} else {

					Notifier::Add("Erro ao excluir setor!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao ler dados do setor!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "walletsector_popup_new":

		$id_wallet = $_POST["id_wallet"];

		$data = [
			"id_wallet" => $id_wallet,
		];

		$tplWallet = new View('wallet');

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR_NEW"));

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}