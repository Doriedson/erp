<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\Clean;
use database\Printing;
use database\Table;
use database\SaleOrder;
use database\SaleOrderItem;
use database\Entity;
use database\Product;
use database\ProductKit;
use database\ProductType;
// use database\ProductSector;
// use database\Collaborator;

require "./inc/config.inc.php";
require "./inc/authorization.php";

function LoadWaiterTableOrder($id_mesa) {

	$tplWaiterOrder = new View('templates/waiter_order');
	$tplWaiterOrderProducts = new View('templates/waiter_order_products');

	$tplEntity = new View('templates/entity');

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

	$tplWaiterOrder = new View('templates/waiter_order');
	$tplEntity = new View('templates/entity');

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

        ControlAccess::Check(ControlAccess::CA_WAITER);

		$tplSelfService = new View("templates/waiter_self_service");

		$tplProduct = new View("templates/product");

		$tplTable = new View("templates/waiter_table");

		$table = new Table();

		$table->getList();

		$tables = "";

		if ($rowTable = $table->getResult()) {

			do {

				$rowTable['screen'] = "selfservice";
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

		$data["extra_block_table"] = $tables;
		$data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		Send($tplSelfService->getContent($data, "BLOCK_PAGE"));

		break;

	case "product_select":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		if( is_numeric($id_produto) ) {

			$product->SearchByCode($id_produto);

		} else {

			$product->SearchByString($id_produto);
		}

		if ($row = $product->getResult()) {

			if ($row['ativo'] == 0) {

				Notifier::Add("Produto está inativo para venda!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$tplSelfService = new View('templates/waiter_self_service');

			$row = Product::FormatFields($row);

			Send($tplSelfService->getContent($row, "EXTRA_BLOCK_QTY"));

		} else {

			Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "table_select":

		$id_mesa = $_POST['id_mesa'];
		$versao = $_POST["versao"];
		$id_produto = $_POST['id_produto'];
		$qtd = $_POST['qtd'];
		$obs = "";

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			$mesa = $row['mesa'];

			if (is_null($row['id_venda'])) {

				$saleorder = new SaleOrder();

				$id_venda = $saleorder->Create([
					"frete" => 0,
					"id_entidade" => null,
					"id_vendastatus" => SaleOrder::STATUS_MESA_EM_ANDAMENTO,
					"mesa" => $mesa,
				]);

				$table->Book($id_mesa, $id_venda, $GLOBALS['authorized_id_entidade']);

			} else {

				$id_venda = $row['id_venda'];

				$sale = new SaleOrder();

				if (!$sale->CheckVersion($row["id_venda"], $versao)) {

					Send(null);
				}

				$table->Update([
					"field" => "id_entidade",
					"id_mesa" => $id_mesa,
					"value" => $GLOBALS['authorized_id_entidade']
				]);

				$sale->ReadOnly($id_venda);

				if ($rowSale = $sale->getResult()) {

					if ($rowSale['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

						$sale->ChangeStatus($id_venda, SaleOrder::STATUS_MESA_EM_ANDAMENTO);

					}

				} else {

					Notifier::Add("Erro ao carregar dados da venda!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

			$saleItem = new SaleOrderItem();

			$product = new Product();

			$printer_group = [];

			$product->Read($id_produto);

			if ($row = $product->getResult()) {

				if ($row['balanca'] == 0 && intval($qtd) <> $qtd) {

					Notifier::Add("Produto não pode ser vendido fracionado!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				$preco = ($row['preco_promo'] > 0)? $row['preco_promo']: $row['preco'];

				if ($row['id_impressora'] != null) {

					if ($obs == "") {

						$printer_group[$row['id_impressora']][] =  $qtd . " - " . $row['produto'];

					} else {

						$printer_group[$row['id_impressora']][] = $qtd . " - " . $row['produto'] . "\\nObs.: " . $obs;
					}
				}

				$saleItem->Create($id_venda, $id_produto, $row['id_produtotipo'], $qtd, $preco, $obs);

			} else {

				Notifier::Add("Erro ao registrar produto na mesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			if (count($printer_group) > 0) {

				// $printer = new PrinterConfig();

				foreach (array_keys($printer_group) as $printer_index) {

					$printing = new Printing($row['id_impressora']);

					$printing->initialize();

					// $printer->CouponNew();

					// Header comanda
					$printing->text("Mesa: $mesa");
					$printing->textTruncate("Garçom: " . $GLOBALS['authorized_nome']);
					$printing->text("Data/Hora: " . date("d/m/Y H:i"));
					$printing->line(1);
					$printing->text("Produtos");
					$printing->linedashspaced();

					//Body Comanda
					foreach ($printer_group[$printer_index] as $line) {

						$printing->text($line);
						$printing->line(1);
					}

					// Footer Comanda
					$printing->linedashspaced();

					// $printer->CouponPrint($printer_index);
					$printing->close();
				}
			}

			$table->getList();

			$tables = "";

			$tplTable = new View("templates/waiter_table");

			if ($rowTable = $table->getResult()) {

				do {

					$rowTable['screen'] = "selfservice";
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

			Notifier::Add($row['produto'] . "<br>" . number_format($qtd, 3, ",", ".") . " " . $row['produtounidade'] . "<br>adicionado a $mesa", Notifier::NOTIFIER_INFO);
			Send($tables);

		} else {

			Notifier::Add("Erro ao carregar mesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}