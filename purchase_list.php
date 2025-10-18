<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\Product;
use database\PurchaseList;
use database\PurchaseListItem;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_ORDEM_COMPRA_LISTA);

switch ($_POST['action']) {

	case "load":

		$tplPurchaseList = new View('templates/purchase_list');

		$purchase = new PurchaseList();
		$purchase->getList();

		$list = "";

		if ($row = $purchase->getResult()) {

			$list = $tplPurchaseList->getContent(["hidden" => "hidden"], "EXTRA_BLOCK_PURCHASELIST_NONE");

			do {

				$list.= $tplPurchaseList->getContent($row, "EXTRA_BLOCK_PURCHASELIST");

			} while ($row = $purchase->getResult());

		} else {

			$list = $tplPurchaseList->getContent(["hidden" => ""], "EXTRA_BLOCK_PURCHASELIST_NONE");
		}

		$data = [
			"extra_block_purchaselist" => $list
		];

		Send($tplPurchaseList->getContent($data, "BLOCK_PAGE"));
	break;

	case "purchaselist_new":

		$descricao = $_POST['descricao'];

		$purchaseList = new PurchaseList();

		$id_compralista = $purchaseList->Create($descricao);

		$purchaseList->Read($id_compralista);

		if ($row = $purchaseList->getResult()) {

			$tplPurchaseList = new View('templates/purchase_list');

			$row = Product::FormatFields($row);

			Notifier::Add("Lista adiciona com sucesso!", Notifier::NOTIFIER_DONE);
			Send($tplPurchaseList->getContent($row, "EXTRA_BLOCK_PURCHASELIST"));

		} else {

			Notifier::Add("Erro ao cadastrar nova lista de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_delete":

		$id_compralista = $_POST['id_compralista'];

		$purchaseList = new PurchaseList();

		if ($purchaseList->Delete($id_compralista)) {

			Notifier::Add("Lista de compra removida com sucesso!", Notifier::NOTIFIER_DONE);
			Send([]);

		} else {

			Notifier::Add("Erro ao remover lista de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_descricao_edit":

		$id_compralista = $_POST['id_compralista'];

		$purchaseList = new PurchaseList();

		$purchaseList->Read($id_compralista);

		if ($row = $purchaseList->getResult()) {

			$tplPurchaseList = new View('templates/purchase_list');

			Send($tplPurchaseList->getContent($row, "EXTRA_BLOCK_DESCRICAO_FORM"));

		} else {

			Notifier::Add("Erro ao abrir descrição para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_descricao_save":

		$id_compralista = $_POST['id_compralista'];
		$descricao = $_POST['descricao'];

		$purchaseList = new PurchaseList();

		$purchaseList->Update([
			"id_compralista" => $id_compralista,
			"field" => 'descricao',
			"value" => $descricao,
		]);

		$purchaseList->Read($id_compralista);

		if ($row = $purchaseList->getResult()) {

			$tplPurchaseList = new View("templates/purchase_list");

			Send($tplPurchaseList->getContent($row, "BLOCK_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao ler descrição da lista de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_descricao_cancel":

		$id_compralista = $_POST['id_compralista'];

		$purchase = new PurchaseList();

		$purchase->Read($id_compralista);

		if ($row = $purchase->getResult()) {

			$tplPurchaseList = new View("templates/purchase_list");

			Send($tplPurchaseList->getContent($row, "BLOCK_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao carregar descrição da lista de compra!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_open":

		$id_compralista = $_POST['id_compralista'];

		$purchaseItem = new PurchaseListItem();

		$purchaseItem->getItems($id_compralista);

		$extra_block_purchaselist_item = "";

		$tplPurchaseList = new View("templates/purchase_list");

		$tplProduct = new View('templates/product');

		if ($row = $purchaseItem->getResult()) {

			do {

				$row = Product::FormatFields($row);

				$extra_block_purchaselist_item.= $tplPurchaseList->getContent($row, "EXTRA_BLOCK_PURCHASELIST_ITEM");

			} while ($row = $purchaseItem->getResult());

		} else {

			$extra_block_purchaselist_item = $tplPurchaseList->getContent([], "EXTRA_BLOCK_PURCHASELIST_ITEM_NONE");
		}

		$data['extra_block_purchaselist_item'] = $extra_block_purchaselist_item;

		$data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		Send($tplPurchaseList->getContent($data, "EXTRA_BLOCK_PURCHASELIST_TABLE"));
	break;

	case "purchaselist_item_add":

		$produto = $_POST['produto'];
		$id_compralista = $_POST['id_compralista'];

		$product = new Product();

		if( is_numeric($produto) ) {

			$product->SearchByCode($produto);

		} else {

			$product->SearchByString($produto);
		}

		if ($row = $product->getResult()) {

			$purchaseItem = new PurchaseListItem();

			$id_compralistaitem = $purchaseItem->Create($id_compralista, $row['id_produto']);

			$purchaseItem->Read($id_compralistaitem);

			if ($row = $purchaseItem->getResult()) {

				$tplPurchaseList = new View('templates/purchase_list');

				$row = Product::FormatFields($row);

				Send($tplPurchaseList->getContent($row, "EXTRA_BLOCK_PURCHASELIST_ITEM"));

			} else {

				Notifier::Add("Erro ao localizar produto da lista de compra!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "purchaselist_item_delete":

		$id_compralista = $_POST['id_compralista'];
		$id_compralistaitem = $_POST['id_compralistaitem'];

		$purchaseItem = new PurchaseListItem();

		if ($purchaseItem->Delete($id_compralistaitem)) {

			$purchaseItem->getItems($id_compralista);

			if ($purchaseItem->getResult()) {

				Notifier::Add("Item removido com sucesso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				$tplPurchaseList = new View('templates/purchase_list');

				Notifier::Add("Item removido com sucesso!", Notifier::NOTIFIER_DONE);
				Send($tplPurchaseList->getContent([], "EXTRA_BLOCK_PURCHASELIST_ITEM_NONE"));
			}

		} else {

			Notifier::Add("Erro ao excluir item da lista!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}