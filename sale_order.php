<?php

use database\ControlAccess;
use App\View\View;
use database\SaleOrder;
use database\SaleOrderStatusChange;
use database\SaleOrderItem;
use database\SaleOrderAddress;
use database\Entity;
use database\EntityAddress;
use database\Freight;
use database\PaymentKind;
use database\Product;
use database\Log;
use database\PrinterConfig;
use database\SaleOrderPay;
use database\Calc;
use database\Notifier;
use database\ProductComplement;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

function SaleOrderGet(SaleOrder $sale, $menu_view) {

	$response = null;

	$tplSale = new View('templates/sale_order');

	// $total = 0;

	if ($row = $sale->getResult()) {

		$response = "";

		do {

			$row = SaleOrder::FormatFields($row);

			SaleOrder::getMenu($row, $menu_view);

			$row['extra_block_saleaddress'] = SaleOrderAddress::getSaleAddress($row['id_venda']);

			$entity = new Entity();

			$entity->Read($row['id_entidade']);

			if ($rowEntity = $entity->getResult()) {

				$rowEntity = Entity::FormatFields($rowEntity);

				$row['nome'] = $rowEntity['nome'];
				$row['extra_block_entity_button_status'] = $rowEntity['extra_block_entity_button_status'];
				$row['credito_formatted'] = $rowEntity['credito_formatted'];
			}

			switch ($row['id_vendastatus']) {

				case SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO:

					// $total += $row['total'];
					$row['ticket_marker'] = "marker-red";
					//$row['extra_block_saleorder_menu'] = SaleOrder::getMenu($row, false, true);// $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_ANDAMENTO");

					// $response.= $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_TICKET");

					break;

				case SaleOrder::STATUS_PEDIDO_EFETUADO:
				case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED

					// if ($row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EFETUADO) {

					// 	$row['status'] = "Fechado";

					// } else {

					// 	$row['status'] = "Impresso";
					// }

					// $total += $row['total'];
					$row['ticket_marker'] = "marker-green";

					// $response.= $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_TICKET");
					break;

				case SaleOrder::STATUS_PEDIDO_PRODUCAO:

					// $total += $row['total'];
					$row['ticket_marker'] = "marker-orange";

					// $response.= $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_TICKET");
					break;

				case SaleOrder::STATUS_PEDIDO_ENTREGA:

					// $total += $row['total'];
					$row['ticket_marker'] = "marker-blue";

					// $response.= $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_TICKET");
					break;
			}

			$response.= $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_TICKET");

		} while ($row = $sale->getResult());
	}

	return $response;
}

function Load($id_vendastatus, $id_venda = null) {

	$tplSale = new View('templates/sale_order');

	$sale = new SaleOrder();

	if ($id_venda != null) {

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$data = [
				"hidden_sale_order_not_found" => "hidden",
				"extra_block_orders" => SaleOrder::SaleOrderExpand($row, true)
			];

		} else {

			Notifier::Add("Erro ao abrir pedido para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	} else {

		$sale->getOrderList($id_vendastatus);

		if ($extra_block_orders = SaleOrderGet($sale, true)) {

			$data = [
				"hidden_sale_order_not_found" => "hidden",
				"extra_block_orders" => $extra_block_orders,
				// 'saleorder_total_formatted' => number_format($extra_block_orders['total'], 2, ',', '.')
			];

		} else {

			$data = [
				"hidden_sale_order_not_found" => "",
				"extra_block_orders" => "",
				// 'saleorder_total_formatted' => "0,00"
			];
		}
	}

	$tplEntity = new View('templates/entity');

	$data['block_entity_autocomplete_search'] = $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH");

	$data['total_andamento'] = $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO);
	$data['total_efetuado'] = $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO);
	$data['total_producao'] = $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO);
	$data['total_entrega'] = $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA);

	if ($id_venda == null) {

		$data['header'] = "Pedidos em Andamento";

	} else {

		$data['header'] = "Pedido";

		// $entity = new Entity();

		// $entity->Read($row['id_entidade']);

		// if ($row = $entity->getResult()) {

		// 	$data['header'] = $row['nome']; // "Pedido #" . $id_venda;
		// }
	}


	return $tplSale->getContent($data, "BLOCK_PAGE");
}

