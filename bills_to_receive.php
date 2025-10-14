<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\SaleOrder;
use database\Entity;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONTAS_A_RECEBER);

function FormatBillsToReceive($row) {

	$row['datacad_formatted'] = date_format( date_create($row['datacad']), 'd/m/Y');
	$row['datacad'] = date_format( date_create($row['datacad']), 'Y-m-d');

	$row['vencimento_formatted'] = date_format( date_create($row['vencimento']), 'd/m/Y');
	$row['vencimento'] = date_format( date_create($row['vencimento']), 'Y-m-d');

	if ($row['datapago']) {

		$row['datapago_formatted'] = date_format( date_create($row['datapago']), 'd/m/Y');
		$row['valorpago_formatted'] = "R$ " . number_format($row['valorpago'],2,',','.');
		$row['datapago'] = date_format( date_create($row['datapago']), 'Y-m-d');
		$row['bt_pagamento'] = "hidden";
		$row['editable'] = "mouseHand editable";

	} else {

		$row['datapago_formatted'] = "Em aberto";
		$row['valorpago_formatted'] = "";
		$row['bt_pagamento'] = "";
		$row['editable'] = "";
	}

	$row['valor_formatted'] = number_format($row['valor'],2,',','.');


	return $row;
}

switch ($_POST['action']) {

	case "load":

        $tplBillsToReceive = new View('templates/bills_to_receive');
		$tplEntity = new View("templates/entity");

        $sale = new SaleOrder();

        $sale->getTotalSalesOnCreditByEntity();

		$data = [];

		$total = 0;

		$content = "";

        if ($row = $sale->getResult()) {

			do {

				if (array_key_exists($row["id_entidade"], $data)) {

					$data[$row["id_entidade"]]['total'] += $row['total'] + $row['frete'] + $row['valor_servico'];

				} else {

					$data[$row["id_entidade"]]['id_entidade'] = $row["id_entidade"];
					$data[$row["id_entidade"]]['total'] = $row['total'] + $row['frete'] + $row['valor_servico'];
					$data[$row["id_entidade"]]['ativo'] = $row['ativo'];
					$data[$row["id_entidade"]]['nome'] = $row['nome'];
					$data[$row["id_entidade"]]['credito'] = $row['credito'];
				}

			} while ($row = $sale->getResult());

				foreach($data as $entity) {

					$entity = Entity::FormatFields($entity);

					$total += $entity['total'];

					$entity["total_formatted"] = number_format($entity['total'], 2, ",", ".");

					$entity["block_entity_credit"] = $tplEntity->getContent($entity, "BLOCK_ENTITY_CREDIT");

					$content .= $tplBillsToReceive->getContent($entity, "EXTRA_BLOCK_BILLSTORECEIVE");
				}

		} else {

			$content = $tplBillsToReceive->getContent([], "EXTRA_BLOCK_BILLSTORECEIVE_NONE");
		}

		$data = [
			'data' => date('Y-m-d'),
			"total_formatted" => number_format($total, 2, ",", "."),
            'extra_block_billstoreceive' => $content
		];

		Send ($tplBillsToReceive->getContent($data, "BLOCK_PAGE"));
	break;

	case "billsreceive_forwardsale":

		$id_entidade = $_POST['id_entidade'];

		$tplBills = new View("templates/bills_to_receive");
		$tplSale = new View("templates/sale_order");

		$sale = new SaleOrder();

		$sale->getSalesOnCreditByEntity($id_entidade, true);

		$coupon = "";

		if ($row = $sale->getResult()) {

			do {

				$row = SaleOrder::FormatFields($row);

				SaleOrder::getMenu($row, true);

				$coupon .= $tplBills->getContent($row, "EXTRA_BLOCK_FORWARD_SALE");

			} while ($row = $sale->getResult());

			$content['id_entidade'] = $id_entidade;

			Send($coupon);

		} else {

			Notifier::Add("Ocorreu um erro ao carregar os pedidos!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}