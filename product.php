<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Clean;
use App\Legacy\Calc;
use App\Legacy\Config;
use App\Legacy\Log;
use App\Legacy\Product;
use App\Legacy\ProductExpDate;
use App\Legacy\ProductType;
use App\Legacy\ProductKit;
use App\Legacy\ProductComposition;
use App\Legacy\ProductSector;
use App\Legacy\PriceTag;
use App\Legacy\BarCode;
use App\Legacy\PurchaseOrderItem;
use App\Legacy\PurchaseOrder;
use App\Legacy\ProductComplement;

require "inc/config.inc.php";
require "inc/authorization.php";

function ProductPriceSave($id_produto, $field, $value, $page, $kit_skip = false) {

	// $id_produto = $_POST['id_produto'];
	// $value = $_POST['value'];
	// $page = $_POST["page"];

	$product = new Product();

	$product->Read($id_produto);

	if ($row = $product->getResult()) {

		if (!$kit_skip && $row['id_produtotipo'] == Product::PRODUTO_TIPO_KIT) {

			Notifier::Add("Preço do kit deve ser alterado nos produtos do kit!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$data = [
				'id_produto' => (int) $id_produto,
				'field' => $field,
				'value' => $value,
			];

			$product->Update($data);

			if ($field == "preco") {

				Notifier::Add($row['produto'] . "<br>Preço alterado.", Notifier::NOTIFIER_INFO);

			} else {

				Notifier::Add($row['produto'] . "<br>Preço promocional alterado.", Notifier::NOTIFIER_INFO);
			}

			$priceTag = new PriceTag();

			if (!$priceTag->has($id_produto)) {

				Notifier::Add($row['produto'] . "<br>Etiqueta de preço gerada.", Notifier::NOTIFIER_INFO);
				$priceTag->Create($id_produto);
			}

			$tplProduct = new View('product');

			$product->Read($id_produto);

			if ($row = $product->getResult()) {

				$row = Product::FormatFields($row);

				if ($page == "purchase_order.php") {

					$id_compraitem = $_POST["id_compraitem"];

					$row['id_compraitem'] = $id_compraitem;

					$purchaseItem = new PurchaseOrderItem();

					$purchaseItem->Read($id_compraitem);

					if ($rowPurchase = $purchaseItem->getResult()) {

						//Product is composition
						if ($rowPurchase['id_produto'] != $id_produto) {

							$result = PurchaseOrderItem::getCompositionCost($id_compraitem, $id_produto);

							$row['custo_unidade'] = $result[0];
							$row['custo_unidade_ajustado'] = $result[1];

							if ($row['custo_unidade'] == $row['custo_unidade_ajustado']) {

								$row['custo_ajustado_visible'] = "hidden";
							}

							$row = PurchaseOrder::FormatCost($row);

						} else {

							$rowPurchase = PurchaseOrderItem::FormatFields($rowPurchase);

							$row["preco_percent"] = $rowPurchase["preco_percent"];
							$row["preco_promo_percent"] = $rowPurchase["preco_promo_percent"];
						}
					}
				}

				$data = [
					"data" => $tplProduct->getContent($row, "BLOCK_GROUP_PRECO")
				];

				if ($kit_skip == false) {

					Send($data);
				}

			} else {

				Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

	} else {

		Notifier::Add("Ocorreu um erro na alteração de preço do produto!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function UpdatePrecoKit($id_kit, $preco, $produto) {

	$productKit = new ProductKit();

	$productKit->getTotal($id_kit);

	if ($row = $productKit->getResult()) {

		if ($row['total'] > 0) {

			ProductPriceSave($id_kit, "preco", $row["total"], null, true);

			$product = new Product();

			$product->Read($id_kit);

			if ($row_product = $product->getResult()) {

				if ($row_product["preco_promo"] > 0) {

					ProductPriceSave($id_kit, "preco_promo", 0, null, true);
				}
			}
		}
	}
}

function ProductFormEdit($block, $message_error) {

	$id_produto = $_POST['id_produto'];

	$tplProduct = new View('product');

	$product = new Product();
	$product->Read($id_produto);

	if ($row = $product->getResult()) {

		if ($row['id_produtotipo'] == ProductType::KIT && $block == "EXTRA_BLOCK_IMPRESSORA_FORM") {

			Notifier::Add("Função inativa para kit!<br> Altere direto nos produtos do kit.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$row = Product::FormatFields($row);

		// $row['block_product_produto'] = $tplProduct->getContent($row, "BLOCK_PRODUCT_PRODUTO");

		Send($tplProduct->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductFormCancel($block, $message_error) {

	$id_produto = $_POST['id_produto'];

	$tplProduct = new View('product');

	$product = new Product();
	$product->Read($id_produto);

	if ($row = $product->getResult()) {

		$row = Product::FormatFields($row);

		Send($tplProduct->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductFormSave($field, $block, $message_error, $page = null) {

	$id_produto = $_POST['id_produto'];
	$value = $_POST['value'];

	if($field == "id_impressora" && $value == "") {

		$value = null;
	}

	$data = [
		'id_produto' => (int) $id_produto,
		'field' => $field,
		'value' => $value,
	];

	$product = new Product();

	$product->Update($data);

	if ($field == 'preco' || $field == 'preco_promo') {

		$priceTag = new PriceTag();

		if (!$priceTag->has($id_produto)) {

			$priceTag->Create($id_produto);
		}
	}

	$tplProduct = new View('product');

	$product->Read($id_produto);

	if ($row = $product->getResult()) {

		$row = Product::FormatFields($row);

		$data = [
			"data" => $tplProduct->getContent($row, $block)
		];

		if($field = "id_produtounidade") {

			$data['product'] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT");
		}

		Send($data);

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductKitFormEdit($block, $message_error) {

	$id_produto = $_POST['id_produto'];
	$id_kit = $_POST['id_kit'];

	$tplProduct = new View('product');

	$productKit = new ProductKit();

	$productKit->Read($id_kit, $id_produto);

	if ($row = $productKit->getResult()) {

		$row = Product::FormatFields($row);

		$row['qtd_formatted'] = number_format($row['qtd'],3,",",".");

		Send($tplProduct->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductKitFormCancel($block, $message_error) {

	$id_produto = $_POST['id_produto'];
	$id_kit = $_POST['id_kit'];

	$tplProduct = new View('product');

	$productKit = new ProductKit();

	$productKit->Read($id_kit, $id_produto);

	if ($row = $productKit->getResult()) {

		$row = Product::FormatFields($row);

		$row['qtd_formatted'] = number_format($row['qtd'],3,",",".");

		Send($tplProduct->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProductKitFormSave($field, $block, $message_error) {

	$id_produto = $_POST['id_produto'];
	$id_kit = $_POST['id_kit'];
	$value = $_POST['value'];

	if ($field == "qtd" && (!is_numeric($value) || $value == 0)) {

		Notifier::Add("Digite um valor maior que zero para quantidade.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	if ($field == "preco" && !is_numeric($value)) {

		$value = 0;
	}

	$productKit = new ProductKit();

	$productKit->Update([
		'id_kit' => $id_kit,
		'id_produto' => $id_produto,
		'field' => $field,
		'value' => $value,
	]);

	$product = new Product();

	$product->Read($id_kit);

	if ($row = $product->getResult()) {

		UpdatePrecoKit($id_kit, $row['preco'], $row['produto']);

		$tplProduct = new View('product');

		$product->Read($id_kit);

		$row = $product->getResult();

		$row = Product::FormatFields($row);

		$produtct_tr = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT");
		// $preco = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");

		$productKit->Read($id_kit, $id_produto);

		if ($row = $productKit->getResult()) {

			$tplProduct = new View("product");

			$row = ProductKit::FormatFields($row);

			$row = Product::FormatFields($row);

			Send(
				array(
					"data" => $tplProduct->getContent($row, $block),
					"product" => $produtct_tr
				)
			);

		} else {

			Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
			Send(null);

		}
	} else {

		Notifier::Add("Erro ao carregar dados do produto.", Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function LoadProductSector() {

	$tplProduct = new View('product');

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

		$tplProduct = new View('product');

		$sector_list = LoadProductSector();

		$data = [
			"extra_block_product_sector" => $sector_list,
			"hidden" => "hidden"
		];

		if ($sector_list == "") {

			$data['hidden'] = "";
		}

		Send($tplProduct->getContent($data, "BLOCK_PAGE"));

		break;

	case "product_search_autocomplete":

		$value = Clean::HtmlChar($_POST['value']);

		$source = $_POST['source'];
		$sort = $_POST['sort'];
		$active_only = false;

		$tplProduct = new View("product");

		$product = new Product();

		if( is_numeric($value) ) {

			$product->Read($value);

		} else {

			if (empty($value)) {

				switch ($source) {

					case "popup":

						Send("");
						break;

					case "product":

						Send(LoadProductSector());
						break;

					case "waiter":

						$productsector = new ProductSector();

						Send($productsector->getListWaiter());
						break;

					default:

						Notifier::Add("Origem não definida.", Notifier::NOTIFIER_ERROR);
						Send(null);
				}
			}

			if ($source == "waiter") {

				$active_only = true;
			}

			$product->SearchByString($value, false, $sort, $active_only);
		}

		$product_list = "";

		if ($row = $product->getResult()) {

			switch ($source) {

				case "popup":

					do {

						$row = Product::FormatFields($row);

						$product_list.= $tplProduct->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH");

					} while ($row = $product->getResult());

					break;

				case "product":

					$product_sector = new ProductSector();

					$sector = $row['id_produtosetor'];

					$sector_list = "";

					do {

						$row = Product::FormatFields($row);

						$product_list.= $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT");

						$row = $product->getResult();

						if (!$row || $sector != $row['id_produtosetor']) {

							$product_sector->Read($sector);

							if($rowSector = $product_sector->getResult()) {

								$rowSector = ProductSector::FormatFields($rowSector);
								$rowSector["bt_expand"] = "bt_collapse";
								$rowSector["bt_expand_icon"] = "fa-chevron-up";
								$rowSector['hidden'] = "";
								$rowSector['extra_block_product'] = $product_list;

								$sector_list .= $tplProduct->getContent($rowSector, "EXTRA_BLOCK_PRODUCT_SECTOR");

								if ($row) {

									$sector = $row['id_produtosetor'];
								}

								$product_list = "";
							}
						}

					} while ($row);

					$product_list = $sector_list;
					break;

				case "waiter":

					$product_sector = new ProductSector();

					$id_produtosetor = $row['id_produtosetor'];
					$produtosetor = $row['produtosetor'];
					$garcom = $row['garcom'];

					$sector_list = "";

					$tplSector = new View('waiter_sector');

					do {

						if ($garcom == 1) {

							$row = Product::FormatFields($row);

							if ($row['id_produtounidade'] == 1) { //UN

								$row['extra_block_product_un'] = $tplSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN");

							} else if ($row['id_produtounidade'] == 2) { //KG

								$row['extra_block_product_un'] = $tplSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG");
							}

							$product_list.= $tplSector->getContent($row, "EXTRA_BLOCK_WAITERSECTOR_PRODUCT");
						}

						$row = $product->getResult();

						if (!$row || $id_produtosetor != $row['id_produtosetor']) {

							if ($garcom == 1) {
								$data['sector_bt_expand'] = $tplSector->getContent([], "EXTRA_BLOCK_SECTOR_BT_COLLAPSE");
								$data['extra_block_product'] = $product_list;
								$data['produtosetor'] = $produtosetor;
								$data['id_produtosetor'] = $id_produtosetor;
								// $data['expandable'] = '';

								$sector_list.= $tplSector->getContent($data, "EXTRA_BLOCK_SECTOR");
							}

							if ($row) {

								$id_produtosetor = $row['id_produtosetor'];
								$produtosetor = $row['produtosetor'];
								$garcom = $row['garcom'];
							}

							$product_list = "";

						}

					} while ($row);

					$product_list = $sector_list;
					break;
			}

		} else {

			switch ($source) {

				case "popup":

					$product_list = $tplProduct->getContent([], "EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND");
					break;

				case "product":

					$product_list = null; // $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_SECTOR_NOTFOUND");
					break;

				case "waiter":

					$product_list = null;
					break;
			}
		}

		Send($product_list);

	break;

	case "produto_change_status":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];

		$product = new Product;
		$product->ToggleActive($id_produto);

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			$tplProduct = new View("product");

			Send($row['extra_block_product_button_status']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_produto_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormEdit("EXTRA_BLOCK_FORM_PRODUCT", "Erro ao carregar nome do produto!");
	break;

	case "product_produto_cancel":

		ProductFormCancel("BLOCK_PRODUCT_PRODUTO", "Erro ao carregar produto!");
	break;

	case "product_produto_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormSave('produto', "BLOCK_PRODUCT_PRODUTO", "Erro ao salvar produto!");
	break;

	case "product_setor_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormEdit("EXTRA_BLOCK_SETOR_FORM", "Erro ao carregar setor do produto!");
	break;

	case "product_setor_cancel":

		ProductFormCancel("BLOCK_SETOR", "Erro pegando setor do produto!");
	break;

	case "product_setor_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormSave('id_produtosetor', "BLOCK_SETOR", "Erro ao salvar setor do produto!");
	break;

	case "product_impressora_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormEdit("EXTRA_BLOCK_IMPRESSORA_FORM", "Erro ao carregar impressoras!");
	break;

	case "product_impressora_cancel":

		ProductFormCancel("BLOCK_IMPRESSORA", "Erro carregando impressora!");
	break;

	case "product_impressora_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormSave('id_impressora', "BLOCK_IMPRESSORA", "Erro ao definir impressora!");
	break;

	case "product_unidade_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormEdit("EXTRA_BLOCK_UNIDADE_FORM", "Erro ao carregar unidade do produto!");
	break;

	case "product_unidade_cancel":

		ProductFormCancel("BLOCK_UNIDADE", "Erro pegando unidade do produto!");
	break;

	case "product_unidade_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormSave('id_produtounidade', "BLOCK_UNIDADE", "Erro ao salvar unidade do produto!");
	break;

	case "product_preco_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST["id_produto"];
		$page = $_POST["page"];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] == Product::PRODUTO_TIPO_KIT) {

				Notifier::Add("Preço do kit deve ser alterado nos produtos do kit!", Notifier::NOTIFIER_INFO);
				Send(null);

			}

			// ProductFormEdit("EXTRA_BLOCK_PRODUCT_PRECO_FORM", "Produto não encontrado para edição de preço!");

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			if ($page == "purchase_order.php") {

				$row["id_compraitem"] = $_POST["id_compraitem"];
			}

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_PRECO_FORM"));

		} else {

			Notifier::Add("Ocorreu um erro na alteração de preço do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_preco_cancel":

		// ProductFormCancel("BLOCK_PRODUCT_PRECO", "Erro pegando preço do produto!");
		$id_produto = $_POST['id_produto'];
		$page = $_POST["page"];

		$tplProduct = new View('product');

		$product = new Product();
		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			if ($page == "purchase_order.php") {

				$row["id_compraitem"] = $_POST["id_compraitem"];
			}

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_PRECO"));

		} else {

			Notifier::Add("Erro pegando preço do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_preco_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$value = $_POST['value'];
		$page = $_POST["page"];

		ProductPriceSave($id_produto, "preco", $value, $page);

		break;

	case "product_preco_promo_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST["id_produto"];
		$page = $_POST["page"];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] == Product::PRODUTO_TIPO_KIT) {

				Notifier::Add("Preço do kit deve ser alterado nos produtos do kit!", Notifier::NOTIFIER_INFO);
				Send(null);

			}

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			if ($page == "purchase_order.php") {

				$row["id_compraitem"] = $_POST["id_compraitem"];
			}

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_PRECO_PROMO_FORM"));

		} else {

			Notifier::Add("Ocorreu um erro na alteração de preço do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_preco_promo_cancel":

		// ProductFormCancel("BLOCK_PRODUCT_PRECO_PROMO", "Erro pegando promoção do produto!");
		$id_produto = $_POST['id_produto'];
		$page = $_POST["page"];

		$tplProduct = new View('product');

		$product = new Product();
		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			if ($page == "purchase_order.php") {

				$row["id_compraitem"] = $_POST["id_compraitem"];
			}

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_PRECO_PROMO"));

		} else {

			Notifier::Add("Erro pegando preço do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_preco_promo_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$value = $_POST['value'];
		$page = $_POST["page"];

		ProductPriceSave($id_produto, "preco_promo", $value, $page);

		break;

	case "product_obs_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormEdit("EXTRA_BLOCK_OBS_FORM", "Produto não encontrado para edição de observação!");
	break;

	case "product_obs_cancel":

		ProductFormCancel("BLOCK_OBS", "Erro pegando observação do produto!");
	break;

	case "product_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductFormSave('obs', "BLOCK_OBS", "Erro ao salvar observação do produto!");
	break;

	case "image_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$product = new Product;
		$product->SearchByCode($_POST['id_produto']);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			// $files = array_slice(scandir('pic/'), 2);

			// sort($files);

			$tplProduct = new View('product');

			$option = "<option value='' data-img='" . Product::getImageFromName($row['produto']) . "'>Imagem padrão (primeira letra)</option>";

			// for ($indexFiles = 0; $indexFiles < count($files); $indexFiles++) {

			// 	$selected = ($row['imagem'] == $files[$indexFiles]) ? "selected" : "";

			// 	$option.= "<option value='" . $files[$indexFiles] . "' " . $selected . ">" . $files[$indexFiles] . "</option>";
			// }

			foreach (glob('pic/*.{jpg,jpeg,png,bmp}', GLOB_BRACE) as $filename) {

				$selected = ($row['imagem'] == basename($filename)) ? "selected" : "";

				$option.= "<option value='" . basename($filename) . "' " . $selected . ">" . basename($filename) . "</option>";
			}

			$row["imagem_lista"] = $option;
			// $row["block_product_produto"] = $tplProduct->getContent($row, "BLOCK_PRODUCT_PRODUTO");

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_POPUP_PRODUCTIMAGE"));
		}

		break;

	case "image_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$imagem = $_POST['imagem'];

		$product = new Product;

		$product->Update([
			'id_produto' => $id_produto,
			'field' => 'imagem',
			'value' => $imagem,
		]);

		$product->SearchByCode($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);

			$tplProduct = new View('product');

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT"));

		} else {

			Notifier::Add("Erro as salvar imagem do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_barcode_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$rowProduct = Product::FormatFields($rowProduct);

		} else {

			Notifier::Add("Erro ao carregar dados do produto.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$barCode = new BarCode();
		$barCode->getList($id_produto);

		$tplProduct = new View('product');

		$tableLine = "";

		$hidden = "";

		if ($row = $barCode->getResult()) {

			$hidden = "hidden";

			do {
				$row = BarCode::Format($row);

				$tableLine.= $tplProduct->getContent($row, "EXTRA_BLOCK_FORM_CODBAR_TR");

			} while ($row = $barCode->getResult());

		}

		$rowProduct["hidden"] = $hidden;
		$rowProduct["extra_block_form_codbar_tr"] = $tableLine;

		Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_POPUP_CODBAR"));

		break;

	case "barcode_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$value = $_POST['value'];

		$barCode = new BarCode();

		$barCode->Read($value);

		if ($row = $barCode->getResult()) { //If already codbar

			Notifier::Add("Código de barras já cadastrado para: <br>" .$row['id_produto'] . " - " . $row['produto'], Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$barCode->Create($id_produto, $value);

			$tplProduct = new View('product');

			$barCode->Read($value);

			if ($row = $barCode->getResult()) {

				$row = BarCode::Format($row);

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_FORM_CODBAR_TR"));

			} else {

				Notifier::Add("Erro ao ler Código de barras!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "barcode_delete":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$barcode = $_POST['codbar'];

		$barCode = new BarCode();

		$result = $barCode->Delete($barcode);

		if ($result == 0) {

			Notifier::Add("Erro ao excluir código de barras!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			Notifier::Add("Código de barras removido com sucesso!", Notifier::NOTIFIER_DONE);
			Send([]);
		}
		break;

	case "product_validade_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$row = Product::FormatFields($row);
			$produto = $row['produto'];
			$produtotipo = $row['produtotipo'];
			$extra_block_product_button_status = $row["extra_block_product_button_status"];
		}

		$validade = new ProductExpDate();
		$validade->getList($id_produto);

		$tplProduct = new View('product');
		$tplCP = new View('home');

		$tableLine = "";

		$product_expdate_notfound = "";

		if ($row = $validade->getResult()) {

			$product_expdate_notfound = "hidden";

			do {
				$row = ProductExpDate::FormatFields($row);

				if ($row["dias"] <= 0) {

					$row["extra_block_productexpdate_days"] = $tplCP->getContent([], "EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED");

				} else {

					$row["extra_block_productexpdate_days"] = $tplCP->getContent($row, "EXTRA_BLOCK_PRODUCTEXPDATE_DAYS");
				}

				$tableLine.= $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_EXPDATE_TR");

			} while ($row = $validade->getResult());

		}

		$data = [
			"id_produto" => $id_produto,
			"produto" => $produto,
			"produtotipo" => $produtotipo,
			"extra_block_product_button_status" => $extra_block_product_button_status,
			"product_expdate_notfound" => $product_expdate_notfound,
			"extra_block_product_expdate_tr" => $tableLine,
			"data" => date('Y-m-d')
		];

		Send($tplProduct->getContent($data, "EXTRA_BLOCK_POPUP_VALIDADE"));

		break;

	case "validade_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produto = $_POST['id_produto'];
		$validade = $_POST['validade'];

		$productExpDate = new ProductExpDate();

		$productExpDate->Search($id_produto, $validade);

		if ($row = $productExpDate->getResult()) {

			Notifier::Add("Data já cadastrada!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			if ($id_produtovalidade = $productExpDate->Create($id_produto, $validade)) {

				$productExpDate->Read($id_produtovalidade);

				if ($row = $productExpDate->getResult()) {

					$tplProduct = new View('product');

					$row = ProductExpDate::FormatFields($row);

					$row = Product::FormatFields($row);

					$tplCP = new View('home');

					if ($row["dias"] <= 0) {

						$row["extra_block_productexpdate_days"] = $tplCP->getContent([], "EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED");

					} else {

						$row["extra_block_productexpdate_days"] = $tplCP->getContent($row, "EXTRA_BLOCK_PRODUCTEXPDATE_DAYS");
					}

					$data["product_expdate_tr"] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_EXPDATE_TR");

					$config = new Config();

					$config->Read();

					if ($rowConfig = $config->getResult()) {

						if ($row["dias"] <= $rowConfig["product_expirate_days"]) {

							$data["cp_expdate_tr"] = $tplCP->getContent($row, "EXTRA_BLOCK_CP_EXPDATE_TR");
						}
					}

					[$product_list, $expirated, $toexpirate, $days] = ProductExpDate::getListHUD();

					$data["expirated"] = $expirated;
					$data["toexpirate"] = $toexpirate;

					Send($data);

				} else {

					Notifier::Add("Erro ao carregar data de validade!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Erro ao cadastrar data de validade!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "validade_delete":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produtovalidade = $_POST['id_produtovalidade'];

		$productExpDate = new ProductExpDate();

		$result = $productExpDate->Delete($id_produtovalidade);

		if ($result == 0) {

			Notifier::Add("Erro ao excluir data de validade!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			[$product_list, $expirated, $toexpirate, $days] = ProductExpDate::getListHUD();

			$data = [
				"expirated" => $expirated,
				"toexpirate" => $toexpirate
			];

			Notifier::Add("Data de validade removida com sucesso!", Notifier::NOTIFIER_DONE);

			Send($data);
		}

		break;

	case "product_profitmargin_open":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_POPUP_PRODUCT_PROFITMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_profitmargin_edit":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_PRODUCT_PROFITMARGIN_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto !", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_profitmargin_cancel":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_PROFITMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_profitmargin_save":

		$id_produto = $_POST['id_produto'];
		$margem_lucro = $_POST["margem_lucro"];

		$product = new Product();

		$product->Update([
			"field" => "margem_lucro",
			"id_produto" => $id_produto,
			"value" => $margem_lucro,
		]);

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_PROFITMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_lossmargin_open":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			if ($rowProduct['id_produtotipo'] != Product::PRODUTO_TIPO_NORMAL) {

				Notifier::Add("Não é possível configurar margem de perda para Composição ou Kit.", Notifier::NOTIFIER_ALERT);
				Send(null);
			}

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_POPUP_PRODUCT_LOSSMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_lossmargin_edit":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_PRODUCT_LOSSMARGIN_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto !", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_lossmargin_cancel":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_LOSSMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_lossmargin_save":

		$id_produto = $_POST['id_produto'];
		$margem_perda = $_POST["margem_perda"];

		$product = new Product();

		$product->Update([
			"field" => "margem_perda",
			"id_produto" => $id_produto,
			"value" => $margem_perda,
		]);

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$tplProduct = new View("product");

			$rowProduct = Product::FormatFields($rowProduct);

			Send($tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_LOSSMARGIN"));

		} else {

			Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complement_show":

		$id_produto = $_POST['id_produto'];

		$tplProduct = new View('product');

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$rowProduct = Product::FormatFields($rowProduct);

		} else {

			Notifier::Add("Erro ao ler dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		// $rowProduct["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");
		$rowProduct["extra_block_complement_tr"] = "";
		$rowProduct["hidden"] = "";

		$productComp = new ProductComplement();
		$productComp2 = new ProductComplement();

		$productComp->getGroups($id_produto);

		if ($row = $productComp->getResult()) {

			$rowProduct["hidden"] = "hidden";

			do {

				$row = ProductComplement::FormatFieldsComplementGroup($row);

				$row["hidden"] = "";

				$row["extra_block_complementgroup_product"] = "";

				$productComp2->getComplements($row["id_complementogrupo"]);

				if ($rowComp2 = $productComp2->getResult()) {

					$row["hidden"] = "hidden";

					do {

						$rowComp2 = Product::FormatFields($rowComp2);

						$rowComp2['preco_complemento_formatted'] = number_format($rowComp2['preco_complemento'], 2, ",", ".");

						$row["extra_block_complementgroup_product"] .= $tplProduct->getContent($rowComp2, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT");

					} while ($rowComp2 = $productComp2->getResult());
				}

				$rowProduct["extra_block_complement_tr"] .= $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENT_TR");

			} while ($row = $productComp->getResult());
		}

		Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_POPUP_COMPLEMENT"));

	break;

	case "product_complementgroup_new":

		$id_produto = $_POST['id_produto'];

		$tplProduct = new View('product');

		$productComp = new ProductComplement();

		$id_complement_group = $productComp->CreateGroup();

		if ($productComp->LinkProduct($id_produto, $id_complement_group)) {

			$productComp->getGroup($id_complement_group);

			if ($row = $productComp->getResult()) {

				$row = ProductComplement::FormatFieldsComplementGroup($row);

				$row["id_produto"] = $id_produto;

				$row["extra_block_complementgroup_product"] = "";

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENT_TR"));

			} else {

				Notifier::Add("Erro ao ler grupo de complentos do produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao vincular grupo de complentos ao produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_selectshow":

		$id_produto = $_POST['id_produto'];

		$tplProduct = new View('product');

		$product = new Product();

		$product->Read($id_produto);

		$data = [];

		if ($rowProduct = $product->getResult()) {

			$rowProduct = Product::FormatFields($rowProduct);

			$data = [
				"extra_block_product_button_status" => $rowProduct["extra_block_product_button_status"],
				"block_product_produto" => $rowProduct["block_product_produto"]
			];

		} else {

			Notifier::Add("Erro ao ler dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$productComp = new ProductComplement();

		$productComp->ReadGroupsNotIn($id_produto);

		if ($row = $productComp->getResult()) {

			$list = "";

			do {

				$row = ProductComplement::FormatFieldsComplementGroup($row);

				$row["hidden"] = "";
				$row["id_produto"] = $id_produto;

				$list .= $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_COMPLEMENT_ITEM");

			} while ($row = $productComp->getResult());

			$data["extra_block_product_complement_item"] = $list;

			Send($tplProduct->getContent($data, "EXTRA_BLOCK_PRODUCT_COMPLEMENT_SELECTION"));

		} else {

			Notifier::Add("Nenhum grupo de complementos encontrado para seleção.", Notifier::NOTIFIER_INFO);
			Send(null);
		}

	break;

	case "product_complementgroup_select":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$id_produto = $_POST["id_produto"];

		$productComp = new ProductComplement();
		$productComp2 = new ProductComplement();

		if ($productComp->LinkProduct($id_produto, $id_complementogrupo)) {

			$productComp->getGroup($id_complementogrupo);

			if ($row = $productComp->getResult()) {

				$tplProduct = new View('product');

				$row = ProductComplement::FormatFieldsComplementGroup($row);

				$row["id_produto"] = $id_produto;

				$row["hidden"] = "";

				$row["extra_block_complementgroup_product"] = "";

				$productComp2->getComplements($row["id_complementogrupo"]);

				if ($rowComp2 = $productComp2->getResult()) {

					$row["hidden"] = "hidden";

					do {

						$rowComp2 = Product::FormatFields($rowComp2);

						$rowComp2['preco_complemento_formatted'] = number_format($rowComp2['preco_complemento'], 2, ",", ".");

						$row["extra_block_complementgroup_product"] .= $tplProduct->getContent($rowComp2, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT");

					} while ($rowComp2 = $productComp2->getResult());
				}

				Notifier::Add("Grupo de Complementos adicionado ao produto.", Notifier::NOTIFIER_DONE);

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENT_TR"));

			} else {

				Notifier::Add("Erro ao ler grupo de complentos do produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao selecionar grupo de complementos para o produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_expand":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$id_produto = $_POST["id_produto"];

		$productComp = new ProductComplement();
		$productComp2 = new ProductComplement();

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row = ProductComplement::FormatFieldsComplementGroup($row);

			$row["id_produto"] = $id_produto;

			$row["hidden"] = "";

			$row["extra_block_complementgroup_product"] = "";

			$productComp2->getComplements($row["id_complementogrupo"]);

			if ($rowComp2 = $productComp2->getResult()) {

				$row["hidden"] = "hidden";

				do {

					$rowComp2 = Product::FormatFields($rowComp2);

					$rowComp2['preco_complemento_formatted'] = number_format($rowComp2['preco_complemento'], 2, ",", ".");

					$row["extra_block_complementgroup_product"] .= $tplProduct->getContent($rowComp2, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT");

				} while ($rowComp2 = $productComp2->getResult());
			}

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_EXPANDABLE"));

		} else {

			Notifier::Add("Erro ao ler grupo de complentos do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complement_new":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$tplProduct = new View('product');

		$data = [
			"id_complementogrupo" => $id_complementogrupo,
			"block_product_autocomplete_search" => $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH")
		];

		Send($tplProduct->getContent($data, "EXTRA_BLOCK_COMPLEMENT_ITEM_ADD"));

	break;

	case "product_complement_add":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$id_produto = $_POST["id_produto"];

		$productComp = new ProductComplement();

		if ($id_produtocomplemento = $productComp->addComplement($id_produto, $id_complementogrupo)) {

			$tplProduct = new View('product');

			$productComp->getComplement($id_produtocomplemento);

			if ($row = $productComp->getResult()) {

				$row = Product::FormatFields($row);

				$row['preco_complemento_formatted'] = number_format($row['preco_complemento'], 2, ",", ".");

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT"));

			} else {

				Notifier::Add("Erro ao ler dados de complemento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao adicionar complemento!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_del":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$id_produto = $_POST["id_produto"];

		$productComp = new ProductComplement();

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$product = new Product();

			$product->Read($id_produto);

			if ($rowProduct = $product->getResult()) {

				$rowProduct = Product::FormatFields($rowProduct);

				$row["extra_block_product_button_status"] = $rowProduct["extra_block_product_button_status"];
				$row["produto"] = $rowProduct["produto"];
				$row["id_produto"] = $rowProduct["id_produto"];

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_POPUP_COMPLEMENT_DEL"));

			} else {

				Notifier::Add("Erro ao ler dados do Produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_del_ok":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$id_produto = $_POST["id_produto"];

		$productComp = new ProductComplement();

		if ($productComp->UnlinkProduct($id_produto, $id_complementogrupo) > 0) {

			Send([]);

		} else {

			Notifier::Add("Erro ao remover Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_edit":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_DESCRICAO_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_cancel":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_save":

		$id_complementogrupo = $_POST["id_complementogrupo"];
		$descricao = $_POST["descricao"];

		$productComp = new ProductComplement();

		if ($productComp->UpdateComplementGroupDescricao($id_complementogrupo, $descricao) > 0) {

			$productComp->getGroup($id_complementogrupo);

			if ($row = $productComp->getResult()) {

				$tplProduct = new View('product');

				Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_DESCRICAO"));

			} else {

				Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao atualizar descrição do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_min_add":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->addQtdMin($id_complementogrupo);

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row = ProductComplement::FormatFieldsComplementGroup($row);

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_QTDMINMAX"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_min_del":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->delQtdMin($id_complementogrupo);

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row = ProductComplement::FormatFieldsComplementGroup($row);

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_QTDMINMAX"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_max_add":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->addQtdMax($id_complementogrupo);

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row = ProductComplement::FormatFieldsComplementGroup($row);

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_QTDMINMAX"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_max_del":

		$id_complementogrupo = $_POST["id_complementogrupo"];

		$productComp = new ProductComplement();

		$productComp->delQtdMax($id_complementogrupo);

		$productComp->getGroup($id_complementogrupo);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row = ProductComplement::FormatFieldsComplementGroup($row);

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_QTDMINMAX"));

		} else {

			Notifier::Add("Erro ao ler dados do Grupo de Complementos do Produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_complementgroup_product_del":

		$id_produtocomplemento = $_POST["id_produtocomplemento"];

		$productComp = new ProductComplement();

		if ($productComp->delComplement($id_produtocomplemento)) {

			Send([]);

		} else {

			Notifier::Add("Erro ao remover produto do Grupo de Complementos!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	break;

	case "composition_open":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$rowProduct = Product::FormatFields($rowProduct);

			if ($rowProduct['id_produtotipo'] == ProductType::KIT) {

				Notifier::Add("Para criar Produto Composição, primeiro remova todos os itens do Kit.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao ler dados da Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$productComposition = new ProductComposition();

		if ($productComposition->has($id_produto)) {

			$product = new Product();

			$product->Read($id_produto);

			$row = $product->getResult();

			Notifier::Add("[" . $row['produto'] . "] não pode ser Composição por que é composição de outro produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$productComposition->getList($id_produto);

		$tplProduct = new View('product');

		$tableLine = "";

		$composition_notfound = "";

		if ($row = $productComposition->getResult()) {

			$composition_notfound = "hidden";

			do {

				$row = Product::FormatFields($row);

				$row['qtd'] = number_format($row['qtd'], 3,",",".");

				$tableLine .= $tplProduct->getContent($row, "EXTRA_BLOCK_COMPOSITION_TR");

			} while ($row = $productComposition->getResult());

		// } else {

		// 	$tableLine = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPOSITION_NONE");
		// } else {

		// 	$rowProduct["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		// 	$tableLine = $tplProduct->getContent($rowProduct, "EXTRA_BLOCK_COMPOSITION_FIND");
		}

		// $rowProduct["block_product_produto"] = $tplProduct->getContent($rowProduct, "BLOCK_PRODUCT_PRODUTO");
		// $rowProduct["hidden"] = $composition_notfound;
		// $rowProduct["extra_block_composition_tr"] = $tableLine;

		$rowProduct["hidden"] = $composition_notfound;
		$rowProduct["extra_block_composition_tr"] = $tableLine;
		$rowProduct["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		Send($tplProduct->getContent($rowProduct, "EXTRA_BLOCK_POPUP_COMPOSITION"));

		break;

	case "composition_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_composicao = $_POST['id_composicao'];
		$id_produto = $_POST['id_produto'];
		$qtd = $_POST['qtd'];

		if ($qtd == 0){

			Notifier::Add("Erro na quantidade!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$composition = new Product();

		$composition->Read($id_composicao);

		if ($rowComposition = $composition->getResult()) {

			if ($rowComposition['id_produtotipo'] == ProductType::KIT) {

				Notifier::Add("Para criar Composição, primeiro remova todos os itens do Kit.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$productComposition = new ProductComposition();

			if ($productComposition->has($id_composicao)) {

				Notifier::Add("Produto [" . $rowComposition['produto'] . "] não pode ser Composição por que é composição de outro produto!", true);
				Send(null);
			}

			$product = new Product();

			if( is_numeric($id_produto) ) {

				$product->Read($id_produto);

			} else {

				$product->SearchByString($id_produto);
			}

			if ($row = $product->getResult()) {

				if ($row['id_produto'] == $id_composicao) {

					Notifier::Add("Produto não pode ser fração de si mesmo!", Notifier::NOTIFIER_ERROR);
					Send(null);

				} elseif ($row['id_produtotipo'] == ProductType::KIT) {

					Notifier::Add("Produto não pode ser kit!", Notifier::NOTIFIER_ERROR);
					Send(null);

				} elseif ($row['id_produtotipo'] == ProductType::COMPOSICAO) {

					Notifier::Add("Produto não pode ser Composição por que é composição de outro produto!", Notifier::NOTIFIER_ERROR);
					Send(null);

				}

				$id_produto = $row['id_produto'];
				$id_produtotipo = $row['id_produtotipo'];

				$productComposition->Read($id_composicao, $id_produto);

				if ($productComposition->getResult()) {

					Notifier::Add("Produto já está na lista! Altere a quantidade.", Notifier::NOTIFIER_ERROR);
					Send(null);

				} else {

					if ($productComposition->Create($id_composicao, $id_produto, $qtd)) {

						$productComposition->Read($id_composicao, $id_produto);

						if ($row = $productComposition->getResult()) {

							$tplProduct = new View('product');

							$row = Product::FormatFields($row);
							$row['qtd'] = number_format($row['qtd'],3,",",".");

							$data = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPOSITION_TR");

							if ($id_produtotipo == ProductType::PRODUTO) {

								$product->Update([
									"field" => "id_produtotipo",
									"id_produto" => $id_composicao,
									"value" => ProductType::COMPOSICAO,
								]);
							}

							// $productType = new ProductType();

							// $productType->Read(ProductType::COMPOSICAO);

							// if ($row = $productType->getResult()) {

							// 	$produto_tipo = $tplProduct->getContent($row, "BLOCK_TIPO");
							// }

							$product->Read($id_composicao);

							if ($row = $product->getResult()) {

								$row = Product::FormatFields($row);

								Send(
									[
										"composition" => $data,
										"product" => $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT")
									]
								);

							} else {

								Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
								Send(null);
							}
						}

					} else {

						Notifier::Add("Erro ao criar Composição.", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				}
			} else {

				Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Produto raíz da Composição não foi encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_composition_item_del":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_composicao = $_POST['id_composicao'];
		$id_produto = $_POST['id_produto'];
		$data = [];

		$composition = new ProductComposition();

		$tplProduct = new View('product');

		$product = new Product();

		if ($composition->Delete($id_composicao, $id_produto) > 0) {

			$composition->getList($id_composicao);

			$productType = new ProductType();

			if (!$composition->getResult()) {

				$product->Update([
					"id_produto" => $id_composicao,
					"field" => "id_produtotipo",
					"value" => ProductType::PRODUTO,
				]);
			}

			// $data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");
			// $data["id_produto"] = $id_composicao;

			$product->Read($id_composicao);

			if ($row = $product->getResult()) {

				$row = Product::FormatFields($row);

				Send($tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT"));

			} else {

				Notifier::Add("Ocorreu um erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Ocorreu um erro na exclusão da Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_composition_qtd_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_composicao = $_POST['id_composicao'];
		$id_produto = $_POST['id_produto'];

		$composition = new ProductComposition();

		$composition->Read($id_composicao, $id_produto);

		if ($row = $composition->getResult()) {

			$tplComposition = new View("product");

			$row['qtd'] = number_format($row['qtd'],3,",",".");

			Send($tplComposition->getContent($row, "EXTRA_BLOCK_COMPOSITION_QTD_FORM"));

		} else {

			Notifier::Add("Erro pegando quantidade da Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_composition_qtd_cancel":

		$id_composicao = $_POST['id_composicao'];
		$id_produto = $_POST['id_produto'];

		$composition = new ProductComposition;

		$composition->Read($id_composicao, $id_produto);

		if ($row = $composition->getResult()) {

			$tplComposition = new View("product");

			$row['qtd'] = number_format($row['qtd'],3,",",".");

			Send($tplComposition->getContent($row, "BLOCK_COMPOSITION_QTD"));

		} else {

			Notifier::Add("Erro pegando quantidade da Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_composition_qtd_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_composicao = $_POST['id_composicao'];
		$id_produto = $_POST['id_produto'];
		$qtd = $_POST['qtd'];

		if (!is_numeric($qtd)) {

			Notifier::Add("Digite um valor para quantidade.", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else if ($qtd == 0) {

			Notifier::Add("Digite um valor maior que zero para quantidade.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$composition = new ProductComposition();

		$composition->Update([
			'id_composicao' => $id_composicao,
			'id_produto' => $id_produto,
			'field' => 'qtd',
			'value' => $qtd,
		]);

		$composition->Read($id_composicao, $id_produto);

		if ($row = $composition->getResult()) {

			$tplComposition = new View("product");

			$row['qtd'] = number_format($row['qtd'],3,",",".");

			Send($tplComposition->getContent($row, "BLOCK_COMPOSITION_QTD"));

		} else {

			Notifier::Add("Erro pegando quantidade da Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "kit_open":

		$id_produto = $_POST['id_produto'];

		$product = new Product();

		$product->Read($id_produto);

		if ($rowProduct = $product->getResult()) {

			$produto = $rowProduct['produto'];

			if ($rowProduct['id_produtotipo'] == ProductType::COMPOSICAO) {

				Notifier::Add("Para criar kit, primeiro remova os produtos da Composição.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$rowProduct = Product::FormatFields($rowProduct);
		}

		$productKit = new ProductKit();

		if ($productKit->has($id_produto)) {

			Notifier::Add("Produto não pode ser kit por que está na lista de outro kit!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$productKit->getList($id_produto);

		$tplProduct = new View('product');

		$tableLine = "";

		$kit_notfound = "";

		if ($row = $productKit->getResult()) {

			$kit_notfound = "hidden";

			do {
				$row = ProductKit::FormatFields($row);

				$row = Product::FormatFields($row);

				$tableLine.= $tplProduct->getContent($row, "EXTRA_BLOCK_KIT_TR");

			} while ($row = $productKit->getResult());

		// } else {

		// 	$tableLine = $tplProduct->getContent([], 'EXTRA_BLOCK_KIT_NONE');
		}

		$data = [
			"id_produto" => $id_produto,
			"produto" => $produto,
			"hidden" => $kit_notfound,
			"extra_block_kit_tr" => $tableLine,
			"extra_block_product_button_status" => $rowProduct["extra_block_product_button_status"],
			"block_product_produto" => $rowProduct["block_product_produto"],
			"block_product_autocomplete_search" => $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH")
		];

		Send($tplProduct->getContent($data, "EXTRA_BLOCK_POPUP_KIT"));

		break;

	case "kit_add":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_kit = $_POST['id_kit'];
		$id_produto = $_POST['id_produto'];
		$qtd = $_POST['qtd'];

		if ($qtd == 0){

			Notifier::Add("Quantidade não pode ser zero!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$product = new Product();

		$product->Read($id_kit);

		if ($row = $product->getResult()) {

			$id_produtotipo = $row['id_produtotipo'];
			$produto_kit = $row['produto'];
			$preco_kit = $row['preco'];

			if ($row['id_produtotipo'] == ProductType::COMPOSICAO) {

				Notifier::Add("Para criar kit, primeiro remova os produtos da Composição.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao localizar cadastro do kit!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if( is_numeric($id_produto) ) {

			$product->Read($id_produto);

		} else {

			$product->SearchByString($id_produto);
		}

		if ($row = $product->getResult()) {

			if ($row['id_produto'] == $id_kit) {

				Notifier::Add("Produto não pode ser adicionado como seu kit!", Notifier::NOTIFIER_ERROR);
				Send(null);

			} else if ($row['id_produtotipo'] == ProductType::KIT) {

				Notifier::Add("Produto é parte de a outro kit!", Notifier::NOTIFIER_ERROR);
				Send(null);

			} else {

				if ($row['preco_promo'] > 0) {

					$preco = round($qtd * $row['preco_promo'], 2);

				} else {

					$preco = round($qtd * $row['preco'], 2);
				}

				$id_produto = $row['id_produto'];

				$productKit = new ProductKit();

				$productKit->Read($id_kit, $id_produto);

				if ($productKit->getResult()) {

					Notifier::Add("Produto duplicado! Altere a quantidade na lista!", Notifier::NOTIFIER_ERROR);
					Send(null);

				} else {

					if ($productKit->Create($id_kit, $id_produto, $qtd, $preco)) {

						$productKit->Read($id_kit, $id_produto);

						if ($row = $productKit->getResult()) {

							$tplProduct = new View('product');

							$row = ProductKit::FormatFields($row);

							$row = Product::FormatFields($row);

							$data = $tplProduct->getContent($row, "EXTRA_BLOCK_KIT_TR");

							if ($id_produtotipo == ProductType::PRODUTO) {

								$product->Update([
									"id_produto" => $id_kit,
									"field" => "id_produtotipo",
									"value" => ProductType::KIT,
								]);
							}

							UpdatePrecoKit($id_kit, $preco_kit, $produto_kit);

							$product->Read($id_kit);

							if ($row = $product->getResult()) {

								$row = Product::FormatFields($row);

								Send(
									array(
										"kit" => $data,
										"product" => $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT"),
									)
								);

							} else {

								Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
								Send(null);
							}
						}

					} else {

						Notifier::Add("Erro ao cadastrar produto no kit!", Notifier::NOTIFIER_ERROR);
						Send(null);
					}
				}
			}

		} else {

			Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_kit_item_del":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_kit = $_POST['id_kit'];
		$id_produto = $_POST['id_produto'];

		$productKit = new ProductKit();

		$result = $productKit->Delete($id_kit, $id_produto);

		if ($result == 0) {

			Notifier::Add("Ocorreu um erro na exclusão do item do kit!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			$productKit->getList($id_kit);

			$product = new Product();

			if(!$row = $productKit->getResult()) {

				$product->Update([
					"id_produto" => $id_kit,
					"field" => "id_produtotipo",
					"value" => ProductType::PRODUTO,
				]);

			}

			$product->Read($id_kit);

			if ($row = $product->getResult()) {

				UpdatePrecoKit($id_kit, $row['preco'], $row['produto']);

				$tplProduct = new View('product');

				$product->Read($id_kit);

				$row = $product->getResult();

				$row = Product::FormatFields($row);

				// $produto_tipo = $tplProduct->getContent($row, "BLOCK_TIPO");

				// $preco = $tplProduct->getContent($row, "BLOCK_GROUP_PRECO");

				// $data = array(
				// 	"produto_tipo" => $produto_tipo,
				// 	"preco" => $preco,
				// );

				// if ($row['id_produtotipo'] == ProductType::PRODUTO) {

				// 	$data['data'] = $tplProduct->getContent([], 'EXTRA_BLOCK_KIT_NONE');
				// }

				Notifier::Add("Item do kit excluído com sucesso!", Notifier::NOTIFIER_DONE);
				Send($tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT"));

			} else {

				Notifier::Add("Erro ao carregar dados do kit!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "product_kit_qtd_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductKitFormEdit("EXTRA_BLOCK_KIT_QTD_FORM", "Erro pegando quantidade de item do kit!");
		break;

	case "product_kit_qtd_cancel":

		ProductKitFormCancel("BLOCK_KIT_QTD", "Erro pegando quantidade de item do kit!");
		break;

	case "product_kit_qtd_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductKitFormSave('qtd', "EXTRA_BLOCK_KIT_TR", "Erro salvando quantidade de item do kit!");
		break;

	case "product_kit_preco_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductKitFormEdit("EXTRA_BLOCK_KIT_PRECO_FORM", "Erro pegando preço do item do kit!");
		break;

	case "product_kit_preco_cancel":

		ProductKitFormCancel("BLOCK_KIT_PRECO", "Erro pegando preço do item do kit!");
		break;

	case "product_kit_preco_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);
		ProductKitFormSave('preco', "EXTRA_BLOCK_KIT_TR", "Erro salvando preço do item do kit!");
		break;

	case "product_estoque_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);

			} else {

				$screen = $_POST['screen'];

				switch ($screen) {

					case "add":

						ProductFormEdit("EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_ADD", "Produto não encontrado para ajuste de estoque!");
						break;

					case "del":

						ProductFormEdit("EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_DEL", "Produto não encontrado para ajuste de estoque!");
						break;

					case "update":

						ProductFormEdit("EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_UPDATE", "Produto não encontrado para ajuste de estoque!");
						break;

					case "transf":

						$config = new Config();
						$config->Read();

						if ($row = $config->getResult()) {

							if ($row['estoque_secundario'] == 1) {

								ProductFormEdit("EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_TRANSF", "Produto não encontrado para ajuste de estoque!");

							} else {

								Notifier::Add("Ative o estoque secundário em configurações!", Notifier::NOTIFIER_INFO);
								Send(null);
							}

						} else {

							Notifier::Add("Erro ao carregar as configurações!", Notifier::NOTIFIER_ERROR);
							Send(null);
						}

						break;
				}
			}

		} else {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_add":

		ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_ADD);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($estoque > 0) {

			$purchaseItem = new PurchaseOrderItem();

			$custoun = 0;

			$purchaseItem->getLastProductEntry($id_produto);

			if ($row = $purchaseItem->getResult()) {

				$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
			}

			$log->ProdutoEstoqueAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs);

			$product->UpdateStock($id_produto, $estoque);
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCK"));

		} else {

			Notifier::Add("Erro ao ler estoque do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_remove":

		ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_DEL);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($estoque > 0) {

			$purchaseItem = new PurchaseOrderItem();

			$custoun = 0;

			$purchaseItem->getLastProductEntry($id_produto);

			if ($row = $purchaseItem->getResult()) {

				$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
			}

			$log->ProdutoEstoqueDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs);

			$product->UpdateStock($id_produto, -$estoque);
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCK"));

		} else {

			Notifier::Add("Erro ao ler estoque do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_update":

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($row['estoque'] > $estoque) {

			$estoque = $row['estoque'] - $estoque;

			if ($estoque > 0) {

				ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_DEL);

				$purchaseItem = new PurchaseOrderItem();

				$custoun = 0;

				$purchaseItem->getLastProductEntry($id_produto);

				if ($row = $purchaseItem->getResult()) {

					$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
				}

				$log->ProdutoEstoqueDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs);

				$product->UpdateStock($id_produto, -$estoque);
			}

		} else {

			$estoque -= $row['estoque'];

			if ($estoque > 0) {

				ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_ADD);

				$purchaseItem = new PurchaseOrderItem();

				$custoun = 0;

				$purchaseItem->getLastProductEntry($id_produto);

				if ($row = $purchaseItem->getResult()) {

					$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
				}

				$log->ProdutoEstoqueAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs);

				$product->UpdateStock($id_produto, $estoque);
			}
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCK"));

		} else {

			Notifier::Add("Erro ao ler estoque do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_transf":

		ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_DEL);
		ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_ADD);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs1 = "Transferência para o estoque secundário.";
		$obs2 = "Transferência do estoque primário.";

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if (!$row = $product->getResult()) {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($row['id_produtotipo'] != ProductType::PRODUTO) {

			Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$log = new Log();

		if ($estoque > 0) {

			$purchaseItem = new PurchaseOrderItem();

			$custoun = 0;

			$purchaseItem->getLastProductEntry($id_produto);

			if ($row = $purchaseItem->getResult()) {

				$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
			}

			$log->ProdutoEstoqueDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs1);

			$product->UpdateStock($id_produto, -$estoque);

			$log->ProdutoEstoqueSecundarioAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs2);

			$product->UpdateStockSec($id_produto, $estoque);

		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			$data = [
				"estoque" => $tplProduct->getContent($row, "BLOCK_PRODUCT_STOCK"),
				"estoque_secundario" => $tplProduct->getContent($row, "BLOCK_PRODUCT_STOCKSECOND")
			];

			Notifier::Add("Transferência de estoque realizada!", Notifier::NOTIFIER_DONE);
			Send($data);

		} else {

			Notifier::Add("Erro ao ler estoque do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_secundario_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);

			} else {

				$screen = $_POST['screen'];

				switch ($screen) {

					case "add":

						ProductFormEdit("EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_ADD", "Produto não encontrado para ajuste de estoque!");
						break;

					case "del":

						ProductFormEdit("EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_DEL", "Produto não encontrado para ajuste de estoque!");
						break;

					case "update":

						ProductFormEdit("EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_UPDATE", "Produto não encontrado para ajuste de estoque!");
						break;

					case "transf":

						ProductFormEdit("EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_TRANSF", "Produto não encontrado para ajuste de estoque!");
						break;
				}
			}

		} else {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_secundario_add":

		ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_ADD);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($estoque > 0) {

			$log->ProdutoEstoqueSecundarioAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs);

			$product->UpdateStockSec($id_produto, $estoque);
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCKSECOND"));

		} else {

			Notifier::Add("Erro ao ler estoque secundário do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_secundario_remove":

		ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_DEL);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($estoque > 0) {

			$log->ProdutoEstoqueSecundarioDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs);

			$product->UpdateStockSec($id_produto, -$estoque);
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCKSECOND"));


		} else {

			Notifier::Add("Erro ao ler estoque secundário do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_secundario_update":

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs = $_POST['obs'];

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if ($row = $product->getResult()) {

			if ($row['id_produtotipo'] != ProductType::PRODUTO) {

				Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$log = new Log();

		if ($row['estoque_secundario'] > $estoque) {

			$estoque = $row['estoque_secundario'] - $estoque;

			if ($estoque > 0) {

				ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_DEL);

				$log->ProdutoEstoqueSecundarioDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs);

				$product->UpdateStockSec($id_produto, -$estoque);
			}

		} else {

			$estoque -= $row['estoque_secundario'];

			if ($estoque > 0) {

				ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_ADD);

				$log->ProdutoEstoqueSecundarioAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs);

				$product->UpdateStockSec($id_produto, $estoque);
			}
		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			Send($tplProduct->getContent($row, "BLOCK_PRODUCT_STOCKSECOND"));

		} else {

			Notifier::Add("Erro ao ler estoque secundário do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_estoque_secundario_transf":

		ControlAccess::Check(ControlAccess::CA_ESTOQUE_SECUNDARIO_DEL);
		ControlAccess::Check(ControlAccess::CA_PRODUTO_ESTOQUE_ADD);

		$id_produto = $_POST['id_produto'];
		$estoque = $_POST['estoque'];
		$obs1 = "Transferência para o estoque primário.";
		$obs2 = "Transferência do estoque secundário.";

		$product = new Product();

		$product->Read($_POST['id_produto']);

		if (!$row = $product->getResult()) {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($row['id_produtotipo'] != ProductType::PRODUTO) {

			Notifier::Add("Produto não pode ser Kit ou Composição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$log = new Log();

		if ($estoque > 0) {

			$log->ProdutoEstoqueSecundarioDel($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $obs1);

			$product->UpdateStockSec($id_produto, -$estoque);

			$purchaseItem = new PurchaseOrderItem();

			$custoun = 0;

			$purchaseItem->getLastProductEntry($id_produto);

			if ($row = $purchaseItem->getResult()) {

				$custoun = Calc::Div($row["custo"], $row["qtdvol"], 2);
			}

			$log->ProdutoEstoqueAdd($GLOBALS['authorized_id_entidade'], $id_produto, $estoque, $custoun, $obs2);

			$product->UpdateStock($id_produto, $estoque);

		}

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			$data = [
				"estoque" => $tplProduct->getContent($row, "BLOCK_PRODUCT_STOCK"),
				"estoque_secundario" => $tplProduct->getContent($row, "BLOCK_PRODUCT_STOCKSECOND")
			];

			Notifier::Add("Transferência de estoque realizada!", Notifier::NOTIFIER_DONE);
			Send($data);

		} else {

			Notifier::Add("Erro ao ler estoque do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "product_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produtosetor = $_POST['id_produtosetor'];

		if ($id_produtosetor == 0) {

			$productSector = new ProductSector();

			$productSector->getList();

			if ($row = $productSector->getResult()) {

				$id_produtosetor = $row["id_produtosetor"];

			} else {

				Notifier::Add("Não há setor cadastrado para cadastro de produto!", Notifier::NOTIFIER_INFO);
				Send(null);
			}
		}

		$product = new Product();

		$product->Create($id_produtosetor);

		if ($row = $product->getResult()) {

			$tplProduct = new View('product');

			$row = Product::FormatFields($row);

			$data = [
				"data" => $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT"),
				"id_produto" => $row["id_produto"]
			];

			Send($data);

		} else {

			Notifier::Add("Erro ao criar novo produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "productsector_expand":

		$id_produtosetor = $_POST['id_produtosetor'];

		$product = new Product();

		$product->getAllProductsFromSector($id_produtosetor);

		$extra_block_product = "";

		$tplProduct = new View('product');

		// $hidden = "";

		if ($row = $product->getResult()) {

			do {
				$row = Product::FormatFields($row);

				$extra_block_product .= $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT");

			} while ($row = $product->getResult());

			// $hidden = "hidden";

			Send($extra_block_product);

		} else {

			Send(null);
		}

		// $data = [
		// 	"extra_block_product" => $extra_block_product,
		// 	"hidden" => $hidden
		// ];

		// Send ($tplProduct->getContent($data, "EXTRA_BLOCK_PRODUCT_CONTAINER"));

		break;

	case "produtosetor_new":

		$tplProduct = new View("product");

		Send($tplProduct->getContent([], "EXTRA_BLOCK_POPUP_PRODUCTSECTOR"));

	break;

	case "product_precocomplemento_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produtocomplemento = $_POST["id_produtocomplemento"];

		$productComp = new ProductComplement();

		$productComp->getComplement($id_produtocomplemento);

		if ($row = $productComp->getResult()) {

			$tplProduct = new View('product');

			$row['preco_complemento_formatted'] = number_format($row['preco_complemento'], 2, ",", ".");

			// $row = Product::FormatFields($row);

			// if ($page == "purchase_order.php") {

			// 	$row["id_compraitem"] = $_POST["id_compraitem"];
			// }

			Send($tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO_FORM"));

		} else {

			Notifier::Add("Ocorreu um erro na alteração de preço do complemento!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_precocomplemento_cancel":

		$id_produtocomplemento = $_POST['id_produtocomplemento'];

		$tplProduct = new View('product');

		$productComp = new ProductComplement();
		$productComp->getComplement($id_produtocomplemento);

		if ($row = $productComp->getResult()) {

			$row['preco_complemento_formatted'] = number_format($row['preco_complemento'], 2, ",", ".");

			Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO"));

		} else {

			Notifier::Add("Erro pegando preço do complemento!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "product_precocomplemento_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		$id_produtocomplemento = $_POST['id_produtocomplemento'];
		$preco = $_POST['value'];

		$productComp = new ProductComplement();

		if ($productComp->updatePriceComplement($id_produtocomplemento, $preco)) {

			$productComp->getComplement($id_produtocomplemento);

			if ($row = $productComp->getResult()) {

				$tplProduct = new View('product');

				$row['preco_complemento_formatted'] = number_format($row['preco_complemento'], 2, ",", ".");

				Send($tplProduct->getContent($row, "BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO"));

			} else {

				Notifier::Add("Erro ao ler preço do complemento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao salvar preço do complemento!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}