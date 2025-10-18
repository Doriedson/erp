<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\Product;
use database\ProductSector;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO_SETOR);

function ProductsectorFormEdit($block, $message_error) {

	$id_produtosetor = $_POST['id_produtosetor'];

	$tplProductsector = new View('templates/product');

	$productsector = new ProductSector();
	$productsector->Read($id_produtosetor);

	if ($row = $productsector->getResult()) {

		$row = ProductSector::FormatFields($row);

		Send($tplProductsector->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function ProductsectorFormCancel($block, $message_error) {

	$id_produtosetor = $_POST['id_produtosetor'];

	$tplProductsector = new View('templates/product');

	$productsector = new ProductSector();
	$productsector->Read($id_produtosetor);

	if ($row = $productsector->getResult()) {

		$row = ProductSector::FormatFields($row);

		Send($tplProductsector->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductsectorFormSave($field, $block, $message_error) {

	$id_produtosetor = $_POST['id_produtosetor'];
	$value = $_POST['value'];

	$data = [
		'id_produtosetor' => (int) $id_produtosetor,
		'field' => $field,
		'value' => $value,
	];

	$productsector = new ProductSector();

	$productsector->Update($data);

	$tplProductsector = new View('templates/product');

	$productsector->Read($id_produtosetor);

	if ($row = $productsector->getResult()) {

		$row = ProductSector::FormatFields($row);

		Send($tplProductsector->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	// case "load":

	// 	$productsector = new ProductSector();
	// 	$productsector->getList();

	// 	$tplSector = new View('templates/product_sector');

	// 	$sector = "";

	// 	while ($row = $productsector->getResult()) {

	// 		$row = ProductSector::FormatFields($row);

	// 		$sector.= $tplSector->getContent($row, "EXTRA_BLOCK_PRODUCTSECTOR");
	// 	}

	// 	$data['extra_block_productsector'] = $sector;

	// 	Send($tplSector->getContent($data, "BLOCK_PAGE"));

	// 	break;

	case "productsector_add":

		$product_sector = new ProductSector();

		$produtosetor = $_POST['produtosetor'];

		$id_sector = $product_sector->Create($produtosetor);

		$product_sector->Read($id_sector);

		$tplSector = new View('templates/product');

		if ($row = $product_sector->getResult()) {

			$row = ProductSector::FormatFields($row);
			$row["bt_expand"] = "productsector_bt_expand";
			$row["bt_expand_icon"] = "fa-chevron-down";
			// $row['hidden'] = "";
			$row['extra_block_product'] = "";

			Send($tplSector->getContent($row, "EXTRA_BLOCK_PRODUCT_SECTOR"));

		} else {

			Notifier::Add("Não foi possível encontrar o setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "productsector_produtosetor_edit":

		ProductsectorFormEdit('EXTRA_BLOCK_SETOR_EDITION_FORM', 'Erro ao carregar setor!');
	break;

	case "productsector_produtosetor_cancel":

		ProductsectorFormCancel('BLOCK_SETOR_EDITION', 'Erro ao carregar setor!');
	break;

	case "productsector_produtosetor_save":

		ProductsectorFormSave('produtosetor', 'BLOCK_SETOR_EDITION', 'Erro ao carregar setor!');
	break;

	case "productsector_delete":

		$id_produtosetor = $_POST['id_produtosetor'];

		$product = new Product();

		if ($product->hasProductSector($id_produtosetor)) {

			Notifier::Add("Setor em uso não pode ser removido!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$sector = new ProductSector();

			if ($sector->Delete($id_produtosetor)) {

				Notifier::Add("Setor excluído com sucesso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				Notifier::Add("Erro ao excluir setor!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}
	break;

	case "produtosetor_waiter_status":

		$id_produtosetor = $_POST['id_produtosetor'];

		$productSector = new ProductSector();

		$productSector->ToggleGarcom($id_produtosetor);

		$productSector->Read($id_produtosetor);

		if ($row = $productSector->getResult()) {

			// $tplSector = new View('templates/product_sector');

			$row = ProductSector::FormatFields($row);

			Send($row['garcom']);

		} else {

			Notifier::Add("Erro ao carregar dados do setor!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}