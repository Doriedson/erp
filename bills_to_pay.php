<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\BillsToPay;
use database\BillsToPaySector;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR);

function BillstopayFormEdit($block, $message_error) {

	$id_contasapagar = $_POST['id_contasapagar'];

	$tplBillstopay = new View('templates/bills_to_pay');

	$billstopay = new BillsToPay();
	$billstopay->Read($id_contasapagar);

	if ($row = $billstopay->getResult()) {

		$row = BillsToPay::FormatFields($row);

		Send($tplBillstopay->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function BillstopayFormCancel($block, $message_error) {

	$id_contasapagar = $_POST['id_contasapagar'];

	$tplBillstopay = new View('templates/bills_to_pay');

	$billstopay = new BillsToPay();
	$billstopay->Read($id_contasapagar);

	if ($row = $billstopay->getResult()) {

		$row = BillsToPay::FormatFields($row);

		Send($tplBillstopay->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function BillstopayFormSave($field, $block, $message_error) {

	$id_contasapagar = $_POST['id_contasapagar'];
	$value = $_POST['value'];

	$data = [
		'id_contasapagar' => (int) $id_contasapagar,
		'field' => $field,
		'value' => $value,
	];

	$billstopay = new BillsToPay();

	$billstopay->Update($data);

	$tplBillsToPay = new View('templates/bills_to_pay');

	$billstopay->Read($id_contasapagar);

	if ($row = $billstopay->getResult()) {

		$row = BillsToPay::FormatFields($row);
		$bill = getBillsToPay($row);
		$status = "";

		if ($field == 'vencimento') {

			if ($row['datapago'] != null) {

				$status = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_PAID");

			} else {

				if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

					$status = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_OVERDUE");

				} else {

					$status = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_PENDING");
				}
			}
		}

		Send([
			"data" => $tplBillsToPay->getContent($row, $block),
			"bill" => $bill,
			"status" => $status
		]);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function getBillsToPay($row) {

	$tplBillsToPay = new View('templates/bills_to_pay');

	$row = BillsToPay::FormatFields($row);

	$bill = "";

	if ($row['datapago'] != null) {

		$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

	} else {

		if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

			$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

		} else {

			$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
		}
	}

	return $bill;
}

switch ($_POST['action']) {

	case "load":

		// $billsSector = new BillsToPaySector();

		// $billsSector->getList();

		// $sectorList = "";

		// while ($row = $billsSector->getResult()) {

		// 	$sectorList .= "<option value='" . $row['id_contasapagarsetor'] . "'>" . $row['contasapagarsetor'] . "</option>";
		// }

		$billsToPay = new BillsToPay();

		$billsToPay->getPendingList();

		$bills = "";

		$tplBillsToPay = new View('templates/bills_to_pay');

		$billstopay_none = "hidden";

		if ($row = $billsToPay->getResult()) {

			do {

				$bills.= getBillsToPay($row);

			} while ($row = $billsToPay->getResult());

		} else {

			$billstopay_none = "";
		}

		$data = [
			'header' => '',
			'extra_block_billstopay' => $bills,
			'billstopay_none' => $billstopay_none
		];

		Send($tplBillsToPay->getContent($data, "BLOCK_PAGE"));

	break;

	case "billstopay_popup_new":

		$billsSector = new BillsToPaySector();

		$billsSector->getList();

		$sectorList = "";

		while ($row = $billsSector->getResult()) {

			$sectorList .= "<option value='" . $row['id_contasapagarsetor'] . "'>" . $row['contasapagarsetor'] . "</option>";
		}

		$tplBillsToPay = new View('templates/bills_to_pay');

		$data = [
			'data' => date('Y-m-d'),
			'setor' => $sectorList,
		];

		Send($tplBillsToPay->getContent($data, "EXTRA_BLOCK_BILLSTOPAY_POPUP_NEW"));

		break;

	case "billstopay_new":

		$data = [
			"id_entidade" => $GLOBALS['authorized_id_entidade'],
			"vencimento" => $_POST['vencimento'],
			"id_contasapagarsetor" => $_POST['setor'],
			"descricao" => $_POST['descricao'],
			"valor" => $_POST['valor'],
			"pago" => ($_POST['pago'] == "true"? true: false),
		];

		$billsToPay = new BillsToPay();

		if ($id_contasapagar = $billsToPay->Create($data)) {

			$billsToPay->Read($id_contasapagar);

			if($row = $billsToPay->getResult()) {

				$tplBillsToPay = new View('templates/bills_to_pay');

				$row = BillsToPay::FormatFields($row);

				if ($row['datapago'] != null) {

					$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

				} else {

					if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

						$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

					} else {

						$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
					}
				}

				Send($bill);

			} else {

				Notifier::Add("Erro ao ler conta cadastrada!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao cadastrar conta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "billstopay_popup_filter":

		$tplBillsToPay = new View('templates/bills_to_pay');

		$data["data"] = date('Y-m-d');

		$billsSector = new BillsToPaySector();

		$billsSector->getList();

		$sectorList = "";

		while ($row_sector = $billsSector->getResult()) {

			$sectorList.= "<option value='" . $row_sector['id_contasapagarsetor'] . "'>" . $row_sector['contasapagarsetor'] . "</option>";
		}

		$data["setor_lista"] = $sectorList;

		Send($tplBillsToPay->getContent($data, "EXTRA_BLOCK_POPUP_BILLSTOPAY_FILTER"));

		break;

	case "billstopay_search":

		$dataini = $_POST['dataini'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		$datafim = $_POST['datafim'];
		$chk_setor = ($_POST['chk_setor'] == "false")? false : true;
		$setor = $_POST['setor'];
		$chk_descricao = ($_POST['chk_descricao'] == "false")? false : true;
		$descricao = $_POST['descricao'];
		$procura = $_POST['procura'];

		$billsToPay = new BillsToPay();

		if ($chk_descricao == false) {

			$descricao = null;
		}

		if ($chk_setor == false) {

			$setor = null;
		}

		if ($intervalo) {

			$billsToPay->SearchByDateInterval($dataini, $datafim, $procura, $descricao, $setor);

		} else {

			$billsToPay->SearchByDate($dataini, $procura, $descricao, $setor);
		}

		$tplBillsToPay = new View('templates/bills_to_pay');

		if ($row = $billsToPay->getResult()) {

			$response = "";

			do {

				$row = BillsToPay::FormatFields($row);

				if ($row['datapago'] != null) {

					$response.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

				} else {

					if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

						$response.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

					} else {

						$response.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
					}
				}

			} while ($row = $billsToPay->getResult());

			Send($response);

		} else {

			Notifier::Add("Nenhuma conta encontrada!", Notifier::NOTIFIER_INFO);
			Send(null);
		}

	break;

	case "billstopay_topay":

		$billsToPay = new BillsToPay();

		$billsToPay->getPendingList();

		$tplBillsToPay = new View('templates/bills_to_pay');

		if ($row = $billsToPay->getResult()) {

			$bills = "";

			do {
				$row = BillsToPay::FormatFields($row);

				if ($row['datapago'] != null) {

					$bills.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

				} else {

					if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

						$bills.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

					} else {

						$bills.= $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
					}
				}

			} while ($row = $billsToPay->getResult());

			Send($bills);

		} else {

			Notifier::Add("Não há contas em aberto ;-)", Notifier::NOTIFIER_INFO);
			Send([]);
		}

	break;

	case "billstopay_delete":

		$id_contasapagar = $_POST['id_contasapagar'];

		$billsToPay = new BillsToPay();

		if ($billsToPay->Delete($id_contasapagar)) {

			Notifier::Add("Conta removida com suscesso!", Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Error ao exluir conta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "billstopay_edit":

		$id_contasapagar = $_POST['id_contasapagar'];

		$billsToPay = new BillsToPay();

		$billsToPay->Read($id_contasapagar);

		if ($row = $billsToPay->getResult()) {

			$tplBillsToPay = new View('templates/bills_to_pay');

			$row = BillsToPay::FormatFields($row);

			if ($row['datapago'] != null) {

				$row['block_billstopay_status'] = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_PAID");

			} else {

				if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

					$row['block_billstopay_status'] = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_OVERDUE");

				} else {

					$row['block_billstopay_status'] = $tplBillsToPay->getContent($row, "BLOCK_BILLSTOPAY_STATUS_PENDING");
				}
			}

			Send($tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_POPUP_EDITION"));

		} else {

			Notifier::Add("Erro ao abrir conta para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "billstopay_payment_form":

		$id_contasapagar = $_POST['id_contasapagar'];

		$tplBillsToPay = new View("templates/bills_to_pay");

		$billstopay = new BillsToPay();

		$billstopay->Read($id_contasapagar);

		if ($row = $billstopay->getResult()) {

			$row = BillsToPay::FormatFields($row);
			$row['data'] = date('Y-m-d');

			Send($tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_POPUP_PAYMENT"));

		} else {

			Notifier::Add("Erro ao carregar dados para pagamento da conta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "billstopay_payment_cancel":

		$id_contasapagar = $_POST['id_contasapagar'];

		$tplBillsToPay = new View("templates/bills_to_pay");

		$data = [
			"id_contasapagar" => $id_contasapagar,
		];

		Send($tplBillsToPay->getContent($data, "EXTRA_BLOCK_BILLSTOPAY_PAYMENT_BUTTON"));
	break;

	case "billstopay_payment":

		$id_contasapagar = $_POST['id_contasapagar'];
		$datapago = $_POST['datapago'];
		$valorpago = $_POST['valorpago'];

		$billsToPay = new BillsToPay();

		$data = [
			"id_contasapagar" => $id_contasapagar,
			"datapago" => $datapago,
			"valorpago" => $valorpago,
		];

		$billsToPay->setPayment($data);

		$billsToPay->Read($id_contasapagar);

		if ($row = $billsToPay->getResult()) {

			$row = BillsToPay::FormatFields($row);

			$tplBillsToPay = new View("templates/bills_to_pay");

			if ($row['datapago'] != null) {

				$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAID");

			} else {

				if (strtotime($row['vencimento']) < strtotime(date('Y-m-d'))) {

					$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_OVERDUE");

				} else {

					$bill = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PENDING");
				}
			}

			Notifier::Add("Registro de pagamento efetuado com sucesso.", Notifier::NOTIFIER_DONE);
			Send($bill);

		} else {

			Notifier::Add("Erro ao carregar dados da conta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "billstopay_vencimento_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_VENCIMENTO_FORM", "Erro ao carregar data de vencimento!");
	break;

	case "billstopay_vencimento_cancel":

		BillstopayFormCancel("BLOCK_BILLSTOPAY_VENCIMENTO", "Erro ao carregar data de vencimento!");
	break;

	case "billstopay_vencimento_save":

		BillstopayFormSave('vencimento', "BLOCK_BILLSTOPAY_VENCIMENTO", "Erro ao carregar data de vencimento!");
	break;

	case "billstopay_descricao_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_DESCRICAO_FORM", "Erro ao carregar descrição da conta!");
	break;

	case "billstopay_descricao_cancel":

		BillstopayFormCancel("BLOCK_BILLSTOPAY_DESCRICAO", "Erro ao carregar descrição da conta!");
	break;

	case "billstopay_descricao_save":

		BillstopayFormSave('descricao', "BLOCK_BILLSTOPAY_DESCRICAO", "Erro ao carregar descridescrição da conta!");
	break;

	case "billstopay_valor_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_VALOR_FORM", "Erro ao carregar valor da conta!");
	break;

	case "billstopay_valor_cancel":

		BillstopayFormCancel("BLOCK_BILLSTOPAY_VALOR", "Erro ao carregar valor da conta!");
	break;

	case "billstopay_valor_save":

		BillstopayFormSave("valor", "BLOCK_BILLSTOPAY_VALOR", "Erro ao carregar valor da conta");
	break;

	case "billstopay_setor_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_SETOR_FORM", "Erro ao ler categoria da conta!");
	break;

	case "billstopay_setor_cancel":

		BillstopayFormCancel("BLOCK_BILLSTOPAY_SETOR", "Erro ao carregar setor da conta!");
	break;

	case "billstopay_setor_save":

		BillstopayFormSave("id_contasapagarsetor", "BLOCK_BILLSTOPAY_SETOR", "Erro ao carregar setor da conta!");
	break;

	case "billstopay_obs_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_OBS_FORM", "Erro ao carregar observação da conta!");
	break;

	case "billstopay_obs_cancel":

		BillstopayFormCancel("BLOCK_BILLSTOPAY_OBS", "Erro ao carregar observação da conta!");
	break;

	case "billstopay_obs_save":

		BillstopayFormSave("obs", "BLOCK_BILLSTOPAY_OBS", "Erro ao carregar observação da conta!");
	break;

	case "billstopay_valorpago_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_FORM", "Erro ao carregar valor pago da conta!");
	break;

	case "billstopay_valorpago_cancel":

		BillstopayFormCancel("EXTRA_BLOCK_BILLSTOPAY_VALORPAGO", "Erro ao carregar valor pago da conta!");
	break;

	case "billstopay_valorpago_save":

		BillstopayFormSave("valorpago", "EXTRA_BLOCK_BILLSTOPAY_VALORPAGO", "Erro ao carregar valor pago da conta!");
	break;

	case "billstopay_datapago_edit":

		BillstopayFormEdit("EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_FORM", "Erro ao carregar data de pagamento da conta!");
	break;

	case "billstopay_datapago_cancel":

		BillstopayFormCancel("EXTRA_BLOCK_BILLSTOPAY_DATAPAGO", "Erro ao carregar data de pagamento da conta!");
	break;

	case "billstopay_datapago_save":

		BillstopayFormSave("datapago", "EXTRA_BLOCK_BILLSTOPAY_DATAPAGO", "Erro ao carregar valor pago da conta!");
	break;

	default:

		Notifier::Add("Requisição inválida", Notifier::NOTIFIER_ERROR);
		Send(null);
	break;
}