function SaleOrderClose($id_venda, $print) {

	$saleItem = new SaleOrderItem();

	if ($saleItem->countItens($id_venda) == 0) {

		Notifier::Add("Não é possível finalizar pedido sem itens.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$freight = new Freight();

	$freight->Read();

	if (!$rowFreight = $freight->getResult()) {

		Notifier::Add("Erro ao carregar dados de frete.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$saleOrderAddress = new SaleOrderAddress();

	$saleOrderAddress->Read($id_venda);

	$delivery = false;

	if ($row = $saleOrderAddress->getResult()) {

		$delivery = true;
	}

	$sale = new SaleOrder();

	$sale->Read($id_venda);

	if ($row = $sale->getResult()) {

		if ($delivery == true && $rowFreight["deliveryminimo"] == 1) {

			$subtotal = Calc::Sum([
				$row['subtotal'],
				- $row['desconto'],
			]);

			if ($subtotal < $rowFreight["deliveryminimo_valor"]) {

				$rowFreight = Freight::FormatFields($rowFreight);
				Notifier::Add("Pedido não atingiu valor mínimo de <br>R$ " . $rowFreight["deliveryminimo_valor_formatted"] . " para Delivery!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		if ($row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

			$salePayment = new SaleOrderPay();

			$salePayment->getTotal($id_venda);

			$rowPayment = $salePayment->getResult();

			$totalPayment = $rowPayment['total'];

			$totalSale = Calc::Sum([
				$row['subtotal'],
				- $row['desconto'],
				$row['frete'],
				$row['valor_servico']
			]);

			if ($totalPayment < $totalSale) {

				Notifier::Add("Forma de pagamento incompleta!", Notifier::NOTIFIER_ERROR);
				Send(null);

			} else if ($totalPayment > $totalSale) {

				Notifier::Add("Corrija a forma de pagamento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$sale->ChangeStatus($id_venda, SaleOrder::STATUS_PEDIDO_EFETUADO);

		} else {

			Notifier::Add("Não é possível fechar pedido sem status em andamento.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	}

	if ($print == true) {

		$printer = new PrinterConfig();

		$printer->getPrinting(PrinterConfig::PRINTING_SALEORDER);

		if ($rowPrinter = $printer->getResult()) {

			if (is_null($rowPrinter['id_impressora'])) {

				Notifier::Add("Não há impressora configurada para impressão de pedidos!", Notifier::NOTIFIER_INFO);

			} else {

				if (!SaleOrder::DoPrint($id_venda, $rowPrinter['id_impressora'])) {

					Notifier::Add("Erro ao imprimir pedido!", Notifier::NOTIFIER_ERROR);
				}
			}

		} else {

			Notifier::Add("Erro ao imprimir pedido!", Notifier::NOTIFIER_ERROR);
		}
	}

	$sale->Read($id_venda);

	if ($extra_block_orders = SaleOrderGet($sale, true)) {

		if ($print == true) {

			$msg = "Pedido # $id_venda fechado e impresso!";

		} else {

			$msg = "Pedido # $id_venda fechado!";
		}

		$data = [
			'saleorder' => $extra_block_orders,
			'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		Notifier::Add($msg, Notifier::NOTIFIER_DONE);
		Send($data);

	} else {

		Notifier::Add("Erro ao carregar dados do pedido.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		Send(Load(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO));

		break;

	// case "saleorder_get":

	// 	$id_venda = $_POST['id_venda'];

	// 	$sale = new SaleOrder();

	// 	$sale->Read($id_venda);

	// 	if ($extra_block_orders = SaleOrderGet($sale)) {

	// 		Send($extra_block_orders);

	// 	} else {

	// 		Notifier::Add("Erro ao carregar dados do pedido.", Notifier::NOTIFIER_ERROR);
	// 		Send(null);
	// 	}

	// 	break;

	case "sale_order_items":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			Send(SaleOrder::SaleOrderExpand($row, true));

		} else {

			Notifier::Add("Erro ao abrir pedido para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$cliente = $_POST['cliente'];

		$from = $_POST['from'];

		$entity = new Entity();

		if( is_numeric($cliente) ) {

			$entity->Read($cliente);

		} else {

			$entity->ReadName($cliente);
		}

		if ($row = $entity->getResult()) {

			if ($row['ativo'] == 0) {

				Notifier::Add("Cliente inativo!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$data = [
				"id_entidade" => $row['id_entidade'],
				"frete" => 0,
				"id_vendastatus" => SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO,
			];

			$sale = new SaleOrder();

			if ($id_venda = $sale->Create($data)) {

				if (key_exists('id_endereco', $_POST)) {

					$id_endereco = $_POST['id_endereco'];

					$saleAddress = new SaleOrderAddress();
					$entityAddress = new EntityAddress();

					$entityAddress->Read($id_endereco);

					if ($row = $entityAddress->getResult()) {

						$saleAddress->Delete($id_venda);

						$saleAddress->Create([
							"id_venda" => $id_venda,
							"nickname" => $row['nickname'],
							"logradouro" => $row['logradouro'],
							"numero" => $row['numero'],
							"complemento" => $row['complemento'],
							"bairro" => $row['bairro'],
							"cidade" => $row['cidade'],
							"uf" => $row['uf'],
							"cep" => $row['cep'],
							"obs" => $row['obs'],
						]);

						$frete = $sale->applyFreight($id_venda);
					}
				}

				$sale->Read($id_venda);

				if ($row = $sale->getResult()) {

					$data = [
						'saleorder' => SaleOrder::SaleOrderExpand($row, true),
						'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
						'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
						'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
						'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA),
					];

					Send($data);

				} else {

					Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao criar pedido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Não foi possível localizar o cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_endereco_select_open":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$id_entidade = $row['id_entidade'];

			$tplSale = new View("templates/sale_order");

			$entityAddress = new EntityAddress();
			$tplEntity = new View("templates/entity");

			$entityAddress->getList($id_entidade);

			$address_list = "";

			while ($rowAddress = $entityAddress->getResult()) {

				$rowAddress = EntityAddress::FormatFields($rowAddress);

				$rowAddress['extra_block_button_sale_address'] = $tplEntity->getContent($rowAddress, "EXTRA_BLOCK_BUTTON_SALE_ADDRESS");
				$rowAddress['entity_bt_new_saleorder'] = "hidden";

				$address_list.= $tplEntity->getContent($rowAddress, "EXTRA_BLOCK_ADDRESS");
			}

			$row['tplentity_extra_block_address'] = $address_list;

			Send($tplSale->getContent($row, "EXTRA_BLOCK_ADDRESS_SELECTION"));

		} else {

			Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "sale_order_endereco_select":

		$id_venda = $_POST['id_venda'];
		$id_endereco = $_POST['id_endereco'];
		$versao = $_POST['versao'];

		$sale = New SaleOrder();

		if (!$versao = $sale->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleAddress = new SaleOrderAddress();
		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$saleAddress->Delete($id_venda);

			$saleAddress->Create([
				"id_venda" => $id_venda,
				"nickname" => $row['nickname'],
				"logradouro" => $row['logradouro'],
				"numero" => $row['numero'],
				"complemento" => $row['complemento'],
				"bairro" => $row['bairro'],
				"cidade" => $row['cidade'],
				"uf" => $row['uf'],
				"cep" => $row['cep'],
				"obs" => $row['obs'],
			]);

			$frete = $sale->applyFreight($id_venda);

			$tplSale = new View("templates/sale_order");

			$row['extra_block_saleaddress'] = SaleOrderAddress::getSaleAddress($id_venda);

			$row['id_venda'] = $id_venda;

			$sale->Read($id_venda);

			if ($rowSale = $sale->getResult()) {

				$data = [
					"address" => $tplSale->getContent($row, "BLOCK_ADDRESS"),
					"frete" => $frete,
					"frete_formatted" => number_format($frete, 2, ',', '.'),
					"versao" => $versao
				];

				Send($data);
			}

		} else {

			Notifier::Add("Erro ao carregar endereço para o pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_endereco_delete":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];

		$sale = New SaleOrder();

		if (!$versao = $sale->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleAddress = new SaleOrderAddress();

		$saleAddress->Delete($id_venda);

		$frete = $sale->applyFreight($id_venda);

		$tplSale = new View("templates/sale_order");

		$data['extra_block_saleaddress'] = $tplSale->getContent([], "EXTRA_BLOCK_NO_SALEADDRESS");

		$data['id_venda'] = $id_venda;

		$sale->Read($id_venda);

		if ($rowSale = $sale->getResult()) {

			$data = [
				"address" => $tplSale->getContent($data, "BLOCK_ADDRESS"),
				"frete" => $frete,
				"frete_formatted" => number_format($frete, 2, ',', '.'),
				"versao" => $versao
			];
		}

		Send($data);

		break;

	case "saleorder_prazo":

		// ControlAccess::Check(ControlAccess::CA_CLIENTE_LIMITE);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->getListActiveItems($id_venda);

		if (!$saleItem->getResult()) {

			Notifier::Add("Pedido sem item!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($rowSale = $saleOrder->getResult()) {

			$saleOrderDebits = new SaleOrder();

			$saleOrderDebits->getSalesOnCreditByEntity($rowSale['id_entidade']);

			$debits = 0;

			while ($rowDebits = $saleOrderDebits->getResult()) {

				$debits = Calc::Sum([
					$debits,
					$rowDebits['subtotal'],
					$rowDebits['frete'],
					$rowDebits['valor_servico'],
					-$rowDebits['desconto']
				]);
			}

			$entity = new Entity();

			$entity->Read($rowSale['id_entidade']);

			if ($rowEntity = $entity->getResult()) {

				$total = Calc::Sum([
					$rowSale['subtotal'],
					$rowSale['frete'],
					$rowSale['valor_servico'],
					-$rowSale['desconto']
				]);

				$limite = Calc::Sum([
					$rowEntity['limite'],
					-$debits
				]);

				if ($total <= $limite || ControlAccess::Check(ControlAccess::CA_VENDA_PRAZO_SEM_LIMITE, true)) {

					if ($saleOrder->VendaPrazo($id_venda)) {

						$data = [
							'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
							'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
							'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
							'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
						];

						Notifier::Add("Pedido convertido para venda a prazo!", Notifier::NOTIFIER_DONE);
						Send($data);

					} else {

						Notifier::Add("Pedido não pode ser convertido para venda a prazo!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}

				} else {

					if ($limite < 0) {

						$limite = 0;
					}

					Notifier::Add("Cliente sem limite disponível!<br>Limite: R$ " . number_format($rowEntity['limite'], 2, ",", ".") . "<br>Usado: R$ " . number_format($debits, 2, ',', '.') . "<br>Limite disponível: R$ " . number_format($limite, 2, ',', '.'), Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

		} else {

			Notifier::Add("Error ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);

		}

		break;

	case "saleorder_obs_del":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Update($id_venda, 'obs', '');

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$row = SaleOrder::FormatFields($row);

			Send($row['extra_block_saleorder_obs']);

		} else {

			Notifier::Add("Erro ao carregar observação do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];
		$obs = $_POST['obs'];

		$sale = new SaleOrder();

		if (!$versao = $sale->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$sale->Update($id_venda, 'obs', $obs);

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$row = SaleOrder::FormatFields($row);
			// $tplSale = new View("templates/sale_order");

			// Send($tplSale->getContent($row, "BLOCK_OBS"));
			Send([
				"data" => $row['extra_block_saleorder_obs'],
				"versao" => $versao
				]);

		} else {

			Notifier::Add("Erro ao carregar observação do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	 	break;

	case "sale_order_item_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$produto = $_POST['produto'];
		$qtd = $_POST['qtd'];
		$obs = $_POST['obs'];
		$data = [];
		$versao = $_POST["versao"];

		$saleOrder = new SaleOrder();

		$product = new Product();

		if( is_numeric($produto) ) {

			$product->Read($produto);

		} else {

			$product->SearchByString($produto, true);
		}

		$tplSale = new View("templates/sale_order");

		if ($row = $product->getResult()) {

			if ($row['ativo'] == 0) {

				Notifier::Add("Produto está inativo para venda!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$productComp = new ProductComplement();
			$productComp2 = new ProductComplement();

			$productComp->getGroups($row['id_produto']);

			//Produto tem complemento.
			if ($rowComp = $productComp->getResult()) {

				$row = Product::FormatFields($row);
				$row["extra_block_complement_tr"] = "";

				do {

					$rowComp = ProductComplement::FormatFieldsComplementGroup($rowComp);

					// $rowComp["hidden"] = "";

					$rowComp["extra_block_complementgroup_product"] = "";

					$productComp2->getComplements($rowComp["id_complementogrupo"]);

					if ($rowComp2 = $productComp2->getResult()) {

						// $rowComp["hidden"] = "hidden";

						do {

							$rowComp2 = Product::FormatFields($rowComp2);

							$rowComp2['preco_complemento_formatted'] = number_format($rowComp2['preco_complemento'], 2, ",", ".");

							$rowComp["extra_block_complementgroup_product"] .= $tplSale->getContent($rowComp2, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT");

						} while ($rowComp2 = $productComp2->getResult());
					}

					$rowComp["extra_block_complement_tr_msg"] = match($rowComp['qtd_min']) {
						0 					=> $tplSale->getContent($rowComp, "EXTRA_BLOCK_COMPLEMENT_TR_MSG1"),
						$rowComp['qtd_max'] => $tplSale->getContent($rowComp, "EXTRA_BLOCK_COMPLEMENT_TR_MSG2"),
						default 			=> $tplSale->getContent($rowComp, "EXTRA_BLOCK_COMPLEMENT_TR_MSG3")
					};

					$row["extra_block_complement_tr"] .= $tplSale->getContent($rowComp, "EXTRA_BLOCK_COMPLEMENT_TR");

				} while ($rowComp = $productComp->getResult());

				$row["id_venda"] = $id_venda;
				$row["qtd"] = $qtd;
				$row["obs"] = $obs;

				$data["complemento"] = $tplSale->getContent($row, "EXTRA_BLOCK_POPUP_SALE_COMPLEMENT");

				Send($data);

			} else {

				if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

					Send(null);
				}

				$saleItem = new SaleOrderItem();

				$preco = ($row['preco_promo'] > 0)? $row['preco_promo']: $row['preco'];

				if ($id_item = $saleItem->Create($id_venda, $row['id_produto'], $row['id_produtotipo'], $qtd, $preco, $obs)) {

					$saleItem->Read($id_venda, $id_item);

					if ($row = $saleItem->getResult()) {

						$row = Product::FormatFields($row);

						$row = SaleOrderItem::FormatFields($row);

						$data["item"] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");
					}

				} else {

					Notifier::Add("Erro ao adicionar produto ao pedido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

		} else {

			Notifier::Add("Não foi possível localizar o produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data["frete"] = $row['frete'];
			$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
			// $data['payment'] = SaleOrder::LoadSaleOrderPayment($row);
		}

		$data["versao"] = $versao;

		Send($data);

	 break;

	case "sale_order_item_delete":

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$data = [];
		$versao = $_POST["versao"];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem;

		if ($saleItem->Delete($id_venda, $id_vendaitem)) {

			$saleItem->Read($id_venda, $id_vendaitem);

			if ($row = $saleItem->getResult()) {

				$row = Product::FormatFields($row);

				$row = SaleOrderItem::FormatFields($row);

				$tplSale = new View("templates/sale_order");

				$data["item"] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_REVERSED");

			} else {

				Notifier::Add("Erro ao carregador produto", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao estornar produto do pedido", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data["frete"] = $row['frete'];
			$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
			$data["versao"] =$versao;
		}

		Send($data);

		break;

	case "sale_order_item_restore":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$data = [];
		$versao = $_POST["versao"];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem;

		$data = [
			"id_venda" => $id_venda,
			"id_vendaitem" => $id_vendaitem,
			"field" => "estornado",
			"value" => 0,
		];

		if ($saleItem->Update($data)) {

			$saleItem->Read($id_venda, $id_vendaitem);

			if ($row = $saleItem->getResult()) {

				$product = new Product();

				$product->UpdateStockFromSale($id_venda, $id_vendaitem, -$row['qtd']);

				$row = Product::FormatFields($row);

				$row = SaleOrderItem::FormatFields($row);

				$tplSale = new View("templates/sale_order");

				$data["item"] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");

			} else {

				Notifier::Add("Erro ao carregar produto do pedido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao estornar produto do pedido", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data["frete"] = $row['frete'];
			$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
			$data["versao"] = $versao;
		}

		Send($data);

		break;

	case "sale_order_frete_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_FRETE);

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "EXTRA_BLOCK_FORM_FRETE"));

		} else {

			Notifier::Add("Erro ao carregar valor do frete do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_frete_edit_cancel":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$tplSale = new View("templates/sale_order");

			$row = SaleOrder::FormatFields($row);

			Send($tplSale->getContent($row, "BLOCK_FRETE"));

		} else {

			Notifier::Add("Erro ao carregar valor do frete do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_frete_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_FRETE);

		$id_venda = $_POST['id_venda'];
		$frete = $_POST['frete'];
		$versao = $_POST["versao"];

		$sale = new SaleOrder();

		if (!$versao = $sale->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$sale->Update($id_venda, 'frete', $frete);

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$tplSale = new View("templates/sale_order");

			$row = SaleOrder::FormatFields($row);

			Send([
				"data" => $tplSale->getContent($row, "BLOCK_FRETE"),
				"versao" => $versao
			]);

		} else {

			Notifier::Add("Erro ao carregar valor do frete do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	// case "saleorderitem_obs_del":

	// 	ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

	// 	$id_venda = $_POST['id_venda'];
	// 	$id_vendaitem = $_POST['id_vendaitem'];

	// 	$sale = new SaleOrderItem();

	// 	$sale->Update([
	// 		'id_venda' => $id_venda,
	// 		'id_vendaitem' => $id_vendaitem,
	// 		'field' => 'obs',
	// 		'value' => ''
	// 	]);

	// 	$sale->Read($id_venda, $id_vendaitem);

	// 	if ($row = $sale->getResult()) {

	// 		$row = SaleOrderItem::FormatFields($row);

	// 		Send($row['extra_block_saleorderitem_obs']);

	// 	} else {

	// 		Notifier::Add("Erro ao carregar observação do item!", Notifier::NOTIFIER_ERROR);
	// 		Send(null);
	// 	}

	// 	break;

	case "sale_order_item_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$obs = $_POST['obs'];
		$versao = $_POST["versao"];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->Update([
			'id_venda' => $id_venda,
			'id_vendaitem' => $id_vendaitem,
			'field' => 'obs',
			'value' => $obs,
		]);

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);
			// $tplSale = new View('templates/sale_order');

			// Send($tplSale->getContent($row, "BLOCK_ITEM_OBS"));
			Send([
				"data" => $row['extra_block_saleorderitem_obs'],
				"versao" => $versao
			]);

		} else {

			Notifier::Add("Erro ao carregar obsevação do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_qtd_edit":

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "EXTRA_BLOCK_FORM_ITEM_QTD"));

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_qtd_cancel":

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "BLOCK_ITEM_QTD"));

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_qtd_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$qtd = $_POST['qtd'];
		$data = [];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$product = new Product();

			$product->UpdateStockFromSale($id_venda, $id_vendaitem, $row['qtd'] - $qtd);
		}

		$saleItem->Update([
			'id_venda' => $id_venda,
			'id_vendaitem' => $id_vendaitem,
			'field' => "qtd",
			'value' => $qtd,
		]);

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = Product::FormatFields($row);

			$row = SaleOrderItem::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			$data["item"] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data["frete"] = $row['frete'];
			$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
		}

		$data["versao"] = $versao;

		Send($data);

		break;

	case "sale_order_item_preco_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "EXTRA_BLOCK_FORM_ITEM_PRECO"));

		} else {

			Notifier::Add("Erro ao carregar preço do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_preco_cancel":

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "BLOCK_ITEM_PRECO"));

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_preco_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$preco = $_POST['preco'];
		$data = [];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->Update([
			'id_venda' => $id_venda,
			'id_vendaitem' => $id_vendaitem,
			'field' => "preco",
			'value' => $preco,
		]);

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$row = Product::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			$data["item"] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data["frete"] = $row['frete'];
			$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
		}

		$data["versao"] = $versao;

		Send($data);

		break;

	case "sale_order_item_desconto_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "EXTRA_BLOCK_FORM_ITEM_DESCONTO"));

		} else {

			Notifier::Add("Erro ao carregar desconto do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_desconto_cancel":

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			Send($tplSale->getContent($row, "BLOCK_ITEM_DESCONTO"));

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_item_desconto_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO);

		$id_venda = $_POST['id_venda'];
		$id_vendaitem = $_POST['id_vendaitem'];
		$desconto = $_POST['desconto'];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			if ($desconto > round($row['qtd']*$row['preco'],2)) {

				Notifier::Add("Desconto não pode ser maior que o valor de venda!", Notifier::NOTIFIER_ERROR);
				Send(null);

			}

		} else {

			Notifier::Add("Erro ao carregar dados do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);

		}

		$saleItem->Update([
			'id_venda' => $id_venda,
			'id_vendaitem' => $id_vendaitem,
			'field' => "desconto",
			'value' => $desconto,
		]);

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			$row = SaleOrderItem::FormatFields($row);

			$row = Product::FormatFields($row);

			$tplSale = new View('templates/sale_order');

			$data['item'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");

			$saleOrder->Read($id_venda);

			if ($row = $saleOrder->getResult()) {

				$data["frete"] = $row['frete'];
				$data["frete_formatted"] = number_format($row['frete'], 2, ',', '.');
			}

			$data["versao"] = $versao;

			Send($data);

		} else {

			Notifier::Add("Erro ao carregar quantidade do item do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_close":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];
		$toprint = ($_POST['toprint'] == "false")? false: true;

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		SaleOrderClose($id_venda, $toprint);

		break;

	case "saleorder_open":

		$id_venda = $_POST['id_venda'];
		$id_entidade = $_POST['auth_id'];
		$pass = $_POST['auth_pass'];
		$versao = $_POST["versao"];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleOrder->Read($id_venda);

		// $termsale = false;

		if ($row = $saleOrder->getResult()) {

			if ($row['id_vendastatus'] == SaleOrder::STATUS_VENDA_PRAZO) {

				// ControlAccess::Check(ControlAccess::CA_VENDA_PRAZO_EDITAR, false);
				ControlAccess::CheckAuth($id_entidade, $pass, ControlAccess::CA_VENDA_PRAZO_EDITAR);

			} else {

				ControlAccess::CheckAuth($id_entidade, $pass, ControlAccess::CA_ORDEM_VENDA_EDITAR);
			}

			switch ($row['id_vendastatus']) {

				case SaleOrder::STATUS_PEDIDO_EFETUADO:
				case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED
				case SaleOrder::STATUS_VENDA_PRAZO:
				case SaleOrder::STATUS_PEDIDO_PRODUCAO:
				case SaleOrder::STATUS_PEDIDO_ENTREGA:

					$saleOrder->ChangeStatus($id_venda, SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO, $id_entidade);

					break;

				default:

					Notifier::Add("Não é possível abrir este pedido para edição!", Notifier::NOTIFIER_ERROR);
					Send(null);
					// break;
			}
		}

		$data = [
			'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		Send($data);

		break;

	case "saleorder_statushistory":

		$id_venda = $_POST['id_venda'];

		$saleOrder = new SaleOrder();
		$saleOrderStatus = new SaleOrderStatusChange();

		$tplSale = new View('templates/sale_order');

		$saleOrder->ReadOnly($id_venda);

		if ($row = $saleOrder->getResult()) {

			$data_formatted = date_format(date_create($row['data']),'d/m/Y H:i');

		} else {

			Notifier::Add("Erro ao carregar dados de venda.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrderStatus->Read($id_venda);

		$list = "";

		while ($row = $saleOrderStatus->getResult()) {

			$row["data_formatted"] = date_format(date_create($row['data']),'d/m/Y H:i');

			$row["obs"] = "";

			switch ($row['id_vendastatus']) {

				case SaleOrder::STATUS_MESA_CANCELADA:
				case SaleOrder::STATUS_PEDIDO_CANCELADO:
				case SaleOrder::STATUS_VENDA_CANCELADA:

					$log = new Log();

					$log->getEstornoVenda($row["id_venda"]);

					if ($rowLog = $log->getResult()) {

						$logObject = json_decode($rowLog['log']);

						if (property_exists($logObject, "obs")) {

							$row["obs"] = $logObject->{'obs'};
						}
					}

				break;
			}

			$list .= $tplSale->getContent($row, "EXTRA_BLOCK_STATUSHISTORY_LIST");
		}

		if ($list == "") {

			$list = $tplSale->getContent([], "EXTRA_BLOCK_STATUSHISTORY_NOTFOUND");
		}

		$data = [
			"data_formatted" => $data_formatted,
			"id_venda" => $id_venda,
			"extra_block_statushistory_list" => $list
		];

		Send($tplSale->getContent($data, "EXTRA_BLOCK_STATUSHISTORY"));

		break;

	case "saleorder_delete":

		$id_entidade = $_POST['auth_id'];
		$pass = $_POST['auth_pass'];
		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];
		$obs = $_POST["obs"];
		$page = $_POST["page"];

		$saleOrder = new SaleOrder();

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			if (empty($id_entidade)) {

				$need_authentication = true;

				if ($row["id_vendastatus"] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

					$saleStatusChange = new SaleOrderStatusChange();

					$count = $saleStatusChange->countStatus($id_venda, SaleOrder::STATUS_PEDIDO_EFETUADO);

					if ($count == 0) {

						$need_authentication = false;
					}
				}

				if ($need_authentication == true) {

					Notifier::Add("Autenticação necessária!", Notifier::NOTIFIER_INFO);
					Send(null);

				} else {

					$id_entidade = $GLOBALS['authorized_id_entidade'];
				}

			} else {

				ControlAccess::CheckAuth($id_entidade, $pass, ControlAccess::CA_PDV_CANCELA_VENDA);
			}

			if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

				Send(null);
			}

			if ($saleOrder->Delete($id_venda, $obs, $id_entidade)) {

				switch($page) {

					case "sale_order.php":

						$data = [
							// "credito" => $credito,
							'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
							'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
							'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
							'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA),
							// 'saleorder' => $tplSaleCoupon->getContent($row, "EXTRA_BLOCK_REPORTSALE")
						];

						Notifier::Add("Cupom # $id_venda cancelado com sucesso.", Notifier::NOTIFIER_DONE);

						Send($data);

						break;

					case "report_sale_coupon.php":

						$credito = null;

						$saleOrder->Read($id_venda);

						if ($row = $saleOrder->getResult()) {

							$tplSaleCoupon = new View('templates/report_sale_coupon');
							$tplSale = new View('templates/sale_order');

							$row = SaleOrder::FormatFields($row);

							if ($row['id_entidade'] == null) {

								$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent([], "EXTRA_BLOCK_SALEORDER_ENTITY_NONE");
								// $credito = 0;

							} else {

								$entity = new Entity();

								$entity->Read($row['id_entidade']);

								if ($rowEntity = $entity->getResult()) {

									$rowEntity = Entity::FormatFields($rowEntity);

									$row['extra_block_saleorder_entity'] = $tplSaleCoupon->getContent($rowEntity, "EXTRA_BLOCK_SALEORDER_ENTITY");
									$row['nome'] = $rowEntity['nome'];
									// $credito = $rowEntity['credito'];
								}
							}

							$row['total'] = 0;

							Notifier::Add("Cupom # $id_venda cancelado com sucesso.", Notifier::NOTIFIER_DONE);

							SaleOrder::getMenu($row, true);

							Send($tplSaleCoupon->getContent($row, "EXTRA_BLOCK_REPORTSALE"));

						} else {

							Notifier::Add("Erro ao ler dados do Pedido/Venda!", Notifier::NOTIFIER_ERROR);

							Send(null);
						}

					break;

					case "entity.php":

						$saleOrder->Read($id_venda);

						if ($row = $saleOrder->getResult()) {

							$tplSale = new View('templates/sale_order');

							Notifier::Add("Cupom # $id_venda cancelado com sucesso.", Notifier::NOTIFIER_DONE);

							$row = SaleOrder::FormatFields($row);

							SaleOrder::getMenu($row, true);

							Send($tplSale->getContent($row, "EXTRA_BLOCK_HISTORY_ORDER"));

						} else {

							Notifier::Add("Erro ao ler dados do Pedido/Venda!", Notifier::NOTIFIER_ERROR);

							Send(null);
						}

					break;

					case "bills_to_receive.php":

						Notifier::Add("Cupom # $id_venda cancelado com sucesso.", Notifier::NOTIFIER_DONE);

						Send([]);

					break;

					default:

						Notifier::Add("TODO: " . $page, Notifier::NOTIFIER_INFO);
						Send(null);
					break;
				}

			} else {

				Notifier::Add("Erro ao cancelar pedido/venda.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_delete_popup":

		$id_venda = $_POST['id_venda'];

		$saleOrder = new SaleOrder();

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			$row = SaleOrder::FormatFields($row);

			$tplSale = new View("templates/sale_order");

			Send($tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_REVERSE"));

		} else {

			Notifier::Add("Erro ao carregar dados da venda.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}


		break;

	case 'saleorder_producao':

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			if ($row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EFETUADO) {

				$saleOrder->ChangeStatus($id_venda, SaleOrder::STATUS_PEDIDO_PRODUCAO);

				$saleOrder->Read($id_venda);

				if ($extra_block_orders = SaleOrderGet($saleOrder, false)) {

					$data = [
						'saleorder' => $extra_block_orders,
						'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
						'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
						'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
						'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
					];

					Notifier::Add("Pedido # $id_venda em produção!", Notifier::NOTIFIER_DONE);
					Send($data);

				} else {

					Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao colocar pedido em produção!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		} else {

			Notifier::Add("Erro ao carregar dados da venda!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case 'saleorder_entrega':

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];

		$saleOrder = new SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($row = $saleOrder->getResult()) {

			if ($row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EFETUADO || $row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_PRODUCAO) {

				$saleOrder->ChangeStatus($id_venda, SaleOrder::STATUS_PEDIDO_ENTREGA);

				$saleOrder->Read($id_venda);

				if ($extra_block_orders = SaleOrderGet($saleOrder, false)) {

					$data = [
						'saleorder' => $extra_block_orders,
						'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
						'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
						'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
						'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
					];

					Notifier::Add("Pedido # $id_venda em entrega!", Notifier::NOTIFIER_DONE);
					Send($data);

				} else {

					Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao colocar pedido em entrega!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		} else {

			Notifier::Add("Erro ao carregar dados da venda!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_discountclear":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO);

		$id_venda = $_POST['id_venda'];
		$window = $_POST['window'];
		$versao = $_POST["versao"];

		$sale = new SaleOrder();

		if (!$versao = $sale->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleItem = new SaleOrderItem();

		$saleItem->DiscountClear($id_venda);

		$sale->Read($id_venda);

		if ($window == 'saleorder_show') {

			if ($row = $sale->getResult()) {

				Send([
					"data" => SaleOrder::SaleOrderExpand($row, true),
					"versao" => $versao
				]);

			} else {

				Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Send([
				"data" => SaleOrderGet($sale, false),
				"versao" => $versao
			]);
		}

		break;

	case "sale_order_whatsapp":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($rowSale = $sale->getResult()) {

			$rowSale = SaleOrder::FormatFields($rowSale);

			if (is_null($rowSale['id_entidade'])) {

				$rowSale['nome'] = "Varejo";

			} else {

				$entity = new Entity();

				$entity->Read($rowSale['id_entidade']);

				if ($rowEntity = $entity->getResult()) {

					$rowSale['nome'] = $rowEntity['nome'];
				}
			}

			$whatsapp = "*Cliente:* " . $rowSale['nome'];
			$whatsapp.= "%0A" . "*Pedido:* " . $rowSale['id_venda'] . " [" . $rowSale['dataonly_formatted'] . "]";

			$endereco = "%0A*Endereço:* Cliente retira na loja.";

			$saleAddress = new SaleOrderAddress();

			$saleAddress->Read($id_venda);

			$delivery = false;

			if ($row_address = $saleAddress->getResult()) {

				$delivery = true;

				$row_address = EntityAddress::FormatFields($row_address);

				$endereco = "%0A*Endereço:* " . $row_address['endereco'];

				// if (!empty($row_address['obs'])) {

				// 	$endereco_obs = "%0A*Obs.:* " . $row_address['obs'];
				// }
			}

			$whatsapp.= $endereco;

			if (!empty($endereco_obs)) {

				$whatsapp.= $endereco_obs;
			}

			$whatsapp.= "%0A%0A" . "*Itens:*";

			$saleItem = new SaleOrderItem();

			$saleItem->getListActiveItems($id_venda);

			$subtotal = $rowSale['subtotal'];
			$desconto = $rowSale['desconto'];
			$frete = $rowSale['frete'];
			$servico = $rowSale['valor_servico'];

			while ($rowItem = $saleItem->getResult()) {

				$rowItem['subtotal'] = number_format(round($rowItem['qtd'] * $rowItem['preco'],2),2,',','.');
				$rowItem['qtd'] = number_format($rowItem['qtd'],3,',','.');
				$rowItem['preco'] = number_format($rowItem['preco'],2,',','.');

				$whatsapp .= "%0A" . $rowItem['qtd'] . " | " . $rowItem['produtounidade'] . " | " . $rowItem['produto'] . " | R$ " . $rowItem['subtotal'];
				// $stringRight = " | R$ " . $rowItem['subtotal'];
				// $size = 62 - (mb_strlen($stringLeft) + mb_strlen($stringRight));
				// $string = $stringLeft . substr($rowItem['produto'], 0, $size) . $stringRight;
				// $whatsapp.= "%0A" . $string;

				if (!empty($rowItem['obs'])) {

					$whatsapp.= "%0A" . "obs.: " . $rowItem['obs'];
				}

				$whatsapp.= "%0A" . str_repeat("- ", 31);
			}

			$whatsapp.= "%0A%0A" . "Subtotal:  R$ " . number_format($subtotal,2,',','.');

			if ($desconto > 0) {

				$whatsapp.= "%0A" . "Desconto [Fidelidade]:  R$ " . number_format($desconto,2,',','.');

				$subtotal = Calc::Sum([
					$subtotal,
					-$desconto
				]);
			}

			if ($delivery) {

				if ($frete > 0) {

					$whatsapp.= "%0A" . "Frete:  R$ " . number_format($frete,2,',','.');

				} else {

					$whatsapp.= "%0A" . "Frete:  Grátis";
				}

				$subtotal = Calc::Sum([
					$subtotal,
					$frete
				]);
			}

			if ($servico > 0) {

				$whatsapp.= "%0A" . "*Serviço:*  R$ " . number_format($servico, 2, ',', '.');

				$subtotal = Calc::Sum([
					$subtotal,
					$servico
				]);

			}

			$whatsapp.= "%0A%0A" . "*Total:  R$ " . number_format($subtotal,2,',','.') . "*";

			Send($whatsapp);

		} else {

			Notifier::Add("Erro ao pegar dados do pedido.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sale_order_history_order":

		$id_entidade = $_POST['id_entidade'];
		$id_venda = $_POST['id_venda'];
		$page = $_POST['page'];

		$tplSale = new View("templates/sale_order");

		$sale = new SaleOrder();

		$sale->getCouponsByEntity($id_entidade, $page - 1);

		$coupon = "";

		if ($row = $sale->getResult()) {

			do {
				if ($row["id_venda"] != $id_venda) {

					$row = SaleOrder::FormatFields($row);

					SaleOrder::getMenu($row, true);

					$coupon .= $tplSale->getContent($row, "EXTRA_BLOCK_HISTORY_ORDER");
				}

			} while ($row = $sale->getResult());

			$content['page'] = $page + 1;
			$content['id_entidade'] = $id_entidade;

			$coupon .= $tplSale->getContent($content, "EXTRA_BLOCK_BUTTON_LOAD_PAGE");

			Send($coupon);

		} else {

			if ($page == 1) {

				Notifier::Add("Não há histórico de vendas para este cliente!", Notifier::NOTIFIER_INFO);
				Send(null);

			} else {

				Notifier::Add("Não há mais histórico de vendas para este cliente!", Notifier::NOTIFIER_INFO);
				Send(null);
			}
		}

		break;

	case "saleorder_payment_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_venda = $_POST['id_venda'];
		$id_especie = $_POST['id_especie'];
		$valor_recebido = floatval($_POST['valor']);
		$versao = $_POST['versao'];

		if ($valor_recebido == 0) {

			Notifier::Add("Valor não pode ser zero!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder = New SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$saleOrder->Read($id_venda);

		if ($rowSale = $saleOrder->getResult()) {

			$totalSale = Calc::Sum([
				$rowSale['subtotal'],
				$rowSale['valor_servico'],
				$rowSale['frete'],
				- $rowSale['desconto']
			]);

			$salePayment = new SaleOrderPay();

			$salePayment->getTotal($id_venda);

			$row = $salePayment->getResult();

			$totalPayment = $row['total'];

			if ($totalPayment >= $totalSale) {

				Notifier::Add("Pedido já pago!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$valor = $valor_recebido;

			$resta = Calc::Sum([
				$totalSale,
				-$totalPayment
			]);

			if ($valor_recebido > $resta) {

				// Money
				if ($id_especie == 1) {

					$valor = $resta;

				} else {

					Notifier::Add("Valor de pagamento não pode ser maior que valor do pedido!", Notifier::NOTIFIER_INFO);
					Send(null);
				}
			}

			$credito = null;

			// Entity Credit
			if ($id_especie == 2) {

				$entity = new Entity();

				$entity->Read($rowSale['id_entidade']);

				if ($rowEntity = $entity->getResult()) {

					if ($valor_recebido > $rowEntity['credito']) {

						Notifier::Add("Crédito insuficiente!<br> Crédito atual R$ " . number_format($rowEntity['credito'], 2, ",", "."), Notifier::NOTIFIER_ERROR);
						Send(null);
					}

					$entity->setCredito($rowSale['id_entidade'], -$valor_recebido, "Uso de crédito no pedido " . $rowSale['id_venda'], $GLOBALS["authorized_id_entidade"]);

					$credito = Calc::Sum([
						$rowEntity['credito'],
						-$valor_recebido
					]);

				} else {

					Notifier::Add("Erro ao carregador informação do cliente!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

			if ($salePayment->Create($id_venda, $id_especie, $valor, $valor_recebido)) {


				Send([
					"data" => SaleOrder::LoadSaleOrderPayment($rowSale), "Pagamento registrado", Notifier::NOTIFIER_DONE,
					"credito" => $credito,
					"versao" => $versao
				]);

			} else {

				Notifier::Add("Erro ao registrar pagamento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao ler dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_payment_del":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);

		$id_vendapay = $_POST['id_vendapay'];
		$id_venda = $_POST['id_venda'];
		$versao = $_POST['versao'];
		$credito = null;

		$saleOrder = New SaleOrder();

		if (!$versao = $saleOrder->CheckVersion($id_venda, $versao)) {

			Send(null);
		}

		$salePayment = new SaleOrderPay();

		$salePayment->Read($id_vendapay);

		if ($row = $salePayment->getResult()) {

			$saleOrder->Read($row['id_venda']);

			if ($rowSale = $saleOrder->getResult()) {

				if ($rowSale['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

					if ($salePayment->Delete($id_vendapay)) {

						// Entity Credit
						if ($row['id_especie'] == 2) {

							$entity = new Entity();

							$entity->Read($rowSale['id_entidade']);

							if ($rowEntity = $entity->getResult()) {

								$credito = $rowEntity['credito'];
							}
						}

						Send([
							"data" => SaleOrder::LoadSaleOrderPayment($rowSale), "Pagamento removido", Notifier::NOTIFIER_INFO,
							"credito" => $credito,
							"versao" => $versao
						]);

					} else {

						Notifier::Add("Error ao remover pagamento do pedido!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}

				} else {

					Notifier::Add("Não é possível remover o pagamento deste pedido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			} else {

				Notifier::Add("Erro ao carregar dados da venda!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados do pagamento!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_payment":

		$id_venda = $_POST['id_venda'];

		$saleorder = new SaleOrder();

		$saleorder->Read($id_venda);

		if ($row = $saleorder->getResult()) {

			Send(SaleOrder::LoadSaleOrderPayment($row));

		} else {

			Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_show":

		$id_venda = $_POST['id_venda'];

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			Send(SaleOrder::SaleOrderExpand($row, true));

		} else {

			Notifier::Add("Erro ao carregar dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "saleorder_show_andamento":

		$sale = new SaleOrder();

		$data = [
			"extra_block_orders" => "",
			'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		$sale->getOrderList(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO);

		if ($extra_block_orders = SaleOrderGet($sale, true)) {

			$data["extra_block_orders"] = $extra_block_orders;
		}

		Send($data);

		break;

	case "saleorder_show_efetuado":

		$sale = new SaleOrder();

		$data = [
			"extra_block_orders" => "",
			'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		$sale->getOrderList(SaleOrder::STATUS_PEDIDO_EFETUADO);

		if ($extra_block_orders = SaleOrderGet($sale, true)) {

			$data["extra_block_orders"] = $extra_block_orders;
		}

		Send($data);

		break;

	case "saleorder_show_producao":

		$sale = new SaleOrder();

		$data = [
			"extra_block_orders" => "",
			'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		$sale->getOrderList(SaleOrder::STATUS_PEDIDO_PRODUCAO);

		if ($extra_block_orders = SaleOrderGet($sale, true)) {

			$data["extra_block_orders"] = $extra_block_orders;
		}

		Send($data);

		break;

	case "saleorder_show_entrega":

		$sale = new SaleOrder();

		$data = [
			"extra_block_orders" => "",
			'total_andamento' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $sale->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		$sale->getOrderList(SaleOrder::STATUS_PEDIDO_ENTREGA);

		if ($extra_block_orders = SaleOrderGet($sale, true)) {

			$data["extra_block_orders"] = $extra_block_orders;
		}

		Send($data);

		break;

	case "saleorder_update_screen":

		$window = $_POST["window"];

		$saleOrder = new SaleOrder();

		$get_order_list = true;

		$data = [
			"extra_block_orders" => "",
			'total_andamento' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO),
			'total_efetuado' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_EFETUADO),
			'total_producao' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_PRODUCAO),
			'total_entrega' => $saleOrder->countCouponByStatus(SaleOrder::STATUS_PEDIDO_ENTREGA)
		];

		switch($window) {

			case "saleorder_andamento":

				$saleOrder->getOrderList(SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO);

				break;

			case "saleorder_efetuado":

				$saleOrder->getOrderList(SaleOrder::STATUS_PEDIDO_EFETUADO);

				break;

			case "saleorder_producao":

				$saleOrder->getOrderList(SaleOrder::STATUS_PEDIDO_PRODUCAO);

				break;

			case "saleorder_entrega":

				$saleOrder->getOrderList(SaleOrder::STATUS_PEDIDO_ENTREGA);

				break;

			default:

				$get_order_list = false;

				break;

		}

		if ($get_order_list == true) {

			if ($extra_block_orders = SaleOrderGet($saleOrder, true)) {

				$data['extra_block_orders'] = $extra_block_orders;
			}
		}

		Send($data);

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    	break;
}