<?php

use database\ControlAccess;
use App\View\View;
use database\PriceTag;
use database\Product;
use database\Notifier;

require "./inc/config.inc.php";
require "./inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR);

function PricetagM1($pricetag_option) {

	$priceTag = new PriceTag();

	$priceTag->getList($pricetag_option);

	if ($row = $priceTag->getResult()) {

		$tplPricetag = new View("templates/price_tag_mod1");

		$result = "";

		do {

			$content['extra_block_tag'] = "";

			if ($row['preco_promo'] > 0) {

				$row['preco'] = number_format($row['preco_promo'],2,",",".");

			} else {

				$row['preco'] = number_format($row['preco'],2,",",".");
			}

			$content['extra_block_tag'] .= $tplPricetag->getContent($row, "EXTRA_BLOCK_TAG");

			if ($row = $priceTag->getResult()) {

				if ($row['preco_promo'] > 0) {

					$row['preco'] = number_format($row['preco_promo'],2,",",".");

				} else {

					$row['preco'] = number_format($row['preco'],2,",",".");
				}

				$content['extra_block_tag'] .= $tplPricetag->getContent($row, "EXTRA_BLOCK_TAG");
			}

			$result.= $tplPricetag->getContent($content, "BLOCK_PAGE");

		} while ($row = $priceTag->getResult());

		Send($result);

	} else {

		Notifier::Add("Não há etiquetas na regra selecionada para impressão!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PricetagM2($pricetag_option) {

	$priceTag = new PriceTag();

	$priceTag->getList($pricetag_option);

	if ($row = $priceTag->getResult()) {

		$tplPricetag = new View("templates/price_tag_mod2");

		$content['extra_block_tag'] = "";

		do {

			if ($row['preco_promo'] > 0) {

				$row['preco'] = number_format($row['preco_promo'],2,",",".");

			} else {

				$row['preco'] = number_format($row['preco'],2,",",".");
			}

			$content['extra_block_tag'] .= $tplPricetag->getContent($row, "EXTRA_BLOCK_TAG");

		} while ($row = $priceTag->getResult());

		Send($tplPricetag->getContent($content, "BLOCK_PAGE"));

	} else {

		Notifier::Add("Não há etiquetas na regra selecionada para impressão!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$priceTag = new PriceTag();

		$priceTag->getList("ALL");

		$tplPricetag = new View('templates/price_tag');

		$tplProduct = new View("templates/product");

		$pricetag = "";

		if ($row = $priceTag->getResult()) {

			$data['pricetag_bt_clear_visibility'] = '';

			do {

				$row = Product::FormatFields($row);

				$row["product_block_group_preco"] = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");

				$pricetag.= $tplPricetag->getContent($row, "EXTRA_BLOCK_PRICETAG");

			} while ($row = $priceTag->getResult());

		} else {

			$data['pricetag_bt_clear_visibility'] = 'hidden';

			$pricetag.= $tplPricetag->getContent([], "EXTRA_BLOCK_PRICETAG_NONE");
		}

		$data['extra_block_pricetag'] = $pricetag;
		$data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		Send($tplPricetag->getContent($data, "BLOCK_PAGE"));

		break;

	case "pricetag_clear":

		$priceTag = new PriceTag;
		$result = $priceTag->DeleteAll();

		if ($result == 0) {

			Notifier::Add("Erro ao remover etiquetas!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$tplPricetag = new View('templates/price_tag');

			Send($tplPricetag->getContent($row, "EXTRA_BLOCK_PRICETAG_NONE"));
		}

		break;

	case "pricetag_del":

		$priceTag = new PriceTag();

		if ($priceTag->Delete($_POST['id_etiqueta'])) {

			$priceTag->getList('ALL');

			if($priceTag->getResult()) {

				Send([]);

			} else {

				$tplPricetag = new View('templates/price_tag');

				Send($tplPricetag->getContent([], 'EXTRA_BLOCK_PRICETAG_NONE'));
			}

		} else {

			Notifier::Add("Erro ao remover etiqueta!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "pricetag_add":

		$id_produto = $_POST['id_produto'];

		$product = new Product;

		if( is_numeric($id_produto) ) {

			$product->SearchByCode($id_produto);

		} else {

			$product->SearchByString($id_produto);
		}

		if ($row = $product->getResult()) {

			$priceTag = new PriceTag();

			$id_etiqueta = $priceTag->Create($row['id_produto']);

			$priceTag->Read($id_etiqueta);

			if ($row = $priceTag->getResult()) {

				$tplPricetag = new View('templates/price_tag');
				$tplProduct = new View('templates/product');

				$row = Product::FormatFields($row);

				$row["product_block_group_preco"] = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");

				Send($tplPricetag->getContent($row, "EXTRA_BLOCK_PRICETAG"));

			} else {

				Notifier::Add("Erro ao adicionar etiqueta!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "pricetag_print":

		$pricetag_option = $_POST['pricetag_option'];
		$pricetag_model = $_POST['pricetag_model'];

		switch($pricetag_model) {

			case "TAG":

				PricetagM1($pricetag_option);
			break;

			case "SALEOFF":

				PricetagM2($pricetag_option);
			break;
		}

		break;

	case "pricetag_popup_print":

		$tplPricetag = new View('templates/price_tag');

		Send($tplPricetag->getContent([], "EXTRA_BLOCK_POPUP_PRICETAG_PRINT"));

		break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}