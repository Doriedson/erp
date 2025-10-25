<?php


use App\Legacy\Entity;
use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\SaleOrder;
use App\Legacy\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$tplSaleCoupon = new View("report_sale_coupon");

		$data = ['data' => date('Y-m-d')];

		$saleorder = new SaleOrder();

		$saleorder->getListStatus();

		$listStatus = "";

		while ($row = $saleorder->getResult()) {

			$listStatus .= $tplSaleCoupon->getContent($row, "EXTRA_BLOCK_SALEORDER_STATUS");
		}

		$data['venda_status'] = $listStatus;

		Send($tplSaleCoupon->getContent($data, "BLOCK_PAGE"));

		break;

	case "reportsale_coupon":

		$data = $_POST['data'];
		$id_vendastatus = $_POST['id_vendastatus'];

		$data_formatted = date_format(date_create($data), 'd/m/Y');

		$sale = new SaleOrder();

		$tplSaleCoupon = new View('report_sale_coupon');

		$sale->getCouponsByDate($data, $id_vendastatus);

		if ($row = $sale->getResult()) {

			$data = "";
			$total = 0;
			$counter = 0;

			$tplSale= new View("sale_order");

			do {

				$counter++;

				$row = SaleOrder::FormatFields($row);

				SaleOrder::getMenu($row, true);

				if ($row['id_entidade'] == null) {

					$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent([], "EXTRA_BLOCK_SALEORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent($rowEntity, "EXTRA_BLOCK_SALEORDER_ENTITY");
						$row['nome'] = $rowEntity['nome'];
					}
				}

				if ($row['id_vendastatus'] != SaleOrder::STATUS_PEDIDO_CANCELADO && $row['id_vendastatus'] != SaleOrder::STATUS_VENDA_CANCELADA && $row['id_vendastatus'] != SaleOrder::STATUS_MESA_CANCELADA && $row['id_vendastatus'] != SaleOrder::STATUS_MESA_TRANSFERIDA) {

					// $total += $row['subtotal'] + $row['frete'] + $row['valor_servico'] - $row['desconto'];
					$total = Calc::Sum([
						$total,
						$row['subtotal'],
						$row['frete'],
						$row['valor_servico'],
						- $row['desconto']
					]);

				} else {

					$row['total'] = 0;
				}

				$data.= $tplSaleCoupon->getContent($row, "EXTRA_BLOCK_REPORTSALE");

			} while ($row = $sale->getResult());

			Send([
				"data" => $data,
				"data_formatted" => $data_formatted,
				"total_formatted" => number_format($total, 2, ",", "."),
				"counter" => $counter
			]);

		} else {

			// $data = [
			// 	"data" => $tplSaleCoupon->getContent([], "EXTRA_BLOCK_REPORTSALE_NONE"),
			// 	"total_formatted" => "0,00",
			// 	"counter" => 0
			// ];

			Notifier::Add("Nenhum relatório encontrado para os dados informados!", Notifier::NOTIFIER_INFO);
			Send(null);
		}
	break;

	case "reportsalecoupon_id_venda_search":

		$id_venda = $_POST["id_venda"];

		$sale = new SaleOrder();

		$tplSaleCoupon = new View('report_sale_coupon');

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$data = "";
			$total = 0;
			$counter = 0;

			$tplSale= new View("sale_order");

			do {

				$counter++;

				$row = SaleOrder::FormatFields($row);

				SaleOrder::getMenu($row, true);

				if ($row['id_entidade'] == null) {

					$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent([], "EXTRA_BLOCK_SALEORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent($rowEntity, "EXTRA_BLOCK_SALEORDER_ENTITY");
						$row['nome'] = $rowEntity['nome'];
					}
				}

				if ($row['id_vendastatus'] != SaleOrder::STATUS_PEDIDO_CANCELADO && $row['id_vendastatus'] != SaleOrder::STATUS_VENDA_CANCELADA && $row['id_vendastatus'] != SaleOrder::STATUS_MESA_CANCELADA && $row['id_vendastatus'] != SaleOrder::STATUS_MESA_TRANSFERIDA) {

					// $total += $row['subtotal'] + $row['frete'] + $row['valor_servico'] - $row['desconto'];
					$total = Calc::Sum([
						$total,
						$row['subtotal'],
						$row['frete'],
						$row['valor_servico'],
						- $row['desconto']
					]);

				} else {

					$row['total'] = 0;
				}

				$data.= $tplSaleCoupon->getContent($row, "EXTRA_BLOCK_REPORTSALE");

			} while ($row = $sale->getResult());

			Send([
				"data" => $data
			]);

		} else {

			Notifier::Add("Nenhuma venda encontrada para o código informado!", Notifier::NOTIFIER_INFO);
			Send(null);
		}

		break;

	case "coupon_expand":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			Send(SaleOrder::SaleOrderExpand($row, false));

		} else {

			Notifier::Add("Erro ao carregar os produtos da venda.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;
}