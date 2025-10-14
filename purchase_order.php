<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\PurchaseOrder;
use database\PurchaseOrderItem;
use database\PurchaseList;
use database\PurchaseListItem;
use database\Product;
use database\ProductType;
use database\ProductComposition;
use database\ProductKit;
use database\Provider;
use database\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_COMPRA);

function PurchaseOrderGetCostHistory($row) {

	$purchaseItem = new PurchaseOrderItem();

	$purchaseItem->getLastProductEntry($row['id_produto']);

	$row['custohistory'] = 0;
	$row['custohistoryun'] = 0;

	if ($rowHistory = $purchaseItem->getResult()) {

		$row['custohistory'] = $rowHistory['custo'];

		if ($rowHistory['qtdvol'] > 0) {

			$row['custohistoryun'] = round($rowHistory['custo'] / $rowHistory['qtdvol'], 2);

		// } else {

		// 	$row['custohistoryun'] = 0;
		}

	// } else {

	// 	$row['custohistory'] = 0;
	// 	$row['custohistoryun'] = 0;
	}

	$row['custohistory_formatted'] = number_format($row['custohistory'], 2, ",", ".");
	$row['custohistoryun_formatted'] = number_format($row['custohistoryun'], 2, ",", ".");

	return $row;
}

function PurchaseOrderGet(PurchaseOrder $purchase) {

	if ($row = $purchase->getResult()) {

		$total = 0;

		$tplPurchase = new View('templates/purchase_order');

		$response = "";

		do {

			$row = PurchaseOrder::FormatFields($row);

			switch ($row['id_comprastatus']) {

				case PurchaseOrder::COMPRA_STATUS_ABERTA:

					$total += $row['total'];

					$response.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ABERTO");

				break;

				case PurchaseOrder::COMPRA_STATUS_FINALIZADA:

					$total += $row['total'];

					$response.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_FECHADO");

				break;

				case PurchaseOrder::COMPRA_STATUS_CANCELADA:

					$row['total'] = 0;

					$response.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_CANCELADO");

				break;
			}

		} while ($row = $purchase->getResult());

		return [
			"response" => $response,
			"total" => $total
		];

	} else {

		return null;
	}
}

function PurchaseOrderGetComposition($row) {

	$composition = "";
	$tplProduct = new View("templates/product");

	if ($row['id_produtotipo'] == ProductType::PRODUTO) {

		$productComposition = new ProductComposition();

		$productComposition->having($row['id_produto']);

		if ($rowComposition = $productComposition->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			$product = new Product();

			do {

				 $product->Read($rowComposition['id_composicao']);

				 if ($rowProduct = $product->getResult()) {

					$result = PurchaseOrderItem::getCompositionCost($row['id_compraitem'], $rowComposition['id_composicao']);

					$rowProduct['custo_unidade'] = $result[0];
					$rowProduct['custo_unidade_ajustado'] = $result[1];

					if ($rowProduct['custo_unidade'] == $rowProduct['custo_unidade_ajustado']) {

						$rowProduct['custo_ajustado_visible'] = "hidden";
					}

					$rowProduct = Product::FormatFields($rowProduct);

					$rowProduct = PurchaseOrder::FormatCost($rowProduct);

					// $rowProduct['qtdvol'] = $rowComp['qtdvol'];
					// $rowProduct['vol'] = $rowComp["vol"];
					$rowProduct['id_compraitem'] = $row['id_compraitem'];
					$rowProduct['composicao'] = $row['produto'];

					$rowProduct["block_group_preco"] = $tplProduct->getContent($rowProduct, "BLOCK_GROUP_PRECO");
					// $rowProduct = PurchaseOrderItem::FormatFields($rowProduct);
					$rowProduct["block_product_menu"] = $tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_MENU");;

					$composition.= $tplPurchase->getContent($rowProduct, "EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_COMPOSITION");
				 }

			} while ($rowComposition = $productComposition->getResult());
		}
	}

	return $composition;
}

