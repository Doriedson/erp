<?php

use database\Notifier;
use database\View;
use database\Collaborator;
use database\Wallet;

require "./inc/config.inc.php";
require "./inc/authorization.php";

switch($_POST['action']) {

	case "load":

		$tplUser = new View("templates/user");

		$tplWallets = new View("templates/wallets");
		$tplWallet = new View("templates/wallet");

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

		$data = [
			'id_entidade' => $GLOBALS['authorized_id_entidade'],
			'nome' => $GLOBALS['authorized_nome'],
			"wallets" => $tplWallets->getContent($data, "BLOCK_WALLETS_CONTAINER")
		];

		Send($tplUser->getContent($data, "BLOCK_PAGE"));
	break;

	case "collaborator_show_password":

		$tplUser = new View("templates/user");

		$data = [
			"nome" => $GLOBALS["authorized_nome"]
		];

		Send($tplUser->getContent($data, "EXTRA_BLOCK_COLLABORATOR_PASS"));

	break;

	case "collaborator_password":

		$old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $new_pass_confirm = $_POST['new_pass_confirm'];

		if ($new_pass != $new_pass_confirm) {

			Notifier::Add("Nova senha não confere!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$collaborator = new Collaborator();

		if ($collaborator->Read($GLOBALS['authorized_id_entidade'])) {

			$row = $collaborator->getResult();

			if (password_verify($old_pass, $row['hash'])) {

				$collaborator->setPass($GLOBALS['authorized_id_entidade'],$new_pass);

				Notifier::Add("Senha alterada com sucesso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				Notifier::Add("Senha atual inválida, digite novamente!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar cadastro de colaborador!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	default:

		Unauthorized(); // login is not set
	break;
}