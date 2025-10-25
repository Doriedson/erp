<?php


use App\View\View;
use App\Legacy\Printing;
use App\Legacy\Table;
use App\Legacy\SaleOrder;
use App\Legacy\SaleOrderItem;
use App\Legacy\Entity;
use App\Legacy\Notifier;
use App\Legacy\Product;
use App\Legacy\Log;

require "./inc/config.inc.php";
require "./inc/authorization.php";

function LoadWaiterTableOrder($id_mesa) {

	$tplWaiterOrder = new View('waiter_order');
	$tplWaiterOrderProducts = new View('waiter_order_products');

	$tplEntity = new View('entity');

	$table = new Table();

	$table->Read($id_mesa);

	if ($row = $table->getResult()) {

		if ($row['id_venda']) {

			$saleOrder = new SaleOrder();

			$saleOrder->Read($row['id_venda']);

			if ($row = $saleOrder->getResult()) {

				$frete = $row['frete'];
				$servico =  $row['valor_servico'];

				if ($row['id_entidade'] == null) {

					$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_products"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$row["window"] = "waiter_order_products";
						$row["block_entity_nome"] = $tplEntity->getContent($rowEntity, "BLOCK_ENTITY_NOME");

						$extra_block_waiterorder_entity = $tplWaiterOrder->getContent($rowEntity, "EXTRA_BLOCK_WAITERORDER_ENTITY");
					}
				}

				$saleItem = new SaleOrderItem();

				$saleItem->getListActiveItems($row['id_venda']);

				if ($row = $saleItem->getResult()) {

					$products = "";

					$subtotal = 0;
					$desconto = 0;

					do {
						$row = SaleOrderItem::FormatFields($row);

						$subtotal+= $row['subtotal'];

						$desconto+= $row['desconto'];

						$products .= $tplWaiterOrderProducts->getContent($row, "EXTRA_BLOCK_PRODUCT");

					} while ($row = $saleItem->getResult());

					$total = $subtotal - $desconto + $frete + $servico;

					$data = [
						"extra_block_product" => $products,
						"extra_block_waiterorder_entity" => $extra_block_waiterorder_entity,
						// "extra_block_entity_new" => $extra_block_entity_new,
						"subtotal_formatted" => number_format($subtotal, 2, ',', '.'),
						"desconto_formatted" => number_format($desconto, 2, ',', '.'),
						"servico_formatted" => number_format($servico, 2, ',', '.'),
						"total_formatted" => number_format($total, 2, ',', '.'),
					];

					Send($tplWaiterOrderProducts->getContent($data, "BLOCK_PAGE"));

				} else {

					Notifier::Add("Mesa sem produtos para fechamento!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("A mesa está livre e não pode ser fechada!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	} else {

		Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function LoadWaiterTableOrderRevision($id_mesa, $products) {

	$tplWaiterOrder = new View('waiter_order');
	$tplEntity = new View('entity');

	$table = new Table();

	$table->Read($id_mesa);

	if ($row = $table->getResult()) {

		if ($row['id_venda']) {

			$saleOrder = new SaleOrder();

			$saleOrder->Read($row['id_venda']);

			if ($row = $saleOrder->getResult()) {

				if ($row['id_entidade'] == null) {

					$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_revision"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$row["window"] = "waiter_order_revision";
						$row["block_entity_nome"] = $tplEntity->getContent($rowEntity, "BLOCK_ENTITY_NOME");

						$extra_block_waiterorder_entity = $tplWaiterOrder->getContent($rowEntity, "EXTRA_BLOCK_WAITERORDER_ENTITY");
					}
				}
			}

		} else {

			$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_revision"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");
		}
	}

	$data["extra_block_waiterorder_entity"] = $extra_block_waiterorder_entity;

	if (count($products) == 0) {

		$data["extra_block_product"] = $tplWaiterOrder->getContent([], "EXTRA_BLOCK_PRODUCT_NONE");

		Send($tplWaiterOrder->getContent($data, "BLOCK_PAGE"));
	}

	$product = new Product();

	$product->getList($products);

	$products = "";

	if ($row = $product->getResult()) {

		do {
			$row = Product::FormatFields($row);

			if ($row['id_produtounidade'] == 1) { //UN

				$row['extra_block_product_un'] = $tplWaiterOrder->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN");

			} else if ($row['id_produtounidade'] == 2) { //KG

				$row['extra_block_product_un'] = $tplWaiterOrder->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG");
			}

			$products .= $tplWaiterOrder->getContent($row, "EXTRA_BLOCK_PRODUCT");

		} while ($row = $product->getResult());

	} else {

		$products = $tplWaiterOrder->getContent([], "EXTRA_BLOCK_PRODUCT_NONE");
	}

	$data["extra_block_product"] = $products;

	Send($tplWaiterOrder->getContent($data, "BLOCK_PAGE"));
}

switch($_POST['action']) {

	case "load":

		ControlAccess::Check(ControlAccess::CA_TRANSFERENCIA_MESA);

        $id_mesa = $_POST['id_mesa'];

        $table = new Table();

        $table->Read($id_mesa);

		if ($row = $table->getResult()) {

			if ($row['id_venda'] == null) {

				Notifier::Add("Mesa não está em atendimento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$saleOrder = new SaleOrder();

			$saleOrder->ReadOnly($row["id_venda"]);

			if ($rowOrder = $saleOrder->getResult()) {

				$row["versao"] = $rowOrder["versao"];

			} else {

				Notifier::Add("Erro ao ler dados da mesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$saleItem = new SaleOrderItem();

			$saleItem->getListActiveItems($row['id_venda']);

			if ($rowItem = $saleItem->getResult()) {

				$tplTableTransf = new View("waiter_table_transf");

				$tplProduct = new View("product");

				$row["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

				$tplTable = new View("waiter_table");

				$table->getList();

				$tables = "";

				if ($rowTable = $table->getResult()) {

					do {

						$rowTable['screen'] = "waiter_tabletransf";
						$rowTable = Table::FormatFields($rowTable);

						if ($rowTable['id_venda']) {

							if ($rowTable['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

								$tables .= $tplTable->getContent($rowTable, "EXTRA_BLOCK_TABLE_BUSY");

							} else if ($rowTable['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

								$tables .= $tplTable->getContent($rowTable, "EXTRA_BLOCK_TABLE_PAYMENT");
							}

						} else {

							$tables .= $tplTable->getContent($rowTable, "EXTRA_BLOCK_TABLE_FREE");
						}

					} while ($rowTable = $table->getResult());

				} else {

					$tables = $tplTable->getContent([], "EXTRA_BLOCK_TABLE_NOTFOUND");
				}

				$row["extra_block_table"] = $tables;

				Send($tplTableTransf->getContent($row, "BLOCK_PAGE"));

			} else {

				Notifier::Add("Mesa sem produto para transferir!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "waitertable_transf":

		ControlAccess::Check(ControlAccess::CA_TRANSFERENCIA_MESA);

		$id_mesa_from = $_POST['id_mesa_from'];
		$versao_from = $_POST['versao_from'];
		$id_mesa_to = $_POST['id_mesa_to'];
		$versao_to = $_POST['versao_to'];

		$table = new Table();

		$table->Transfer($id_mesa_from, $versao_from, $id_mesa_to, $versao_to);

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	    break;
}