function PurchaseOrderProductList(PurchaseOrder $purchase) {

	if ($row = $purchase->getResult()) {

		$tplPurchase = new View("templates/purchase_order");
		$tplProduct = new View("templates/product");

		$row = PurchaseOrder::FormatFields($row);

		$compraStatus = $row['id_comprastatus'];

		$data['id_compra'] = $row['id_compra'];
		$data['data_formatted'] = $row['data_formatted'];
		$data['obs'] = $row['obs'];

		$product_tr = "";

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->getItems($row['id_compra']);

		$total = 0;

		switch ($compraStatus) {

			case PurchaseOrder::COMPRA_STATUS_ABERTA:

				// $purchaseItem2 = New PurchaseOrderItem();

				if ($row = $purchaseItem->getResult()) {

					do {

						$row = PurchaseOrderGetCostHistory($row);

						$total+= round($row['vol'] * $row['custo'], 2);

						$row = Product::FormatFields($row);

						$row = PurchaseOrderItem::FormatFields($row);

						$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

						$product_tr.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER_ITEM");

					} while ($row = $purchaseItem->getResult());

				} else {

					$product_tr = $tplPurchase->getContent([], "EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE");
				}

				$data['total'] = number_format($total,2,',','.');

				$tplProduct = new View("templates/product");

				$data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

				$data["extra_block_product_form"] = $tplPurchase->getContent($data, "EXTRA_BLOCK_PRODUCT_FORM");

				$data['extra_block_purchase_aberto_container_item'] = $product_tr;

				$response = $tplPurchase->getContent($data, "EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER");

			break;

			case PurchaseOrder::COMPRA_STATUS_CANCELADA:

				if ($row = $purchaseItem->getResult()) {

					do {

						$total+= round($row['vol'] * $row['custo'], 2);

						$row = Product::FormatFields($row);
						$row = PurchaseOrderItem::FormatFields($row);
						$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

						$product_tr.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER_ITEM");

					} while ($row = $purchaseItem->getResult());

				} else {

					$product_tr = $tplPurchase->getContent([], "EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE");
				}

				$data['total'] = number_format($total,2,',','.');

				$data["extra_block_product_form"] = "";

				$data['extra_block_purchase_cancelado_container_item'] = $product_tr;

				$response = $tplPurchase->getContent($data, "EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER");

			break;

			case PurchaseOrder::COMPRA_STATUS_FINALIZADA:

				if ($row = $purchaseItem->getResult()) {

					do {

						$total+= round($row['vol'] * $row['custo'], 2);

						$row = Product::FormatFields($row);
						$row = PurchaseOrderItem::FormatFields($row);

						$row['extra_block_purchase_fechado_container_composition'] = PurchaseOrderGetComposition($row);
						$row["block_group_preco"] = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");
						$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

						$product_tr.= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_ITEM");

					} while ($row = $purchaseItem->getResult());

				} else {

					$product_tr = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE");
				}

				$data['total'] = number_format($total,2,',','.');

				$data["extra_block_product_form"] = "";

				$data['extra_block_purchase_fechado_container_item'] = $product_tr;

				$response = $tplPurchase->getContent($data, "EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER");
			break;
		}

		return array (
			"data" => $response,
			"comprastatus" => $compraStatus,
		);

	} else {

		return null;
	}
}

function PurchaseOrderGetItem($row, $id_vendastatus) {

	$tplPurchase = new View('templates/purchase_order');
	$tplProduct = new View('templates/product');

	$ret = null;

	switch ($id_vendastatus) {

		case PurchaseOrder::COMPRA_STATUS_ABERTA:

			$row = PurchaseOrderGetCostHistory($row);

			$row = Product::FormatFields($row);
			$row = PurchaseOrderItem::FormatFields($row);
			$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

			$ret = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER_ITEM");

			break;

		case PurchaseOrder::COMPRA_STATUS_FINALIZADA:

			$row = Product::FormatFields($row);
			$row = PurchaseOrderItem::FormatFields($row);

			$row['extra_block_purchase_fechado_container_composition'] = PurchaseOrderGetComposition($row);
			$row["block_group_preco"] = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");
			$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

			$ret = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_ITEM");

			break;

		case PurchaseOrder::COMPRA_STATUS_CANCELADA:

			$row = Product::FormatFields($row);
			$row = PurchaseOrderItem::FormatFields($row);

			$ret = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER_ITEM");

			break;
	}

	return $ret;
}

