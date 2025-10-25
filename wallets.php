<?php

use App\Legacy\Collaborator;
use App\Legacy\Entity;
use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Wallet;

require "./inc/config.inc.php";
require "./inc/authorization.php";

function WalletsFormEdit($block, $message_error) {

	$id_wallet = $_POST['id_wallet'];

	$tplWallet = new View('wallets');

	$wallet = new Wallet();

    if ($wallet->isMyWallet($id_wallet) == false) {

        Notifier::Add("Permissão negada!", Notifier::NOTIFIER_ERROR);
		Send(null);
    }

	$wallet->Read($id_wallet);

	if ($row = $wallet->getResult()) {

		// $row = Wallet::FormatFields($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WalletsFormCancel($block, $message_error) {

	$id_wallet = $_POST['id_wallet'];

	$tplWallet = new View('wallets');

	$wallet = new Wallet();
	$wallet->Read($id_wallet);

	if ($row = $wallet->getResult()) {

		// $row = Wallet::FormatFields($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletsFormSave($field, $block, $message_error) {

	$id_wallet = $_POST['id_wallet'];
	$value = $_POST['value'];

	$data = [
		'id_wallet' => (int) $id_wallet,
		'field' => $field,
		'value' => $value,
	];

	$wallet = new Wallet();

    if ($wallet->isMyWallet($id_wallet) == false) {

        Notifier::Add("Permissão negada!", Notifier::NOTIFIER_ERROR);
		Send(null);
    }

	$wallet->Update($data);

	$tplWallet = new View('wallets');

	$wallet->Read($id_wallet);

	if ($row = $wallet->getResult()) {

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch($_POST['action']) {

	case "load":

		$tplWallets = new View("wallets");
		$tplWallet = new View("wallet");

        $wallet = new Wallet();

        $wallet->getWallets();

        $data['wallet_notfound'] = "";
        $data['extra_block_wallet'] = "";

        if ($row = $wallet->getResult()) {

            $data['wallet_notfound'] = "hidden";

            do {

				$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

				if ($row['saldo'] < 0) {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

				} else {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
				}

                $data['extra_block_wallet'] .= $tplWallets->getContent($row, "EXTRA_BLOCK_WALLET");

            } while ($row = $wallet->getResult());

        }

		$wallet->getWalletsShared();

		if ($row = $wallet->getResult()) {

            $data['wallet_notfound'] = "hidden";

            do {

				$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

				if ($row['saldo'] < 0) {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

				} else {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
				}

                $data['extra_block_wallet'] .= $tplWallets->getContent($row, "EXTRA_BLOCK_WALLETSHARE");

            } while ($row = $wallet->getResult());

        }

        Send($tplWallets->getContent($data, "BLOCK_PAGE"));

	    break;

	case "wallet_description_edit":

		WalletsFormEdit('EXTRA_BLOCK_FORM_DESCRIPTION', 'Erro ao carregar descrição!');
	    break;

	case "wallet_description_cancel":

		WalletsFormCancel('BLOCK_DESCRIPTION', 'Erro ao carregar descrição!');
	    break;

	case "wallet_description_save":

		WalletsFormSave('wallet', 'BLOCK_DESCRIPTION', 'Erro ao carregar descrição!');
    	break;

	case "wallet_new":

		$wallet = new Wallet();

		if ($id_wallet = $wallet->Create()) {

			$wallet->getWallet($id_wallet);

			if ($row = $wallet->getResult()) {

				$tplWallets = new View('wallets');
				$tplWallet = new View('wallet');

				$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

				if ($row['saldo'] < 0) {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

				} else {

					$row['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
				}

				Send($tplWallets->getContent($row, "EXTRA_BLOCK_WALLET"));

			} else {

				Notifier::Add("Erro ao ler informações da nova carteira.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao criar carteira.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "wallet_del":

		$id_wallet = $_POST['id_wallet'];

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($wallet->Delete($id_wallet)) {

			Notifier::Add("Carteira removida com sucesso", Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Erro ao remover carteira.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "wallet_popup_sharing":

		$id_wallet = $_POST['id_wallet'];

		$wallet = new Wallet();
		$collaborator = new Collaborator();
		$tplWallet = new View('wallets');

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$collaborator->getList();

		$list = "";

		if ($row = $collaborator->getResult()) {

			do {

				$row['id_wallet'] = $id_wallet;

				if ($row['id_entidade'] != $GLOBALS['authorized_id_entidade']) {

					$wallet->ReadSharing($row['id_entidade'], $id_wallet);

					$row['shared'] = "";

					if ($row_sharing = $wallet->getResult()) {

						$row['shared'] = "checked";
					}

					$list .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETSHARING_LIST");
				}

			} while ($row = $collaborator->getResult());

			if ($list == "") {

				$list = $tplWallet->getContent([], "EXTRA_BLOCK_WALLETSHARING_LIST_NONE");
			}

		} else {

			$list = $tplWallet->getContent([], "EXTRA_BLOCK_WALLETSHARING_LIST_NONE");
		}

		$data = [
			"extra_block_walletsharing_list" => $list
		];

		$wallet->Read($id_wallet);

		if ($rowWallet = $wallet->getResult()) {

			$data["wallet"] = $rowWallet["wallet"];
		}

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLET_SHARING"));

		break;

	case "walletsharing_del":

		$id_wallet = $_POST['id_wallet'];

		$wallet = new Wallet();

		if ($wallet->DeleteSharing($id_wallet, $GLOBALS['authorized_id_entidade'])) {

			Notifier::Add("Carteira removida com sucesso", Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Erro ao remover carteira.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "walletsharing_share":

		$id_wallet = $_POST['id_wallet'];
		$id_entidade = $_POST['id_entidade'];

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$wallet->CreateSharing($id_wallet, $id_entidade);

		$entity = new Entity();

		$entity->Read($id_entidade);

		$name = "";

		if ($row = $entity->getResult()) {

			$name = $row['nome'];
		}

		Notifier::Add("Carteira compartilhada com $name.", Notifier::NOTIFIER_DONE);
		Send([]);

		break;

	case "walletsharing_notshare":

		$id_wallet = $_POST['id_wallet'];
		$id_entidade = $_POST['id_entidade'];

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$wallet->DeleteSharing($id_wallet, $id_entidade);

		$entity = new Entity();

		$entity->Read($id_entidade);

		$name = "";

		if ($row = $entity->getResult()) {

			$name = $row['nome'];
		}

		Notifier::Add("Removido o Compartilhamento da carteira com $name.", Notifier::NOTIFIER_DONE);
		Send([]);

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}