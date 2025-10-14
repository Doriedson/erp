<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Clean;
use database\Printing;
use database\PrinterConfig;
use database\Table;
use database\SaleOrder;
use database\SaleOrderItem;
use database\Entity;
use database\Product;
use database\ProductSector;
use database\Collaborator;
use database\Company;

require "./inc/config.inc.php";
require "./inc/version.php";

if (!isset($_POST['action'])) {

	$tplWaiterIndex = new View("templates/waiter_index");

	$module = $tplWaiterIndex->getContent(["module" => 'waiter'], "BLOCK_PAGE");

    $tplIndex = new View("templates/index");

	$company = new Company();

	$company->Read();

	$empresa = "Nome da Empresa";

	if ($row = $company->getResult()) {

		$empresa = $row['empresa'];
	}

    $content = [
        "version" => $version,
		"date" => date('Y-m-d'),
		"date_search" => date("Y-m"),
        "title" => 'Garçom',
        "module" => $module,
        'manifest' => 'waiter_manifest.json',
		"empresa" => $empresa
    ];

    $tplIndex->Show($content, "BLOCK_PAGE");

    exit();
}

switch($_POST['action']) {

	case "load":
	case "login":
	// case "popup_load":

		$GLOBALS['authorized_skip'] = true;
		break;
}

require "./inc/authorization.php";

