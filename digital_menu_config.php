<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\Clean;
use database\Log;
use database\Product;
use database\ProductExpDate;
use database\ProductType;
use database\ProductKit;
use database\ProductComposition;
use database\ProductSector;
use database\PriceTag;
use database\BarCode;
use database\Company;

require "inc/config.inc.php";
require "inc/authorization.php";

function LoadProductSector() {

	$tplProduct = new View('templates/digital_menu_config');

	$productsector = new ProductSector();
	$productsector->getList();

	$sector_list = "";

	while ($row = $productsector->getResult()) {

		$row = ProductSector::FormatFields($row);

		$row['extra_block_product'] = "";
		$row['bt_expand'] = "productsector_bt_expand";
		$row['bt_expand_icon'] = "fa-chevron-down";
		// $row['hidden'] = "hidden";

		$sector_list .= $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_SECTOR");
	}

	return $sector_list;
}

switch ($_POST['action']) {

	case "load":

		$tplProduct = new View('templates/digital_menu_config');

		$company = new Company();

		$company->Read();

		if ($row = $company->getResult()) {

			$empresa = $row['empresa'];
		}

		$sector_list = LoadProductSector();

		$date = new DateTimeImmutable();

		$data = [
			"empresa" => $empresa,
			"extra_block_product_sector" => $sector_list,
			"hidden" => "hidden",
			"timestamp" => $date->getTimestamp()
		];

		if ($sector_list == "") {

			$data['hidden'] = "";
		}

		Send($tplProduct->getContent($data, "BLOCK_PAGE"));

		break;

	case "productsector_expand":

		$id_produtosetor = $_POST['id_produtosetor'];

		$product = new Product();

		$product->getAllProductsFromSector($id_produtosetor);

		$extra_block_product = "";

		$tplDigitalMenu = new View('templates/digital_menu_config');
		$tplProduct = new View('templates/product');

		if ($row = $product->getResult()) {

			do {
				$row = Product::FormatFields($row);

				$row["block_group_preco"] = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");

				$extra_block_product .= $tplDigitalMenu->getContent($row, "EXTRA_BLOCK_PRODUCT");

			} while ($row = $product->getResult());

			Send($extra_block_product);

		} else {

			Send(null);
		}

		break;

	case "digitalmenu_sector":

		$id_produtosetor = $_POST['id_produtosetor'];

		$productSector = new ProductSector();

		$productSector->ToggleMenuDigital($id_produtosetor);

		$productSector->Read($id_produtosetor);

		if ($row = $productSector->getResult()) {

			$row = ProductSector::FormatFields($row);

			Send($row['cardapio_setor']);

		} else {

			Notifier::Add("Ocorrreu um erro da ativação/desativação do setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "digitalmenu_product":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->ToggleMenuDigital($id_produto);

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			Send($row['cardapio_produto']);

		} else {

			Notifier::Add("Ocorrreu um erro da ativação/desativação do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

	break;
}