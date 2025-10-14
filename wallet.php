<?php

use database\Notifier;
use database\View;
use database\Wallet;
use database\WalletSector;
use database\WalletCashType;
use database\Calc;

require "./inc/config.inc.php";
require "./inc/authorization.php";

function WalletDespesaFormEdit($block, $message_error) {

	$id_walletdespesa = $_POST['id_walletdespesa'];

	$tplWallet = new View('templates/wallet');

	$wallet = new Wallet();

	$wallet->ReadDespesa($id_walletdespesa);

	if ($row = $wallet->getResult()) {

		if ($block == "EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETSECTOR") {

			$row['extra_block_walletdespesa_sector_option'] = "";

			$walletSector = new WalletSector();

			$walletSector->getList($row['id_wallet']);

			while ($rowSector = $walletSector->getResult()) {

				$rowSector['selected'] = "";

				if ($rowSector['id_walletsector'] == $row['id_walletsector']) {

					$rowSector['selected'] = "selected";
				}

				$row['extra_block_walletdespesa_sector_option'] .= $tplWallet->getContent($rowSector, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
			}
		}

		if ($block == "EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETCASHTYPE") {

			$row['extra_block_walletdespesa_cashtype_option'] = "";

			$walletCashtype = new WalletCashType();

			$walletCashtype->getList($row['id_wallet']);

			while ($rowCashtype = $walletCashtype->getResult()) {

				$rowCashtype['selected'] = "";

				if ($rowCashtype['id_walletcashtype'] == $row['id_walletcashtype']) {

					$rowCashtype['selected'] = "selected";
				}

				$row['extra_block_walletdespesa_cashtype_option'] .= $tplWallet->getContent($rowCashtype, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
			}
		}

		$row = Wallet::FormatFieldsDespesa($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WalletDespesaFormCancel($block, $message_error) {

	$id_walletdespesa = $_POST['id_walletdespesa'];

	$tplWallet = new View('templates/wallet');

	$wallet = new Wallet();
	$wallet->ReadDespesa($id_walletdespesa);

	if ($row = $wallet->getResult()) {

		$row = Wallet::FormatFieldsDespesa($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletDespesaFormSave($field, $block, $message_error) {

	$id_walletdespesa = $_POST['id_walletdespesa'];
	$value = $_POST['value'];

	$data = [
		'id_walletdespesa' => (int) $id_walletdespesa,
		'field' => $field,
		'value' => $value,
	];

	$wallet = new Wallet();

	$wallet->UpdateDespesa($data);

	$tplWallet = new View('templates/wallet');

	$wallet->ReadDespesa($id_walletdespesa);

	if ($row = $wallet->getResult()) {

		$row = Wallet::FormatFieldsDespesa($row);

		$data = [
			"data" => $tplWallet->getContent($row, $block),
			"walletdespesa" => $tplWallet->getContent($row, "EXTRA_BLOCK_EXPENSE")
		];

		if ($field == 'valor') {

			$data['saldo'] = WalletGetSaldo($row['id_wallet']);
		}

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletReceitaFormEdit($block, $message_error) {

	$id_walletreceita = $_POST['id_walletreceita'];

	$tplWallet = new View('templates/wallet');

	$wallet = new Wallet();

	$wallet->ReadReceita($id_walletreceita);

	if ($row = $wallet->getResult()) {

		$row = Wallet::FormatFieldsReceita($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function WalletReceitaFormCancel($block, $message_error) {

	$id_walletreceita = $_POST['id_walletreceita'];

	$tplWallet = new View('templates/wallet');

	$wallet = new Wallet();
	$wallet->ReadReceita($id_walletreceita);

	if ($row = $wallet->getResult()) {

		$row = Wallet::FormatFieldsReceita($row);

		Send($tplWallet->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletReceitaFormSave($field, $block, $message_error) {

	$id_walletreceita = $_POST['id_walletreceita'];
	$value = $_POST['value'];

	$data = [
		'id_walletreceita' => (int) $id_walletreceita,
		'field' => $field,
		'value' => $value,
	];

	$wallet = new Wallet();

	$wallet->UpdateReceita($data);

	$tplWallet = new View('templates/wallet');

	$wallet->ReadReceita($id_walletreceita);

	if ($row = $wallet->getResult()) {

		$row = Wallet::FormatFieldsReceita($row);

		$data = [
			"data" => $tplWallet->getContent($row, $block),
			"walletreceita" => $tplWallet->getContent($row, "EXTRA_BLOCK_RECEITA")
		];

		if ($field == 'valor') {

			$data['saldo'] = WalletGetSaldo($row['id_wallet']);
		}

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function WalletGetSaldo($id_wallet) {

	$wallet = new Wallet();

	$wallet->Read($id_wallet);

	if ($row = $wallet->getResult()) {

		$tplWallet = new View('templates/wallet');

		$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

		if ($row['saldo'] < 0) {

			return $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

		} else {

			return $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
		}
	}
}

function Walletfilter($id_wallet, $dataini, $intervalo, $datafim, $id_walletcashtype, $id_walletsector) {

	if ($intervalo == false) {

		$datafim = $dataini;
	}

	$wallet = new Wallet();

	if ($wallet->isMyWallet($id_wallet) == false) {

		Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$mes = [
		'Janeiro',
		'Fevereiro',
		'Março',
		'Abril',
		'Maio',
		'Junho',
		'Julho',
		'Agosto',
		'Setembro',
		'Outubro',
		'Novembro',
		'Dezembro'
	];

	$cashtype = null;

	if(!is_null($id_walletcashtype)) {

		$wcash = new WalletCashType();

		$wcash->Read($id_walletcashtype);

		if ($row = $wcash->getResult()) {

			$cashtype = $row['walletcashtype'];
		}
	}

	$sector = null;

	if(!is_null($id_walletsector)) {

		$walletSector = new WalletSector();

		$walletSector->Read($id_walletsector);

		if ($row = $walletSector->getResult()) {

			$sector = $row['walletsector'];
		}
	}

	$tplWallet = new View('templates/wallet');

	$description = $mes[date_format($dataini, 'm') - 1] . ", " . date_format($dataini, 'Y');

	if ($intervalo) {

		$data_filter = [
			"id_wallet" => $id_wallet,
			"filter_01" => " a " . $mes[date_format($datafim, 'm') - 1] . ", " . date_format($datafim, 'Y')
		];

		$description .= $tplWallet->getContent($data_filter, "EXTRA_BLOCK_WALLET_FILTER_01");
	}

	if ($sector) {

		$data_filter = [
			"id_wallet" => $id_wallet,
			"filter_02" => $sector
		];

		$description .= $tplWallet->getContent($data_filter, "EXTRA_BLOCK_WALLET_FILTER_02");
	}

	if ($cashtype) {

		$data_filter = [
			"id_wallet" => $id_wallet,
			"filter_03" => $cashtype
		];

		$description .= $tplWallet->getContent($data_filter, "EXTRA_BLOCK_WALLET_FILTER_03");
	}

	$wallet->DespesaSearchByDate($id_wallet, date_format($dataini, 'Y-m-d'), date_format($datafim, 'Y-m-d'), $id_walletcashtype, $id_walletsector);

	$wallet_despesa = 0;
	$extra_block_expense = "";

	if ($row = $wallet->getResult()) {

		do {

			$row = Wallet::FormatFieldsDespesa($row);

			$extra_block_expense.= $tplWallet->getContent($row, "EXTRA_BLOCK_EXPENSE");

			$wallet_despesa = Calc::Sum([
				$wallet_despesa,
				$row["valorpago"]
			]);

		} while ($row = $wallet->getResult());

	} else {

		$extra_block_expense = $tplWallet->getContent([], "EXTRA_BLOCK_EXPENSE_NONE");
	}

	$wallet->DespesaFutura($id_wallet);

	$wallet_despesafutura = 0;
	$extra_block_futureexpense = "";

	if ($row = $wallet->getResult()) {

		do {

			$row = Wallet::FormatFieldsDespesa($row);

			$extra_block_futureexpense.= $tplWallet->getContent($row, "EXTRA_BLOCK_FUTUREEXPENSE");

			$wallet_despesafutura = Calc::Sum([
				$wallet_despesafutura,
				$row["valor"]
			]);

		} while ($row = $wallet->getResult());

	} else {

		$extra_block_futureexpense = $tplWallet->getContent([], "EXTRA_BLOCK_FUTUREEXPENSE_NONE");
	}

	$wallet->ReceitaSearchByDate($id_wallet, date_format($dataini, 'Y-m-d'), date_format($datafim, 'Y-m-d'));

	$wallet_receita = 0;
	$extra_block_receita = "";

	if ($row = $wallet->getResult()) {

		do {
			$row = Wallet::FormatFieldsReceita($row);

			$extra_block_receita .= $tplWallet->getContent($row, "EXTRA_BLOCK_RECEITA");

			$wallet_receita = Calc::Sum([
				$wallet_receita,
				$row["valor"]
			]);

		} while ($row = $wallet->getResult());

	} else {

		$extra_block_receita = $tplWallet->getContent([], "EXTRA_BLOCK_RECEITA_NONE");
	}

	$resume = [];

	$wallet_saldo = Calc::Sum([
		$wallet_receita,
		-$wallet_despesa
	]);

	$resume["wallet_saldo_formatted"] = number_format($wallet_saldo, 2, ",", ".");

	if ($wallet_saldo < 0) {

		$resume['wallet_saldo'] = $tplWallet->getContent(["saldo_formatted" => $resume["wallet_saldo_formatted"]], "EXTRA_BLOCK_WALLET_NEGATIVESALDO");

	} else {

		$resume['wallet_saldo'] = $tplWallet->getContent(["saldo_formatted" => $resume["wallet_saldo_formatted"]], "EXTRA_BLOCK_WALLET_POSITIVESALDO");
	}

	$wallet->Read($id_wallet);

	if ($row = $wallet->getResult()) {

		$wallet = $row['wallet'];

		$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

		if ($row['saldo'] < 0) {

			$resume['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

		} else {

			$resume['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
		}
	}

	$resume["despesa_formatted"] = number_format($wallet_despesa,2,",",".");
	$resume["despesafutura_formatted"] = number_format($wallet_despesafutura,2,",",".");
	$resume["receita_formatted"] = number_format($wallet_receita,2,",",".");

	$extra_block_wallet_expense_container = $tplWallet->getContent([
		"extra_block_expense" => $extra_block_expense,
		"despesa_formatted" => $resume["despesa_formatted"]
	], "EXTRA_BLOCK_WALLET_EXPENSE_CONTAINER");

	$extra_block_wallet_futureexpense_container = $tplWallet->getContent([
		"extra_block_futureexpense" => $extra_block_futureexpense,
		"despesafutura_formatted" => $resume["despesafutura_formatted"]
	], "EXTRA_BLOCK_WALLET_FUTUREEXPENSE_CONTAINER");

	$extra_block_wallet_receita_container = $tplWallet->getContent([
		"extra_block_receita" => $extra_block_receita,
		"receita_formatted" => $resume["receita_formatted"]
	], "EXTRA_BLOCK_WALLET_RECEITA_CONTAINER");

	$data = [
		"id_wallet" => $id_wallet,
		"wallet" => $wallet,
		"extra_block_wallet_resume" => $tplWallet->getContent($resume, "EXTRA_BLOCK_WALLET_RESUME"),
		"extra_block_wallet_expense_container" => $extra_block_wallet_expense_container,
		"extra_block_wallet_futureexpense_container" => $extra_block_wallet_futureexpense_container,
		"extra_block_wallet_receita_container" => $extra_block_wallet_receita_container,
		"walletfilter_description" => $description
	];

	return $data;
}

switch($_POST['action']) {

	case "load":

		$id_wallet = $_POST['id_wallet'];

		$date = date_create(date('Y-m-d'));

		$data = Walletfilter($id_wallet, $date, false, null, null, null);

		$tplWallet = new View("templates/wallet");

		Send([
			"data" => $tplWallet->getContent($data, "BLOCK_PAGE"),
		]);

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$wallet->DespesaSearchByDate($id_wallet, $date, $date, null, null);

		$wallet_despesa = 0;
		$extra_block_expense = "";
		$despesa_notfound = "";

		if ($row = $wallet->getResult()) {

			$despesa_notfound = "hidden";

			do {
				$row = Wallet::FormatFieldsDespesa($row);

				$extra_block_expense.= $tplWallet->getContent($row, "EXTRA_BLOCK_EXPENSE");

				$wallet_despesa = Calc::Sum([
					$wallet_despesa,
					$row["valor"]
				]);

			} while ($row = $wallet->getResult());
		}

		$wallet->ReceitaSearchByDate($id_wallet, $date, $date);

		$wallet_receita = 0;
		$extra_block_receita = "";
		$receita_notfound = "";

		if ($row = $wallet->getResult()) {

			$receita_notfound = "hidden";

			do {
				$row = Wallet::FormatFieldsReceita($row);

				$extra_block_receita .= $tplWallet->getContent($row, "EXTRA_BLOCK_RECEITA");

				$wallet_receita = Calc::Sum([
					$wallet_receita,
					$row["valor"]
				]);

			} while ($row = $wallet->getResult());
		}

		$month = date('m');
		$year = date('Y');

		$mes = [
			'Janeiro',
			'Fevereiro',
			'Março',
			'Abril',
			'Maio',
			'Junho',
			'Julho',
			'Agosto',
			'Setembro',
			'Outubro',
			'Novembro',
			'Dezembro'
		];

		$data = [
			// "extra_block_walletdespesa_sector_option" => $extra_block_walletdespesa_sector_option,
			// "extra_block_walletdespesa_cashtype_option" => $extra_block_walletdespesa_cashtype_option,
			'id_wallet' => $id_wallet,
			"data" => $date,
			// "date_search" => date("Y-m"),
			"despesa_formatted" => number_format($wallet_despesa,2,",","."),
			"receita_formatted" => number_format($wallet_receita,2,",","."),
			'extra_block_expense' => $extra_block_expense,
			'extra_block_receita' => $extra_block_receita,
			'description' => $mes[$month - 1] . ", " . $year,
			'source' => 'wallet',
			'despesa_notfound' => $despesa_notfound,
			'receita_notfound' => $receita_notfound
		];

		$wallet_saldo = Calc::Sum([
			$wallet_receita,
			-$wallet_despesa
		]);

		$wallet_saldo_formatted = number_format($wallet_saldo, 2, ",", ".");

		if ($wallet_saldo < 0) {

			$data['wallet_saldo'] = $tplWallet->getContent(["saldo_formatted" => $wallet_saldo_formatted], "EXTRA_BLOCK_WALLET_NEGATIVESALDO");

		} else {

			$data['wallet_saldo'] = $tplWallet->getContent(["saldo_formatted" => $wallet_saldo_formatted], "EXTRA_BLOCK_WALLET_POSITIVESALDO");
		}

		$wallet->Read($id_wallet);

		if ($row = $wallet->getResult()) {

			$data['wallet'] = $row['wallet'];
		}

		$row['saldototal_formatted'] = number_format($row['saldo'], 2, ',', '.');

		if ($row['saldo'] < 0) {

			$data['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL");

		} else {

			$data['wallet_saldototal'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL");
		}

	break;

	case "walletdespesa_new":

		$date = $_POST['data'];
		$valor = $_POST['valor'];
		$description = $_POST['descricao'];
		$id_wallet = $_POST['id_wallet'];
		$id_walletcashtype = $_POST['id_walletcashtype'];
		$id_walletsector = $_POST['id_walletsector'];
		$pago = ($_POST['pago'] == "true"? true: false);
		$datapago = ($pago == true)? $date: null;
		$valorpago = ($pago == true)? $valor: 0;
		$parcelado = ($_POST['parcelado'] == "true"? true: false);
		$parcelas = ($parcelado == true)? $_POST['parcelas']: 1;

		$wallet = new Wallet();


		if ($parcelas == 1) {

			$id_walletdespesa = $wallet->CreateDespesa($id_wallet, $GLOBALS['authorized_id_entidade'], $date, $id_walletsector, $description, $id_walletcashtype, $valor, $datapago, $valorpago);

			$wallet->ReadDespesa($id_walletdespesa);

			if ($row = $wallet->getResult()) {

				$tplWallet = new View('templates/wallet');

				$row = Wallet::FormatFieldsDespesa($row);

				Notifier::Add("Despesa Registrada<br>" . $tplWallet->getContent($row, "EXTRA_BLOCK_EXPENSE_INFO"), Notifier::NOTIFIER_DONE);

			} else {

				Notifier::Add("Erro ao cadastrar despesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			$valor_parcela = Calc::Div($valor, $parcelas);

			$valor_total = Calc::Mult($valor_parcela, $parcelas);

			$valor_parcela_ultima = Calc::Sum([
				$valor_parcela,
				Calc::Sum([
					$valor,
					-$valor_total
				])
			]);

			$date_tmp = date_create($date);

			for ($index = 1; $index <= $parcelas; $index++) {

				if ($index < $parcelas) {

					$valor_tmp = $valor_parcela;
					$valorpago_tmp = ($pago == true)? $valor_parcela: 0;

				} else {

					$valor_tmp = $valor_parcela_ultima;
					$valorpago_tmp = ($pago == true)? $valor_parcela_ultima: 0;
				}

				$datapago_tmp = ($pago == true)? $date_tmp: null;

				$date_tmp_formatted = date_format($date_tmp, "Y-m-d");

				if ($datapago_tmp != null) {

					$datapago_tmp_formatted = date_format($datapago_tmp, "Y-m-d");

				} else {

					$datapago_tmp_formatted = null;
				}

				$id_walletdespesa = $wallet->CreateDespesa($id_wallet, $GLOBALS['authorized_id_entidade'], $date_tmp_formatted, $id_walletsector, $description, $id_walletcashtype, $valor_tmp, $datapago_tmp_formatted, $valorpago_tmp, "(" . $index . " de " . $parcelas . ")");

				$wallet->ReadDespesa($id_walletdespesa);

				if ($row = $wallet->getResult()) {

					$tplWallet = new View('templates/wallet');

					$row = Wallet::FormatFieldsDespesa($row);

					Notifier::Add("Despesa Registrada<br>" . $tplWallet->getContent($row, "EXTRA_BLOCK_EXPENSE_INFO"), Notifier::NOTIFIER_DONE);

				} else {

					Notifier::Add("Erro ao cadastrar despesa!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				$date_tmp->add(new DateInterval('P1M'));
			}

		}

		$data = [
			"saldo" => WalletGetSaldo($id_wallet)
		];

		Send($data);

	break;

	case "walletreceita_new":

		$date = $_POST['data'];
		$valor = $_POST['valor'];
		$description = $_POST['descricao'];
		$id_wallet = $_POST['id_wallet'];

		$wallet = new Wallet();
		$id_walletreceita = $wallet->CreateReceita($id_wallet, $GLOBALS['authorized_id_entidade'], $date, $description, $valor);

		$wallet->ReadReceita($id_walletreceita);

		if ($row = $wallet->getResult()) {

			$tplWallet = new View('templates/wallet');

			$row = Wallet::FormatFieldsReceita($row);

			// $data = [
			// 	"data" => $tplWallet->getContent($row, "EXTRA_BLOCK_RECEITA"),
			// 	"saldo" => WalletGetSaldo($id_wallet)
			// ];

			Notifier::Add("Receita Registrada<br>" . $tplWallet->getContent($row, "EXTRA_BLOCK_RECEITA_INFO"), Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Erro ao cadastrar receita!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "walletdespesa_delete":

		$id_walletdespesa = $_POST['id_walletdespesa'];

		$wallet = new Wallet();

		$wallet->ReadDespesa($id_walletdespesa);

		if ($row = $wallet->getResult()) {

			$id_wallet = $row['id_wallet'];

			$valor = 0;

			if ($row["datapago"] != null) {

				$valor = $row['valor'];
			}


		} else {

			Notifier::Add("Erro ao carregar dados da despesa.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($wallet->DeleteDespesa($id_walletdespesa)) {

			if ($valor != 0) {

				$wallet->UpdateSaldo($id_wallet, $valor);
			}

			Notifier::Add("Despesa removida com sucesso.", Notifier::NOTIFIER_DONE);
			// Send(WalletGetSaldo($id_wallet));
			Send([]);

		} else {

			Notifier::Add("Erro ao remover despesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "walletreceita_delete":

		$id_walletreceita = $_POST['id_walletreceita'];

		$wallet = new Wallet();

		$wallet->ReadReceita($id_walletreceita);

		if ($row = $wallet->getResult()) {

			$id_wallet = $row['id_wallet'];
			$valor = $row['valor'];

		} else {

			Notifier::Add("Erro ao carregar dados da receita.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($wallet->DeleteReceita($id_walletreceita)) {

			$wallet->UpdateSaldo($id_wallet, -$valor);

			Notifier::Add("Receita removida com sucesso.", Notifier::NOTIFIER_DONE);
			// Send(WalletGetSaldo($id_wallet));
			Send([]);

		} else {

			Notifier::Add("Erro ao remover receita!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "walletdespesa_data_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_DATA', 'Erro ao carregar data!');
	break;

	case "walletdespesa_data_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_DATA', 'Erro ao carregar data!');
	break;

	case "walletdespesa_data_save":

		WalletDespesaFormSave('data', 'BLOCK_WALLETDESPESA_DATA', 'Erro ao carregar data!');
	break;

	case "walletdespesa_datapago_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_DATAPAGO', 'Erro ao carregar data!');
	break;

	case "walletdespesa_datapago_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_DATAPAGO', 'Erro ao carregar data!');
	break;

	case "walletdespesa_datapago_save":

		WalletDespesaFormSave('datapago', 'BLOCK_WALLETDESPESA_DATAPAGO', 'Erro ao carregar data!');
	break;

	case "walletdespesa_sector_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETSECTOR', 'Erro ao carregar setor!');
	break;

	case "walletdespesa_sector_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_WALLETSECTOR', 'Erro ao carregar setor!');
	break;

	case "walletdespesa_sector_save":

		WalletDespesaFormSave('id_walletsector', 'BLOCK_WALLETDESPESA_WALLETSECTOR', 'Erro ao carregar setor!');
	break;

	case "walletdespesa_cashtype_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETCASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "walletdespesa_cashtype_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_WALLETCASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "walletdespesa_cashtype_save":

		WalletDespesaFormSave('id_walletcashtype', 'BLOCK_WALLETDESPESA_WALLETCASHTYPE', 'Erro ao carregar espécie!');
	break;

	case "walletdespesa_valor_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_valor_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_valor_save":

		WalletDespesaFormSave('valor', 'BLOCK_WALLETDESPESA_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_valorpago_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_VALORPAGO', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_valorpago_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_VALORPAGO', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_valorpago_save":

		WalletDespesaFormSave('valorpago', 'BLOCK_WALLETDESPESA_VALORPAGO', 'Erro ao carregar valor!');
	break;

	case "walletdespesa_walletdespesa_edit":

		WalletDespesaFormEdit('EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETDESPESA', 'Erro ao carregar descrição!');
	break;

	case "walletdespesa_walletdespesa_cancel":

		WalletDespesaFormCancel('BLOCK_WALLETDESPESA_WALLETDESPESA', 'Erro ao carregar descrição!');
	break;

	case "walletdespesa_walletdespesa_save":

		WalletDespesaFormSave('walletdespesa', 'BLOCK_WALLETDESPESA_WALLETDESPESA', 'Erro ao carregar descrição!');
	break;

	case "walletreceita_data_edit":

		WalletReceitaFormEdit('EXTRA_BLOCK_WALLETRECEITA_FORM_DATA', 'Erro ao carregar data!');
	break;

	case "walletreceita_data_cancel":

		WalletReceitaFormCancel('BLOCK_WALLETRECEITA_DATA', 'Erro ao carregar data!');
	break;

	case "walletreceita_data_save":

		WalletReceitaFormSave('data', 'BLOCK_WALLETRECEITA_DATA', 'Erro ao carregar data!');
	break;

	case "walletreceita_valor_edit":

		WalletReceitaFormEdit('EXTRA_BLOCK_WALLETRECEITA_FORM_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletreceita_valor_cancel":

		WalletReceitaFormCancel('BLOCK_WALLETRECEITA_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletreceita_valor_save":

		WalletReceitaFormSave('valor', 'BLOCK_WALLETRECEITA_VALOR', 'Erro ao carregar valor!');
	break;

	case "walletreceita_walletreceita_edit":

		WalletReceitaFormEdit('EXTRA_BLOCK_WALLETRECEITA_FORM_WALLETRECEITA', 'Erro ao carregar descrição!');
	break;

	case "walletreceita_walletreceita_cancel":

		WalletReceitaFormCancel('BLOCK_WALLETRECEITA_WALLETRECEITA', 'Erro ao carregar descrição!');
	break;

	case "walletreceita_walletreceita_save":

		WalletReceitaFormSave('walletreceita', 'BLOCK_WALLETRECEITA_WALLETRECEITA', 'Erro ao carregar descrição!');
	break;

	case "wallet_filter":

		$id_wallet = $_POST['id_wallet'];
		$dataini = date_create($_POST['dataini']);
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$datafim = date_create($_POST['datafim']);
		$id_walletcashtype = ($_POST['especie'] == "false")? null : $_POST['id_walletcashtype'];
		$id_walletsector = ($_POST['setor'] == "false")? null : $_POST['id_walletsector'];

		$data = Walletfilter($id_wallet, $dataini, $intervalo, $datafim, $id_walletcashtype, $id_walletsector);

		Send($data);

		break;

	case "walletdespesa_shownew":

		$id_wallet = $_POST['id_wallet'];

		$tplWallet = new View('templates/wallet');

		$walletSector = new WalletSector();

		$walletSector->getList($id_wallet);

		$extra_block_walletdespesa_sector_option = "";

		while ($row = $walletSector->getResult()) {

			$extra_block_walletdespesa_sector_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
		}

		$walletCashtype = new WalletCashType();

		$walletCashtype->getList($id_wallet);

		$extra_block_walletdespesa_cashtype_option = "";

		while ($row = $walletCashtype->getResult()) {

			$extra_block_walletdespesa_cashtype_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
		}

		$walletdespesanew_parcelado = "";

		for ($index = 2; $index <= 180; $index++) {

			$data = [
				"valor" => $index,
				"valor_desc" => $index . " x"
			];

			$walletdespesanew_parcelado .= $tplWallet->getContent($data, "EXTRA_BLOCK_WALLETDESPESA_PARCELADO_OPTION");
		}

		$data = [
			"id_wallet" => $id_wallet,
			"wallet" => "",
			"data" => date('Y-m-d'),
			"walletdespesanew_parcelado" => $walletdespesanew_parcelado,
			"extra_block_walletdespesa_sector_option" => $extra_block_walletdespesa_sector_option,
			"extra_block_walletdespesa_cashtype_option" => $extra_block_walletdespesa_cashtype_option
		];

		$wallet = New Wallet();

		$wallet->Read($id_wallet);

		if ($row = $wallet->getResult()) {

			$data['wallet'] = $row['wallet'];
		}

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETDESPESA_NEW"));

		break;

	case "walletreceita_popup_new":

		$id_wallet = $_POST['id_wallet'];

		$tplWallet = new View('templates/wallet');

		$data = [
			"id_wallet" => $id_wallet,
			"wallet" => "",
			"data" => date('Y-m-d'),
		];

		$wallet = New Wallet();

		$wallet->Read($id_wallet);

		if ($row = $wallet->getResult()) {

			$data['wallet'] = $row['wallet'];
		}

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLETRECEITA_NEW"));

		break;

	case 'walletdespesa_edition':

		$id_walletdespesa = $_POST['id_walletdespesa'];

		$wallet = new Wallet();

		$wallet->ReadDespesa($id_walletdespesa);

		if ($row = $wallet->getResult()) {

			$tplWallet = new View('templates/wallet');

			$row = Wallet::FormatFieldsDespesa($row);

			$wallet->Read($row["id_wallet"]);

			if ($rowWallet = $wallet->getResult()) {

				$row["wallet"] = $rowWallet["wallet"];
			}

			$row["extra_block_walletdespesa_payment_edit"] = "";

			if ($row["datapago"] != null) {

				$row["extra_block_walletdespesa_payment_edit"] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_PAYMENT_EDIT");
			}

			Send($tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_EDIT"));

		} else {

			Notifier::Add("Erro ao carregar dados da despesa.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case 'walletreceita_edition':

		$id_walletreceita = $_POST['id_walletreceita'];

		$wallet = new Wallet();

		$wallet->ReadReceita($id_walletreceita);

		if ($row = $wallet->getResult()) {

			$tplWallet = new View('templates/wallet');

			$row = Wallet::FormatFieldsReceita($row);

			$wallet->Read($row["id_wallet"]);

			if ($rowWallet = $wallet->getResult()) {

				$row["wallet"] = $rowWallet["wallet"];
			}

			Send($tplWallet->getContent($row, "EXTRA_BLOCK_WALLETRECEITA_EDIT"));

		} else {

			Notifier::Add("Erro ao carregar dados da receita.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "wallet_expense_filter":

		$id_wallet = $_POST['id_wallet'];

		$datestart = $_POST['datestart'];

		if ($_POST['dateend_sel'] == "true") {

			$dateend_sel = "checked";
			$select_datafim = "";

		} else {

			$dateend_sel = "";
			$select_datafim = "disabled";
		}


		$dateend = $_POST['dateend'];

		if ($_POST['sector_sel'] == "true") {

			$sector_sel = "checked";
			$select_id_walletsector = "";

		} else {

			$sector_sel = "";
			$select_id_walletsector = "disabled";
		}


		$sector = $_POST['sector'];

		if ($_POST['cashtype_sel'] == "true") {

			$cashtype_sel = "checked";
			$select_id_walletcashtype = "";

		} else {

			$cashtype_sel = "";
			$select_id_walletcashtype = "disabled";
		}

		$cashtype = $_POST['cashtype'];

		$wallet = new Wallet();

		if ($wallet->isMyWallet($id_wallet) == false) {

			Notifier::Add("Acesso negado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$tplWallet = new View("templates/wallet");

		$month = date('m');
		$year = date('Y');

		$mes = [
			'Janeiro',
			'Fevereiro',
			'Março',
			'Abril',
			'Maio',
			'Junho',
			'Julho',
			'Agosto',
			'Setembro',
			'Outubro',
			'Novembro',
			'Dezembro'
		];

		$walletCashtype = new WalletCashType();

		$walletCashtype->getList($id_wallet);

		$extra_block_walletdespesa_cashtype_option = "";

		while ($row = $walletCashtype->getResult()) {

			$row['selected'] = "";

			if ($cashtype == $row['id_walletcashtype']) {

				$row['selected'] = "selected";
			}

			$extra_block_walletdespesa_cashtype_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION");
		}

		$walletSector = new WalletSector();

		$walletSector->getList($id_wallet);

		$extra_block_walletdespesa_sector_option = "";

		while ($row = $walletSector->getResult()) {

			$row['selected'] = "";

			if ($sector == $row['id_walletsector']) {

				$row['selected'] = "selected";
			}

			$extra_block_walletdespesa_sector_option .= $tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION");
		}

		if ($datestart == null) {

			$datestart = date("Y-m");
		}

		if ($dateend == null) {

			$dateend = date("Y-m");
		}

		$data = [
			"extra_block_walletdespesa_sector_option" => $extra_block_walletdespesa_sector_option,
			"extra_block_walletdespesa_cashtype_option" => $extra_block_walletdespesa_cashtype_option,
			'id_wallet' => $id_wallet,
			"datestart" => $datestart,
			"dateend_sel" => $dateend_sel,
			"select_datafim" => $select_datafim,
			"dateend" => $dateend,
			"select_id_walletsector" => $select_id_walletsector,
			"sector_sel" => $sector_sel,
			"cashtype_sel" => $cashtype_sel,
			"select_id_walletcashtype" => $select_id_walletcashtype,
		];

		// $wallet->Read($id_wallet);

		// if ($row = $wallet->getResult()) {

		// 	$data['wallet'] = $row['wallet'];
		// }

		// $row['saldo_formatted'] = number_format($row['saldo'], 2, ',', '.');

		// if ($row['saldo'] < 0) {

		// 	$data['wallet_saldo'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_NEGATIVESALDO");

		// } else {

		// 	$data['wallet_saldo'] = $tplWallet->getContent($row, "EXTRA_BLOCK_WALLET_POSITIVESALDO");
		// }

		Send($tplWallet->getContent($data, "EXTRA_BLOCK_POPUP_WALLET_FILTER"));

	break;

	case "walletdespesa_popup_payment":

		$id_walletdespesa = $_POST["id_walletdespesa"];

		$wallet = new Wallet();

		$wallet->ReadDespesa($id_walletdespesa);

		if ($row = $wallet->getResult()) {

			$tplWallet = new View("templates/wallet");

			$row = Wallet::FormatFieldsDespesa($row);

			Send($tplWallet->getContent($row, "EXTRA_BLOCK_WALLETDESPESA_PAYMENT"));

		} else {

			Notifier::Add("Erro ao carregar dados da despesa.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "walletdespesa_payment":

		$id_wallet = $_POST['id_wallet'];
		$id_walletdespesa = $_POST['id_walletdespesa'];
		$datapago = $_POST['datapago'];
		$valorpago = $_POST['valorpago'];

		$wallet = new Wallet();

		$wallet->setPaymentDespesa($id_wallet, $id_walletdespesa, $datapago, $valorpago);

		$data = [
			"saldo" => WalletGetSaldo($id_wallet)
		];

		Send($data);

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}