function LoadWaiterTableOrder($id_mesa) {

	$tplWaiterOrder = new View('templates/waiter_order');
	$tplWaiterSector = new View('templates/waiter_sector');
	$tplWaiterOrderProducts = new View('templates/waiter_order_products');

	$tplEntity = new View('templates/entity');

	$table = new Table();

	$table->Read($id_mesa);

	if ($row = $table->getResult()) {

		if ($row['id_venda']) {

			$mesa_desc = $row['mesa'];
			$saleOrder = new SaleOrder();
			$versao = null;

			$saleOrder->Read($row['id_venda']);

			if ($row = $saleOrder->getResult()) {

				if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

					Notifier::Add("A mesa está fechada para pagamento!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				if ($row['id_vendastatus'] != SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

					Notifier::Add("Não é possível visualizar essa mesa!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				$frete = $row['frete'];
				$servico =  $row['valor_servico'];
				$versao = $row["versao"];

				if ($row['id_entidade'] == null) {

					$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_products"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$rowEntity["window"] = "waiter_order_products";
						$rowEntity["block_entity_nome"] = $tplEntity->getContent($rowEntity, "BLOCK_ENTITY_NOME");

						$extra_block_waiterorder_entity = $tplWaiterOrder->getContent($rowEntity, "EXTRA_BLOCK_WAITERORDER_ENTITY");
					}
				}

				$saleItem = new SaleOrderItem();

				$saleItem->getListActiveItems($row['id_venda']);

				$products = "";
				$total = 0;
				$subtotal = 0;
				$desconto = 0;

				if ($row = $saleItem->getResult()) {

					do {
						$row = SaleOrderItem::FormatFields($row);

						// $row['preco_final'] = $row['preco'];
						// $row['preco_final_formatted'] = $row['preco_formatted'];

						$subtotal+= $row['subtotal'];

						$desconto+= $row['desconto'];

						// if ($row['id_produtounidade'] == 1) { //UN

						// 	$row['extra_block_product_un'] = $tplWaiterSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN");

						// } else if ($row['id_produtounidade'] == 2) { //KG

						// 	$row['extra_block_product_un'] = $tplWaiterSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG");
						// }
						$row["versao"] = $versao;

						$products .= $tplWaiterOrderProducts->getContent($row, "EXTRA_BLOCK_PRODUCT");

					} while ($row = $saleItem->getResult());

					$total = $subtotal - $desconto + $frete + $servico;

				} else {

					$products = $tplWaiterSector->getContent([], "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE");
				}

				$data = [
					"extra_block_product" => $products,
					"id_mesa" => $id_mesa,
					"mesa_desc" => $mesa_desc,
					"extra_block_waiterorder_entity" => $extra_block_waiterorder_entity,
					// "extra_block_entity_new" => $extra_block_entity_new,
					"subtotal_formatted" => number_format($subtotal, 2, ',', '.'),
					"desconto_formatted" => number_format($desconto, 2, ',', '.'),
					"servico_formatted" => number_format($servico, 2, ',', '.'),
					"total_formatted" => number_format($total, 2, ',', '.'),
				];

				Send([
					"data" => $tplWaiterOrderProducts->getContent($data, "BLOCK_PAGE"),
					"versao" => $versao
				]);

			} else {

				Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("A mesa está livre!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	} else {

		Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function LoadWaiterTableOrderRevision($id_mesa, $products) {

	$tplWaiterOrder = new View('templates/waiter_order');
	$tplWaiterSector = new View('templates/waiter_sector');
	$tplEntity = new View('templates/entity');
	$versao = 0;

	$data["id_mesa"] = $id_mesa;

	$table = new Table();

	$table->Read($id_mesa);

	if ($row = $table->getResult()) {

		$data["mesa_desc"] = $row['mesa'];

		if ($row['id_venda']) {

			$saleOrder = new SaleOrder();

			$saleOrder->Read($row['id_venda']);

			if ($row = $saleOrder->getResult()) {

				$versao = $row["versao"];

				if ($row['id_entidade'] == null) {

					$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_revision"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");

				} else {

					$entity = new Entity();

					$entity->Read($row['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$rowEntity = Entity::FormatFields($rowEntity);

						$rowEntity["window"] = "waiter_order_revision";
						$rowEntity["block_entity_nome"] = $tplEntity->getContent($rowEntity, "BLOCK_ENTITY_NOME");

						$extra_block_waiterorder_entity = $tplWaiterOrder->getContent($rowEntity, "EXTRA_BLOCK_WAITERORDER_ENTITY");
					}
				}
			}

		} else {

			$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(["window" => "waiter_order_revision"], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");
		}

	} else {

		Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$data["extra_block_waiterorder_entity"] = $extra_block_waiterorder_entity;

	if (count($products) == 0) {

		$data["extra_block_product"] = $tplWaiterSector->getContent([], "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE");

		Send([
			"data" => $tplWaiterOrder->getContent($data, "BLOCK_PAGE"),
			"versao" => $versao
		]);
	}

	$product = new Product();

	$product->getList($products);

	$products = "";

	if ($row = $product->getResult()) {

		do {
			$row = Product::FormatFields($row);

			if ($row['id_produtounidade'] == 1) { //UN

				$row['extra_block_product_un'] = $tplWaiterSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN");

			} else if ($row['id_produtounidade'] == 2) { //KG

				$row['extra_block_product_un'] = $tplWaiterSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG");
			}

			$products .= $tplWaiterSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT");

		} while ($row = $product->getResult());

	} else {

		$products = $tplWaiterSector->getContent([], "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE");
	}

	$data["extra_block_product"] = $products;

	Send([
		"data" => $tplWaiterOrder->getContent($data, "BLOCK_PAGE"),
		"versao" => $versao
	]);
}

switch($_POST['action']) {

	case "load":

		$id_entidade = $_POST["id_entidade"];

		$tplLogin = new View("templates/waiter_login");

		$collaborator = new Collaborator();

		$row_result = $collaborator->getListHavingAccess(ControlAccess::CA_WAITER);

		$waiters = "";

		foreach($row_result as $row) {

			$row["selecte"] = "";

			if ($row["id_entidade"] == $id_entidade) {

				$row["selected"] = "selected";
			}

            $waiters.= $tplLogin->getContent($row, "EXTRA_BLOCK_WAITER");
		}

		if (count($row_result) == 0) {

			$waiters = $tplLogin->getContent([], "EXTRA_BLOCK_WAITER_NONE");
		}

		$data = [
			'waiters' => $waiters
		];

		Send($tplLogin->getContent($data, "BLOCK_PAGE"));

		break;

	case "login":

		$user = Clean::HtmlChar($_POST['id_entidade']);
		$pass = Clean::HtmlChar($_POST['pass']);

		if ( trim($user)=='' || trim($pass=='') ) {

			ControlAccess::Unauthorized();

		} else if ($user == 0) {

			Notifier::Add("Nenhum garçom cadastrado para acesso!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			if (ControlAccess::Login($user, $pass, ControlAccess::CA_WAITER)) {

				$tplMenu = new View("templates/waiter_menu");
				$tplTable = new View("templates/waiter_table");

				$nome = strtok($GLOBALS['authorized_nome'], " ");

				$company = new Company();

				$company->Read();

				$empresa = "Nome da Empresa";

				if ($row = $company->getResult()) {

					$empresa = $row['empresa'];
				}

				$date = new DateTimeImmutable();

				$data = [
					"id_entidade" => $GLOBALS['authorized_id_entidade'],
					"nome" => $nome,
					"empresa" => $empresa,
					"timestamp" => $date->getTimestamp()
				];

				Send([
					"data" => Table::LoadWaiterTable("waiter_table"),
					"menu" => $tplMenu->getContent($data, View::ALL),
					"logged" => true
				]);

			} else {

				ControlAccess::Unauthorized(); //user not found
			}
		}

		break;

	case "auth":

		ControlAccess::Check(ControlAccess::CA_WAITER);

		$tplMenu = new View("templates/waiter_menu");
		$tplTable = new View("templates/waiter_table");

		$nome = strtok($GLOBALS['authorized_nome'], " ");

		$company = new Company();

		$company->Read();

		$empresa = "Nome da Empresa";

		if ($row = $company->getResult()) {

			$empresa = $row['empresa'];
		}

		$date = new DateTimeImmutable();

		$data = [
			"id_entidade" => $GLOBALS['authorized_id_entidade'],
			"nome" => $nome,
			"empresa" => $empresa,
			"timestamp" => $date->getTimestamp()
		];

		Send([
			"data" => Table::LoadWaiterTable("waiter_table"),
			"menu" => $tplMenu->getContent($data, View::ALL)
		]);

		// } else {

		// 	header('HTTP/1.0 202 Accepted');
		// }

		break;

	case "waiter_table":

		Send(Table::LoadWaiterTable("waiter_table"));
		break;

	case "waiter_sector":

		$id_mesa = $_POST['id_mesa'];
		$versao = 0;

		$table = new Table();

		$table->Read($id_mesa);

		$change_status = false;

		if ($row = $table->getResult()) {

			$mesa_desc = $row['mesa'];

			if ($row['id_venda']) {

				$versao = $_POST["versao"];

				$sale = new SaleOrder();

				$sale->ReadOnly($row['id_venda']);

				if ($row = $sale->getResult()) {

					if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

						if (!$versao = $sale->CheckVersion($row['id_venda'], $versao)) {

							Send(null);
						}

						$sale->ChangeStatus($row['id_venda'], SaleOrder::STATUS_MESA_EM_ANDAMENTO);
						$change_status = true;
					}
				}
			}
		}

		$productsector = new ProductSector();

		$sector = $productsector->getListWaiter();

		$tplSector = new View('templates/waiter_sector');

		$waitersector_notfound = "";

		if (!empty($sector)) {

			$waitersector_notfound = "hidden";
		}

		$data = [
			"mesa_desc" => $mesa_desc,
			"extra_block_sector" => $sector,
			"waitersector_notfound" => $waitersector_notfound
		];

		if ($change_status) {

			Notifier::Add("Mesa aberta para atendimento.", Notifier::NOTIFIER_INFO);
		}

		Send([
			"data" => $tplSector->getContent($data, "BLOCK_PAGE"),
			"versao" => $versao
		]);

		break;

	case "waiter_product":

		$id_produtosetor = $_POST['id_produtosetor'];

		$product = new Product();

		$product->getProductBySector($id_produtosetor);

		$tplWaiterProduct = new View('templates/waiter_sector');

		$products = "";

		if ($row = $product->getResult()) {

			do {
				$row = Product::FormatFields($row);

				if ($row['id_produtounidade'] == 1) { //UN

					$row['extra_block_product_un'] = $tplWaiterProduct->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN");

				} else if ($row['id_produtounidade'] == 2) { //KG

					$row['extra_block_product_un'] = $tplWaiterProduct->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG");
				}

				$products .= $tplWaiterProduct->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT");

			} while ($row = $product->getResult());

			$data = [
				"extra_block_product" => $products
			];

			Send($products);

		} else {

			Send(null);
		}

	break;

	case "waiter_order_revision":

		$id_mesa = $_POST['id_mesa'];
		$products = [];

		if (key_exists('products', $_POST)) {

			$products = $_POST['products'];
		}

		LoadWaiterTableOrderRevision($id_mesa, $products);

		break;

	case "waiter_order":

		$id_mesa = $_POST['id_mesa'];
		$codes = $_POST['codes'];
		$versao = $_POST["versao"];

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			$mesa = $row['mesa'];

			$saleOrder = new SaleOrder();

			if (is_null($row['id_venda'])) {

				$id_venda = $saleOrder->Create([
					"frete" => 0,
					"id_entidade" => null,
					"id_vendastatus" => SaleOrder::STATUS_MESA_EM_ANDAMENTO,
					"mesa" => $mesa,
				]);

				$table->Book($id_mesa, $id_venda, $GLOBALS['authorized_id_entidade']);

				$saleOrder->ReadOnly($id_venda);

				if ($rowSaleorder = $saleOrder->getResult()) {

					$versão = $rowSaleorder["versao"];
				}

			} else {

				$versao = $_POST["versao"];
				$id_venda = $row['id_venda'];

				$saleOrder->ReadOnly($id_venda);

				if ($rowSaleorder = $saleOrder->getResult()) {

					if ($rowSaleorder["id_vendastatus"] != SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

						Notifier::Add("Essa mesa não está mais disponível para atendimento!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}

				} else {

					Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

				if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

					Send(null);
				}

				$table->Update([
					"field" => "id_entidade",
					"id_mesa" => $id_mesa,
					"value" => $GLOBALS['authorized_id_entidade']
				]);
			}

			$saleitem = new SaleOrderItem();

			$product = new Product();

			$printer_group = [];

			for ($index = 0; $index < count($codes); $index++) {

				$product->Read($_POST['product_' . $codes[$index]]['_id_product']);

				if ($row = $product->getResult()) {

					$row = Product::FormatFields($row);

					if ($row['id_impressora'] != null) {

						if ($_POST['product_' . $codes[$index]]['_obs'] == "") {

							$printer_group[$row['id_impressora']][] =  $_POST['product_' . $codes[$index]]['_qty'] . " - " . $row['produto'];

						} else {

							$printer_group[$row['id_impressora']][] = $_POST['product_' . $codes[$index]]['_qty'] . " - " . $row['produto'] . "\\nObs.: " . $_POST['product_' . $codes[$index]]['_obs'];
						}
					}

					$saleitem->Create($id_venda, $_POST['product_' . $codes[$index]]['_id_product'], $row['id_produtotipo'], $_POST['product_' . $codes[$index]]['_qty'], $row['preco_final'], $_POST['product_' . $codes[$index]]['_obs']);

				} else {

					Notifier::Add("Erro ao registrar produto na mesa!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

			if (count($printer_group) > 0) {

				// $printer = new PrinterConfig();

				foreach (array_keys($printer_group) as $printer_index) {

					$printing = new Printing($printer_index);

					$printing->initialize();

					// $printer->CouponNew();

					// Header comanda
					$printing->text("Mesa: $mesa");
					$printing->textTruncate("Garçom: " . $GLOBALS['authorized_nome']);
					$printing->text("Data/Hora: " . date("d/m/Y H:i"));
					$printing->line(1);
					$printing->text("Produtos");
					$printing->linedashspaced();

					$first_line = true;

					//Body Comanda
					foreach ($printer_group[$printer_index] as $line) {

						if (!$first_line) {

							$printing->line(1);
						}

						$printing->text($line);

						$first_line = false;
					}

					// Footer Comanda
					$printing->linedashspaced();

					// $printer->CouponPrint($printer_index);
					$printing->close();
				}
			}

			Notifier::Add("$mesa<br>Pedido registrado" , Notifier::NOTIFIER_DONE);
			Send([
				"data" => Table::LoadWaiterTable("waiter_table"),
				"versao" => $versao
			]);

		} else {

			Notifier::Add("Erro ao carregar mesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "extra_block_product_none":

		$tplWaiterSector = new View('templates/waiter_sector');

		Send($tplWaiterSector->getContent([], "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE"));

		break;

	case "waitertable_entity_search_open":

		$window = $_POST["window"];
		$tplEntity = new View('templates/entity');

		$data = [
			"block_entity_autocomplete_search" => $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH"),
			"window" => $window
		];

		$tplWaiterOrder = new View('templates/waiter_order');

		Send($tplWaiterOrder->getContent($data, "EXTRA_BLOCK_WAITERORDER_ENTITY_SEARCH"));
		break;

	case "waitertable_entity_search":

		$id_mesa = $_POST['id_mesa'];
		$entidade = $_POST['entidade'];
		$window = $_POST['window'];
		$versao = $_POST["versao"];

		$entity = new Entity;

		if( is_numeric($entidade) ) {

			$entity->SearchByCode($entidade);

		} else {

			$entity->Search($entidade);
		}

		if ($row = $entity->getResult()) {

			$id_entidade = $row['id_entidade'];

			$table = new Table();

			$table->Read($id_mesa);

			if ($row = $table->getResult()) {

				$mesa = $row['mesa'];

				$saleOrder = new SaleOrder();

				if ($row['id_venda']) {


					if (!$saleOrder->CheckVersion($row["id_venda"], $versao)) {

						Send(null);
					}

					$saleOrder->Update($row['id_venda'], "id_entidade", $id_entidade);

					$id_venda = $row['id_venda'];

					$table->Update([
						"field" => "id_entidade",
						"id_mesa" => $id_mesa,
						"value" => $GLOBALS['authorized_id_entidade']
					]);

					$saleOrder->applyFidelityProgram($row['id_venda']);

				} else {

					$id_venda = $saleOrder->Create([
						"frete" => 0,
						"id_entidade" => $id_entidade,
						"id_vendastatus" => SaleOrder::STATUS_MESA_EM_ANDAMENTO,
						"mesa" => $mesa,
					]);

					$table->Book($id_mesa, $id_venda, $GLOBALS['authorized_id_entidade']);
					// $table->Update([
					// 	"field" => "id_venda",
					// 	"id_mesa" => $id_mesa,
					// 	"value" => $id_venda
					// ]);
				}

				if ($window == "waiter_order_products") {

					LoadWaiterTableOrder($id_mesa);

				} elseif ($window == "waiter_order_revision") {

					if (key_exists("products", $_POST)) {

						$products = $_POST['products'];

					} else {

						$products = [];
					}

					LoadWaiterTableOrderRevision($id_mesa, $products);
				}

			} else {

				Notifier::Add("Erro ao regitrar cliente!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Cliente não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "waitertable_entity_search_cancel":

		$id_mesa = $_POST['id_mesa'];
		$window = $_POST['window'];

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			if ($row['id_venda']) {

				$saleOrder = new SaleOrder();

				$saleOrder->Read($row['id_venda']);

				if ($row = $saleOrder->getResult()) {

					$tplWaiterOrder = new View('templates/waiter_order');

					if ($row['id_entidade'] == null) {

						$extra_block_waiterorder_entity = $tplWaiterOrder->getContent(['window' => $window], "EXTRA_BLOCK_WAITERORDER_ENTITY_NONE");

					} else {

						$entity = new Entity();

						$entity->Read($row['id_entidade']);

						if ($rowEntity = $entity->getResult()) {

							$rowEntity = Entity::FormatFields($rowEntity);

							$tplEntity = new View("templates/entity");

							$rowEntity["window"] = $window;
							$rowEntity["block_entity_nome"] = $tplEntity->getContent($rowEntity, "BLOCK_ENTITY_NOME");

							$extra_block_waiterorder_entity = $tplWaiterOrder->getContent($rowEntity, "EXTRA_BLOCK_WAITERORDER_ENTITY");
						}
					}
				}
			}
		}

		Send($extra_block_waiterorder_entity);
		break;

	case "waitertable_entity_del":

		$id_mesa = $_POST['id_mesa'];
		$window = $_POST['window'];
		$versao = $_POST["versao"];

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			$saleOrder = new SaleOrder();

			if ($row['id_venda']) {

				if (!$saleOrder->CheckVersion($row["id_venda"], $versao)) {

					Send(null);
				}

				$saleOrder->Update($row['id_venda'], "id_entidade", null);

				$saleOrder->applyFidelityProgram($row['id_venda']);
			}

			if ($window == "waiter_order_products") {

				LoadWaiterTableOrder($id_mesa);

			} elseif ($window == "waiter_order_revision") {

				if (key_exists("products", $_POST)) {

					$products = $_POST['products'];

				} else {

					$products = [];
				}

				LoadWaiterTableOrderRevision($id_mesa, $products);
			}

		} else {

			Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		break;

	case "waitertable_table_products":

		$id_mesa = $_POST['id_mesa'];

		LoadWaiterTableOrder($id_mesa);

		break;

	case "waiterproduct_item_reverse":

		$id_entidade = $_POST['auth_id'];
		$pass = $_POST['auth_pass'];

		ControlAccess::CheckAuth($id_entidade, $pass, ControlAccess::CA_PDV_CANCELA_ITEM);

		$id_mesa = $_POST['id_mesa'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$versao = $_POST["versao"];

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			$saleOrder = new SaleOrder();

			if (!$saleOrder->CheckVersion($row["id_venda"], $versao)) {

				Send(null);
			}

			$saleItem = new SaleOrderItem();

			$saleItem->Delete($row['id_venda'], $id_vendaitem);
		}

		LoadWaiterTableOrder($id_mesa);

		break;

	case "waitertable_table_close":

		$id_mesa = $_POST['id_mesa'];
		$versao = $_POST['versao'];

		$table = new Table();

		$table->Read($id_mesa);

		if ($row = $table->getResult()) {

			if ($row['id_venda']) {

				$sale = new SaleOrder();

				if (!$versao = $sale->CheckVersion($row['id_venda'], $versao)) {

					Send(null);
				}

				$saleItem = new SaleOrderItem();

				$saleItem->getListActiveItems($row['id_venda']);

				if ($saleItem->getResult()) {

					$sale->ChangeStatus($row['id_venda'], SaleOrder::STATUS_MESA_EM_PAGAMENTO);

					$printer = new PrinterConfig();

					$printer->getPrinting(PrinterConfig::PRINTING_TABLE);

					if (($rowPrinter = $printer->getResult()) && !is_null($rowPrinter["id_impressora"])) {

						SaleOrder::DoPrint($row['id_venda'], $rowPrinter['id_impressora'], false);
					}

					Notifier::Add($row['mesa'] . "<br>Fechada para pagamento", Notifier::NOTIFIER_DONE);
					Send(Table::LoadWaiterTable("waiter_table"));

				} else {

					Notifier::Add("Não há item na mesa!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "table_smart_search_popup":

		$value = Clean::HtmlChar(trim($_POST['value']));
		$value = Clean::DuplicateSpace($value);

		$table = new Table();

		$tplTable = new View("templates/waiter_table");

		$table->Search($value);

		$table_list = "";

		if ($row = $table->getResult()) {

			do {

				$row = Table::FormatFields($row);

				$table_list.= $tplTable->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH");

			} while ($row = $table->getResult());

		} else {

			$table_list = $tplTable->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND");
		}

		Send($table_list);

		break;


	case "table_smart_search":

		$value = Clean::HtmlChar(trim($_POST['value']));
		$value = Clean::DuplicateSpace($value);
		$screen = $_POST['screen'];

		$table = new Table();

		$tplTable = new View("templates/waiter_table");

		$table->Search($value);

		$table_list = "";

		if ($row = $table->getResult()) {

			// $tplSelfService = new View('templates/waiter_self_service');

			do {

				$row['screen'] = $screen;
				$row = Table::FormatFields($row);

				if ($row['id_venda']) {

					if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

						$table_list .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_BUSY");

					} else if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

						$table_list .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_PAYMENT");
					}

				} else {

					$table_list .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_FREE");
				}

				// if ($screen == "waiter_table") {

				// 	$table_list.= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE");

				// } else if ($screen == "selfservice") {

				// 	$table_list.= $tplSelfService->getContent($row, "EXTRA_BLOCK_TABLE");
				// }


			} while ($row = $table->getResult());

		} else {

			$table_list = $tplTable->getContent([], "EXTRA_BLOCK_TABLE_NOTFOUND");
		}

		Send($table_list);

		break;

	case "waitersector_popup_peso":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			$tplTable = new View("templates/waiter_sector");

			Send($tplTable->getContent($row, "EXTRA_BLOCK_POPUP_WAITERSECTOR_PESO"));

		} else {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}