switch ($_POST['action']) {

	case "load":

		$tplPurchase = new View('templates/purchase_order');
		$tplProduct = new View('templates/product');

		$purchase = new PurchaseOrder();

		$purchase->getList();

		$total = 0;

		$purchase_notfound = "";

		$content = "";

		if ($response = PurchaseOrderGet($purchase)) {

			$content = $response['response'];
			$total = $response['total'];
			$purchase_notfound = "hidden";
		}

		$purchase->getListStatus();

		$listStatus = "";

		while ($row = $purchase->getResult()) {

			$listStatus .= $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_STATUS");
		}

		$data = [
			'date' => date('Y-m-d'),
			'purchase_notfound' => $purchase_notfound,
			'extra_block_purchase' => $content,
			'compra_status' => $listStatus,
			'purchaseorder_total_formatted' => number_format($total, 2, ',', '.'),
		];

		Send($tplPurchase->getContent($data, "BLOCK_PAGE"));

		break;

	case "purchase_order_list":

		$purchase = new PurchaseOrder();

		$tr = "";

		$purchase->getList();

		if ($response = PurchaseOrderGet($purchase)) {

			Send($response['response']);

		} else {

			Send([]);
		}

		break;

	case "purchase_order_new":

		$fornecedor = $_POST['fornecedor'];
		$data = $_POST['data'];
		$obs = $_POST['obs'];
		$lista = (int) $_POST['lista'];

		$provider = new Provider();

		if( is_numeric($fornecedor) ) {

			$provider->Read($fornecedor);

		} else {

			$provider->ReadName($fornecedor);
		}

		if (!$row = $provider->getResult()) {

			Notifier::Add("Não foi possível localizar o fornecedor:<br>$fornecedor", Notifier::NOTIFIER_INFO);
			Send(null);
			Notifier::Add("Não foi possível localizar o fornecedor:<br>$fornecedor", Notifier::NOTIFIER_INFO);
			Send(null);
		}

		$data = [
			"id_fornecedor" => $row['id_fornecedor'],
			"id_entidade" => $GLOBALS['authorized_id_entidade'],
			"id_comprastatus" => 1, //Status ABERTO
			"data" => $data,
			"obs" => $obs,
		];

		$purchase = new PurchaseOrder();

		$id_purchase = $purchase->Create($data);

		if ($id_purchase == 0) {

			Notifier::Add("Erro ao cadastrar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($lista > 0) {

			$listItem = new PurchaseListItem;
			$listItem->getItems($lista);

			$purchaseItem = new PurchaseOrderItem;

			while ($row = $listItem->getResult()) {

				$purchaseItem->getLastProductEntry($row['id_produto']);

				$qtdvol = 0;

				if ($row2 = $purchaseItem->getResult()) {

					$qtdvol = $row2['qtdvol'];
				}

				$data = [
					'id_compra' => $id_purchase,
					'id_produto' => $row['id_produto'],
					'qtdvol' => $qtdvol,
					'vol' => 0,
					'custo' => 0,
					'obs' => ''
				];

				$purchaseItem->Create($data);
			}
		}

		$purchase->Read($id_purchase);

		if ($response = PurchaseOrderGet($purchase)) {

			Notifier::Add("Ordem de compra adicionada!", Notifier::NOTIFIER_DONE);
			Send($response['response']);

		} else {

			Notifier::Add("Ococrreu um erro ao adicionar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_new_from_provider":

		$id_fornecedor = $_POST['id_fornecedor'];
		$obs = "";

		$provider = new Provider();

		$data = [
			"id_fornecedor" => $id_fornecedor,
			"id_entidade" => $GLOBALS['authorized_id_entidade'],
			"id_comprastatus" => PurchaseOrder::COMPRA_STATUS_ABERTA,
			"obs" => $obs,
		];

		$purchase = new PurchaseOrder();

		$id_purchase = $purchase->Create($data);

		if ($id_purchase == 0) {

			Notifier::Add("Erro ao cadastrar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$purchase->Read($id_purchase);

		if ($response = PurchaseOrderGet($purchase)) {

			$purchaseList = new PurchaseList();
			$purchaseList->getList();

			$list = "";

			while ($row = $purchaseList->getResult()) {

				$list .= "<option value='" . $row['id_compralista'] . "'>" . $row['descricao'] . "</option>";
			}

			$tplProvider = new View('templates/provider');

			$data = [
				'date' => date('Y-m-d'),
				'compra_lista' => $list,
				'extra_block_purchase' => "",
				'purchaseorder_total_formatted' => "0,00",
				'block_provider_autocomplete_search' => $tplProvider->getContent([], "BLOCK_PROVIDER_AUTOCOMPLETE_SEARCH")
			];

			$tplPurchase = new View('templates/purchase_order');

			Send([
				"page" => $tplPurchase->getContent($data, "BLOCK_PAGE"),
				"purchase_order" => $response['response']
			]);

		} else {

			Notifier::Add("Ocorreu um erro ao adicionar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchaseorder_estimate":

		$id_compra = $_POST['id_compra'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->getItems($id_compra);

		$total = 0;

		if ($row = $purchaseItem->getResult()) {

			do {

				$row = PurchaseOrderGetCostHistory($row);

				$total+= round($row['vol'] * $row['custohistory'], 2);

			} while ($row = $purchaseItem->getResult());
		}

		Notifier::Add("OC #$id_compra<br>Valor estimado: R$ " . number_format($total,2,',','.'), Notifier::NOTIFIER_INFO);
		Send([]);

		break;

	case "purchase_order_delete":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();

		if ($purchase->Delete($id_compra)) {

			$purchase->Read($id_compra);

			if ($response = PurchaseOrderGet($purchase)) {

				Send($response['response']);

			} else {

				Notifier::Add("Ocorreu um erro ao cancelar ordem de compra!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao cancelar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_product":

		$id_compra = $_POST['id_compra'];

		$tplPurchase = new View('templates/purchase_order');

		$purchaseOrder = new PurchaseOrder();

		$purchaseOrder->Read($id_compra);

		if ($response = PurchaseOrderProductList($purchaseOrder)) {

			Send($response);

		} else {

			Notifier::Add("Não foi possível encontrar a ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_read":

		$id_compra = $_POST['value'];

		$purchase = new PurchaseOrder;

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = PurchaseOrder::FormatFields($row);

			$response = $tplPurchase->getContent($row, "BLOCK_TR_HEADER");

			if ($row['id_comprastatus'] == PurchaseOrder::COMPRA_STATUS_ABERTA) {

				$response.= $tplPurchase->getContent($row, "BLOCK_TR_BUTTON_CANCELAR");
				$response.= $tplPurchase->getContent($row, "BLOCK_TR_BUTTON_LANCAR");
			}

			$response.= $tplPurchase->getContent($row, "BLOCK_TR_FOOTER");

			Send($response);

		} else {

			Notifier::Add("Não foi possível carregar ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_item_add":

		$value = $_POST['value'];
		$id_compra = $_POST['id_compra'];

		$product = new Product();

		if( is_numeric($value) ) {

			$product->Read($value);

		} else {

			$product->SearchByString($value, true);
		}

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] == ProductType::KIT) {

				Notifier::Add("Produto não pode ser kit.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			if ($row['id_produtotipo'] == ProductType::COMPOSICAO) {

				Notifier::Add("Produto não pode ser Composição.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$purchaseItem = new PurchaseOrderItem();

			$purchaseItem->getLastProductEntry($row['id_produto']);

			$qtdvol = 0;

			if ($row2 = $purchaseItem->getResult()) {

				$qtdvol = $row2['qtdvol'];
			}

			$data = [
				'id_compra' => $id_compra,
				'id_produto' => $row['id_produto'],
				'qtdvol' => $qtdvol,
				'vol' => 0,
				'custo' => 0,
				'obs' => ''
			];

			$id_compraitem = $purchaseItem->Create($data);

			$purchaseItem->Read($id_compraitem);

			if ($row = $purchaseItem->getResult()) {

				$row = PurchaseOrderGetCostHistory($row);

				$row = Product::FormatFields($row);

				$row = PurchaseOrderItem::FormatFields($row);

				$tplPurchase = new View('templates/purchase_order');

				$tplProduct = new View("templates/product");

				$row["block_product_menu"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_MENU");

				Send($tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER_ITEM"));

			} else {

				Notifier::Add("Ocorreu um erro ao adicionar produto na ordem de compra!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Não foi possível localizar o produto: $value", Notifier::NOTIFIER_INFO);
			Send(null);
		}

		break;

	case "purchase_order_item_del":

		$id_compra = $_POST['id_compra'];
		$id_compraitem = $_POST['value'];

		$purchaseItem = new PurchaseOrderItem();

		if ($purchaseItem->Delete($id_compraitem)) {

			$purchaseItem->getItems($id_compra);

			if ($purchaseItem->getResult()) {

				$data = [];

			} else {

				$tplPurchase = new View('templates/purchase_order');
				$data = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE");
			}

			Notifier::Add("Produto removido da ordem de compra!", Notifier::NOTIFIER_DONE);
			Send($data);

		} else {

			Notifier::Add("Erro ao remover item da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_chart_resume":

		$id_produto = $_POST['id_produto'];

		$purchaseItem = New PurchaseOrderItem();
		$purchaseItem->getLastProductEntry($id_produto);

		if ($row = $purchaseItem->getResult()) {

			$tipo = $row['produtounidade'];
			$date = date_format( date_create($row['data']), 'Y-m-d 00:00:00');
			$data = date_format( date_create($row['data']), 'd/m/Y');

			$total_qty += $row['vol'] * $row['qtdvol'];
			$total_custo += $row['vol'] * $row['custo'];
			$custo = $total_custo / $total_qty;

			$total_qty = number_format($total_qty, 3, ",", ".");
			$custo = number_format($custo, 2, ",", ".");

			Send("$data | $total_qty $tipo | R$ $custo /$tipo");

		} else {

			Send("Sem registro.");
		}

		break;

	case "purchase_order_date_edit":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = PurchaseOrder::FormatFields($row);

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_DATA"));

		} else {

			Notifier::Add("Erro ao abrir data de ordem de compra para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_date_save":

		$id_compra = $_POST['id_compra'];
		$data = $_POST['data'];

		$purchase = new PurchaseOrder();

		$purchase->Update([
			'id_compra' => $id_compra,
			'field' => "data",
			'value' => $data . ' 00:00:00',
		]);

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			$row = PurchaseOrder::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_DATA"));

		} else {

			Notifier::Add("Erro ao salvar data de ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_date_cancel":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = PurchaseOrder::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_DATA"));

		} else {

			Notifier::Add("Não foi possível carregar data da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_provider_edit":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();
		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');
			$tplProvider = new View('templates/provider');

			$row = PurchaseOrder::FormatFields($row);
			$row['block_provider_autocomplete_search'] = $tplProvider->getContent([], "BLOCK_PROVIDER_AUTOCOMPLETE_SEARCH");

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_PROVIDER"));

		} else {

			Notifier::Add("Erro ao abrir campo fornecedor da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_provider_save":

		$id_compra = $_POST['id_compra'];
		$razaosocial = $_POST['razaosocial'];

		$provider = new Provider();

		if( is_numeric($razaosocial) ) {

			$provider->Read($razaosocial);

		} else {

			$provider->ReadName($razaosocial);
		}

		if ($row = $provider->getResult()) {

			$purchase = new PurchaseOrder();

			$purchase->Update([
				'id_compra' => $id_compra,
				'field' => 'id_fornecedor',
				'value' => $row['id_fornecedor']
			]);

			$purchase->Read($id_compra);

			if ($row = $purchase->getResult()) {

				$tplPurchase = new View('templates/purchase_order');

				$row = PurchaseOrder::FormatFields($row);

				Send($tplPurchase->getContent($row, 'BLOCK_PROVIDER'));

			} else {

				Notifier::Add("Erro ao carregado fornecedor da ordem de compra!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Fornecedor não encontrado: $razaosocial", Notifier::NOTIFIER_INFO);
			Send(null);
		}

		break;

	case "purchase_order_provider_cancel":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = PurchaseOrder::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_PROVIDER"));

		} else {

			Notifier::Add("Error ao carregar fornecedor da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_note_save":

		$id_compra = $_POST['id_compra'];
		$obs = $_POST['obs'];

		$purchase = new PurchaseOrder();

		$purchase->Update([
			'id_compra' => $id_compra,
			'field' => 'obs',
			'value' => $obs,
		]);

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			// $tplPurchase = new View("templates/purchase_order");

			$row = PurchaseOrder::FormatFields($row);

			// $row = SaleOrder::FormatFields($row);
			// $tplSale = new View("templates/sale_order");

			// Send($tplSale->getContent($row, "BLOCK_OBS"));
			Send($row['extra_block_purchaseorder_obs']);
			// Send($tplPurchase->getContent($row, 'BLOCK_OBS'));

		} else {

			Notifier::Add("Erro ao salvar observação para ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_note_cancel":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();
		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = PurchaseOrder::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_OBS"));

		} else {

			Notifier::Add("Erro ao carregar observação da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_item_note_save":

		$id_compraitem = $_POST['id_compraitem'];
		$obs = $_POST['obs'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Update([
			'id_compraitem' => $id_compraitem,
			'field' => 'obs',
			'value' => $obs,
		]);

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			// $row = Product::FormatFields($row);
			$row = PurchaseOrderItem::FormatFields($row);

			Send($row["extra_block_purchaseorderitem_obs"]);

		} else {

			Notifier::Add("Erro ao salvar observação para item da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_item_note_cancel":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();
		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = Product::FormatFields($row);
			$row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_ITEM_OBS"));

		} else {

			Notifier::Add("Erro ao carregar observação do item da ordem de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_order_search":

		$status = $_POST['status'];
		$dataini = $_POST['dataini'];
		$intervalo = ($_POST['intervalo'] == "true")? true : false;
		$datafim = $_POST['datafim'];

		$purchase = new PurchaseOrder();

		if ($intervalo == true) {

			$purchase->SearchByDate($status, $dataini, $datafim);

		} else {

			$purchase->SearchByDate($status, $dataini, $dataini);
		}

		if ($response = PurchaseOrderGet($purchase)) {

			Send($response['response']);

		} else {

			Notifier::Add("Nenhuma ordem de compra localizada!", Notifier::NOTIFIER_INFO);
			Send([]);
		}

		break;

	case "purchase_order_close":

		$id_compra = $_POST['id_compra'];

		$purchaseItem = new PurchaseOrderItem();

		if ($purchaseItem->isValidItems($id_compra)) {

			$purchaseItem->getItems($id_compra);

			if ($row = $purchaseItem->getResult()) {

				$result = "";
				$product = new Product();

				do {

					$product->UpdateStock($row["id_produto"], round($row["vol"] * $row["qtdvol"],3));

				} while ($row = $purchaseItem->getResult());

				$purchaseOrder = new PurchaseOrder();

				$purchaseOrder->Update([
					"id_compra" => $id_compra,
					"field" => "id_comprastatus",
					"value" => PurchaseOrder::COMPRA_STATUS_FINALIZADA
				]);

				$purchaseOrder->Read($id_compra);

				if ($response = PurchaseOrderGet($purchaseOrder)) {

					Send($response['response']);

				} else {

					Notifier::Add("Não foi possível encontrar a ordem de compra!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Ordem de compra sem produtos!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Send(null);
		}

		break;

	case "purchase_order_whatsapp":

		$id_compra = $_POST['id_compra'];

		$purchase = new PurchaseOrder();

		$purchase->Read($id_compra);

		$row = $purchase->getResult();

		$row = PurchaseOrder::FormatFields($row);

		$whatsapp = "Pedido para " . $row['data_formatted'] . ":";

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->getItems($id_compra);

		while ($row2 = $purchaseItem->getResult()) {


			$row2['custo'] = number_format($row2['custo'],2,',','.');

			if (intval($row2['qtdvol']) == $row2['qtdvol']) {

				$row2['qtdvol'] = number_format($row2['qtdvol'],0);

			} else {

				$row2['qtdvol'] = number_format($row2['qtdvol'],3,',','.');
			}

			if ($row2['qtdvol'] == 1) {

				$qtdvol = "";

			} else {

				$qtdvol = " [" . $row2['qtdvol'] . " " . $row2['produtounidade'] . "]";
			}

			if (intval($row2['vol']) == $row2['vol']) {

				$row2['vol'] = number_format($row2['vol'],0);

				$whatsapp .= "%0A" . $row2['vol'] . " " . strtolower($row2['produto']) . $qtdvol;

			} else {

				$row2['vol'] = number_format($row2['vol'],3,',','.');

				$whatsapp .= "%0A" . $row2['vol'] . " " . strtolower($row2['produto']) . $qtdvol;
			}
		}

		Send($whatsapp);

		break;

	case "purchase_item_preco_edit":

		$id_compraitem = $_POST['id_compraitem'];
		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);
			$row['id_compraitem'] = $id_compraitem;
			// $row = PurchaseOrderItem::FormatFields($row);

			$tplPurchase = new View('templates/purchase_order');

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_PRECO"));

		} else {

			Notifier::Add("Erro ao carregar preço.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	// case "purchase_item_preco_get":

	// 	$id_compraitem = $_POST['id_compraitem'];
	// 	$id_produto = $_POST['id_produto'];

	// 	$product = new Product();

	// 	$product->Read($id_produto);

	// 	if ($row = $product->getResult()) {

	// 		$row = Product::FormatFields($row);
	// 		$row['id_compraitem'] = $id_compraitem;
	// 		// $row = PurchaseOrderItem::FormatFields($row);

	// 		$tplPurchase = new View('templates/purchase_order');

	// 		Send($tplPurchase->getContent($row, "BLOCK_PRODUCT_PRECO"));

	// 	} else {

	// 		Notifier::Add("Erro lendo preço do produto!", Notifier::NOTIFIER_ERROR);
	// 		Send(null);
	// 	}

	// 	break;

	// case "purchase_item_preco_save":

	// 	$id_compraitem = $_POST['id_compraitem'];
	// 	$id_produto = $_POST['id_produto'];
	// 	$preco = $_POST['preco'];

	// 	$product = new Product();

	// 	$product->Read($id_produto);

	// 	if ($row = $product->getResult()) {

	// 		$product->Update([
	// 			'id_produto' => $id_produto,
	// 			'field' => 'preco',
	// 			'value' => $preco,
	// 			'old_value' => $row['preco'],
	// 			'produto' => $row['produto'],
	// 		]);

	// 		$priceTag = new PriceTag();

	// 		if (!$priceTag->has($id_produto)) {

	// 			$priceTag->Create($id_produto);
	// 		}

	// 		$purchaseItem = new PurchaseOrderItem();

	// 		$purchaseItem->Read($id_compraitem);

	// 		if ($row = $purchaseItem->getResult()) {

	// 			$tplPurchase = new View('templates/purchase_order');
	// 			$tplProduct = new View('templates/product');

	// 			//Product is composition
	// 			if ($row['id_produto'] != $id_produto) {

	// 				$product->Read($id_produto);

	// 				if ($row = $product->getResult()) {

	// 					$row['custo_unidade'] = PurchaseOrderItem::getCompositionCost($id_compraitem, $id_produto);
	// 					$row['id_compraitem'] = $id_compraitem;
	// 					$row = PurchaseOrder::FormatCost($row);

	// 				} else {

	// 					Notifier::Add("Erro ao carregar preço do produto!", Notifier::NOTIFIER_ERROR);
	// 					Send(null);
	// 				}

	// 			} else {

	// 				$row = PurchaseOrderItem::FormatFields($row);
	// 			}

	// 			$row = Product::FormatFields($row);

	// 			Send($tplProduct->getContent($row, "BLOCK_GROUP_PRECO"));

	// 		} else {

	// 			Notifier::Add("Erro lendo preço do produto!", Notifier::NOTIFIER_ERROR);
	// 			Send(null);
	// 		}

	// 	} else {

	// 		Notifier::Add("Erro salvando preço do produto!", true);
	// 		Send(null);
	// 	}

	// 	break;

	// case "purchase_item_promo_get":

	// 	$id_compraitem = $_POST['id_compraitem'];
	// 	$id_produto = $_POST['id_produto'];



		// 			$product = new Product();

		// 			$product->Read($id_produto);



		// if ($row = $product->getResult()) {

		// 	$row = Product::FormatFields($row);
		// 	$row['id_compraitem'] = $id_compraitem;
		// 	// $row = PurchaseOrderItem::FormatFields($row);

		// 	$tplPurchase = new View('templates/purchase_order');

		// 	Send($tplPurchase->getContent($row, "BLOCK_PROMO"));

		// } else {

		// 	Notifier::Add("Erro lendo preço do produto!", Notifier::NOTIFIER_ERROR);
		// 	Send(null);
		// }

		// break;

	// case "purchase_item_promo_edit":

	// 	$id_compraitem = $_POST['id_compraitem'];
	// 	$id_produto = $_POST['id_produto'];



	// 				$product = new Product();

	// 				$product->Read($id_produto);


	// 	if ($row = $product->getResult()) {

	// 		$row = Product::FormatFields($row);
	// 		$row['id_compraitem'] = $id_compraitem;
	// 		// $row = PurchaseOrderItem::FormatFields($row);

	// 		$tplPurchase = new View('templates/purchase_order');

	// 		Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_PROMO"));

	// 	} else {

	// 		Notifier::Add("Erro ao carregar preço.", Notifier::NOTIFIER_ERROR);
	// 		Send(null);
	// 	}

	// 	break;

	// case "purchase_item_promo_save":

	// 	$id_compraitem = $_POST['id_compraitem'];
	// 	$id_produto = $_POST['id_produto'];
	// 	$preco_promo = $_POST['preco_promo'];

	// 	$product = new Product();

	// 	$product->Read($id_produto);

	// 	if ($row = $product->getResult()) {

	// 		$product->Update([
	// 			'id_produto' => $id_produto,
	// 			'field' => 'preco_promo',
	// 			'value' => $preco_promo,
	// 			'old_value' => ($row['preco_promo'] > 0)? $row['preco_promo'] : $row['preco'],
	// 			'produto' => $row['produto'],
	// 		]);

	// 		$priceTag = new PriceTag();

	// 		if (!$priceTag->has($id_produto)) {

	// 			$priceTag->Create($id_produto);
	// 		}

	// 		$purchaseItem = new PurchaseOrderItem();

	// 		$purchaseItem->Read($id_compraitem);

	// 		if ($row = $purchaseItem->getResult()) {

	// 			$tplPurchase = new View('templates/purchase_order');
	// 			$tplProduct = new View('templates/product');

	// 			//Product is composition
	// 			if ($row['id_produto'] != $id_produto) {

	// 				$product->Read($id_produto);

	// 				if ($row = $product->getResult()) {

	// 					$row['custo_unidade'] = PurchaseOrderItem::getCompositionCost($id_compraitem, $id_produto);
	// 					$row['id_compraitem'] = $id_compraitem;
	// 					$row = PurchaseOrder::FormatCost($row);

	// 				} else {

	// 					Notifier::Add("Erro ao carregar preço do produto!", Notifier::NOTIFIER_ERROR);
	// 					Send(null);
	// 				}

	// 			} else {

	// 				$row = PurchaseOrderItem::FormatFields($row);
	// 			}

	// 			$row = Product::FormatFields($row);

	// 			Send($tplProduct->getContent($row, "BLOCK_GROUP_PRECO"));

	// 		} else {

	// 			Notifier::Add("Erro lendo preço do produto!", Notifier::NOTIFIER_ERROR);
	// 			Send(null);
	// 		}

	// 	} else {

	// 		Notifier::Add("Erro salvando preço do produto!", Notifier::NOTIFIER_ERROR);
	// 		Send(null);
	// 	}

	// 	break;

	case "purchase_item_vol_edit":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			$row = Product::FormatFields($row);
			$row['vol_formatted'] = number_format($row['vol'],3,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_ITEM_VOL"));

		} else {

			Notifier::Add("Erro ao carregar dados do volume do item!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_vol_get":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = Product::FormatFields($row);
			$row['vol_formatted'] = number_format($row['vol'],3,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_ITEM_VOL"));

		} else {

			Notifier::Add("Erro lendo volume do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_vol_save":

		$id_compra = $_POST['id_compra'];
		$id_compraitem = $_POST['id_compraitem'];
		$vol = $_POST['vol'];

		$purchase = new PurchaseOrder();

		$purchaseItem = new PurchaseOrderItem();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			switch ($row['id_comprastatus']) {

				case PurchaseOrder::COMPRA_STATUS_FINALIZADA:

					$purchaseItem->Read($id_compraitem);

					if ($row = $purchaseItem->getResult()) {

						$product = new Product();

						$diff = ($vol * $row['qtdvol']) - ($row['vol'] * $row['qtdvol']);

						if ($diff <> 0) {

							$product->UpdateStock($row['id_produto'], $diff);

							$purchaseItem->Update([
								"id_compraitem" => $id_compraitem,
								"field" => "vol",
								'value' => $vol,
							]);
						}

						$purchaseItem->Read($id_compraitem);

						if ($row = $purchaseItem->getResult()) {

							Send(PurchaseOrderGetItem($row, PurchaseOrder::COMPRA_STATUS_FINALIZADA));

						} else {

							Notifier::Add("Erro ao ler ordem de compra!", Notifier::NOTIFIER_ERROR);
							Send(null);
						}

					} else {

						Notifier::Add("Erro lendo Ordem de compra!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				break;

				case PurchaseOrder::COMPRA_STATUS_ABERTA:

					$purchaseItem->Update([
						"id_compraitem" => $id_compraitem,
						"field" => "vol",
						'value' => $vol,
					]);

					$purchaseItem->Read($id_compraitem);

					if ($row = $purchaseItem->getResult()) {

						Send(PurchaseOrderGetItem($row, PurchaseOrder::COMPRA_STATUS_ABERTA));

					} else {

						Notifier::Add("Erro ao ler ordem de compra!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				break;

				default:

					Notifier::Add("Status da Ordem de Compra não permite alteração!", Notifier::NOTIFIER_ERROR);
					Send(null);
				break;
			}

		} else {

			Notifier::Add("Erro ao carregar dados da Ordem de Compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		break;

	case "purchase_item_custo_edit":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			$row = Product::FormatFields($row);
			$row['custo_formatted'] = number_format($row['custo'],2,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_ITEM_CUSTO"));

		} else {

			Notifier::Add("Erro ao carregar custo do item!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_custo_get":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = Product::FormatFields($row);
			$row['custo_formatted'] = number_format($row['custo'],2,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_ITEM_CUSTO"));

		} else {

			Notifier::Add("Erro lendo volume do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_custo_save":

		$id_compra = $_POST['id_compra'];
		$id_compraitem = $_POST['id_compraitem'];
		$custo = $_POST['custo'];

		$purchase = new PurchaseOrder();

		$purchaseItem = new PurchaseOrderItem();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			$id_vendastatus = $row['id_comprastatus'];


			$purchaseItem->Update([
				"id_compraitem" => $id_compraitem,
				"field" => "custo",
				'value' => $custo,
			]);

			$purchaseItem->Read($id_compraitem);

			if ($row = $purchaseItem->getResult()) {

				Send(PurchaseOrderGetItem($row, $id_vendastatus));

			} else {

				Notifier::Add("Erro ao ler ordem de compra!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados da Ordem de Compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		break;

	case "purchase_item_qtdvol_edit":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View("templates/purchase_order");

			$row = Product::FormatFields($row);
			$row['qtdvol_formatted'] = number_format($row['qtdvol'],3,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "EXTRA_BLOCK_FORM_ITEM_QTDVOL"));

		} else {

			Notifier::Add("Erro ao carregar quantidade por volume do item!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_qtdvol_get":

		$id_compraitem = $_POST['id_compraitem'];

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($row = $purchaseItem->getResult()) {

			$tplPurchase = new View('templates/purchase_order');

			$row = Product::FormatFields($row);
			$row['qtdvol_formatted'] = number_format($row['qtdvol'],3,',','.');
			// $row = PurchaseOrderItem::FormatFields($row);

			Send($tplPurchase->getContent($row, "BLOCK_ITEM_QTDVOL"));

		} else {

			Notifier::Add("Erro lendo quantidade por volume do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "purchase_item_qtdvol_save":

		$id_compra = $_POST['id_compra'];
		$id_compraitem = $_POST['id_compraitem'];
		$qtdvol = $_POST['qtdvol'];

		$purchase = new PurchaseOrder();

		$purchaseItem = new PurchaseOrderItem();

		$purchase->Read($id_compra);

		if ($row = $purchase->getResult()) {

			switch ($row['id_comprastatus']) {

				case PurchaseOrder::COMPRA_STATUS_FINALIZADA:

					$purchaseItem->Read($id_compraitem);

					if ($row = $purchaseItem->getResult()) {

						$product = new Product();

						$diff = ($row['vol'] * $qtdvol) - ($row['vol'] * $row['qtdvol']);

						if ($diff <> 0) {

							$product->UpdateStock($row['id_produto'], $diff);

							$purchaseItem->Update([
								"id_compraitem" => $id_compraitem,
								"field" => "qtdvol",
								'value' => $qtdvol,
							]);
						}

						$purchaseItem->Read($id_compraitem);

						if ($row = $purchaseItem->getResult()) {

							Send(PurchaseOrderGetItem($row, PurchaseOrder::COMPRA_STATUS_FINALIZADA));

						} else {

							Notifier::Add("Erro ao ler ordem de compra!", Notifier::NOTIFIER_ERROR);
							Send(null);
						}

					} else {

						Notifier::Add("Erro lendo Ordem de compra!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				break;

				case PurchaseOrder::COMPRA_STATUS_ABERTA:

					$purchaseItem->Update([
						"id_compraitem" => $id_compraitem,
						"field" => "qtdvol",
						'value' => $qtdvol,
					]);

					$purchaseItem->Read($id_compraitem);

					if ($row = $purchaseItem->getResult()) {

						Send(PurchaseOrderGetItem($row, PurchaseOrder::COMPRA_STATUS_ABERTA));

					} else {

						Notifier::Add("Erro ao ler ordem de compra!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				break;

				default:

					Notifier::Add("Status da Ordem de Compra não permite alteração!", Notifier::NOTIFIER_ERROR);
					Send(null);
				break;
			}

		} else {

			Notifier::Add("Erro ao ler dados da Ordem de Compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		break;

	case "purchaseorder_getlastentry":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			if ($row["id_produtotipo"] != Product::PRODUTO_TIPO_NORMAL) {

				switch ($row["id_produtotipo"]) {

					case Product::PRODUTO_TIPO_COMPOSICAO:

						$productComposition = new ProductComposition();

						$productComposition->getList($id_produto);

						if ($row = $productComposition->getResult()) {

							$id_produto = $row["id_produto"];
							// Notifier::Add("Data do filtro -> Compra do produto:<br>" . $row["produto"]);

						} else {

							Notifier::Add("Erro ao carregar dados da Composição.", Notifier::NOTIFIER_ERROR);
							Send(null);
						}

						break;

					case Product::PRODUTO_TIPO_KIT:

						$productKit = new ProductKit();

						$productKit->getList($id_produto);

						if ($row = $productKit->getResult()) {

							$id_produto = $row["id_produto"];
							// Notifier::Add("Data do filtro -> Compra do produto:<br>" . $row["produto"]);

						} else {

							Notifier::Add("Erro ao carregar dados do kit.", Notifier::NOTIFIER_ERROR);
							Send(null);
						}

						break;
				}
			}

		} else {

			Notifier::Add("Erro ao carregar dados do produto.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->getLastProductEntry($id_produto);

		if ($row = $purchaseItem->getResult()) {

			Send([
				"datestart" => date_format(date_create($row['data']),'Y-m-d'),
				"dateend" => date('Y-m-d'),
				"dateend_sel" => true
			]);

		} else {

			Send([
				"datestart" => date('Y-m-d'),
				"dateend" => date('Y-m-d'),
				"dateend_sel" => false
			]);
		}

		break;

	case "purchaseorder_popup_new":

		$tplPurchase = new View('templates/purchase_order');
		$tplProvider = new View("templates/provider");

		$purchaseList = new PurchaseList();
		$purchaseList->getList();

		$list = "";

		while ($row = $purchaseList->getResult()) {

			$list .= "<option value='" . $row['id_compralista'] . "'>" . $row['descricao'] . "</option>";
		}

		$data = [
			'date' => date('Y-m-d'),
			'compra_lista' => $list,
			// 'extra_block_purchase' => $content,
			// 'compra_status' => $listStatus,
			// 'purchaseorder_total_formatted' => number_format($total, 2, ',', '.'),
			'block_provider_autocomplete_search' => $tplProvider->getContent([], "BLOCK_PROVIDER_AUTOCOMPLETE_SEARCH")
		];

		Send($tplPurchase->getContent($data, "EXTRA_BLOCK_POPUP_PURCHASEORDER_NEW"));

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    	break;
}