<?php

namespace App\Legacy;

use App\View\View;

class Product extends Connection {

	const PRODUTO_TIPO_NORMAL = 1;
	const PRODUTO_TIPO_COMPOSICAO = 2;
	const PRODUTO_TIPO_KIT = 3;

	public function Read($id_produto) {

		$this->data = [
			"id_produto" => $id_produto
		];

		//EAN-13, EAN-8, UPC-12
		if (mb_strlen($id_produto) == 13 || mb_strlen($id_produto) == 12 || mb_strlen($id_produto) == 8) { //BarCode

			$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
				INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
				INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
				INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
				LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
				WHERE tab_produto.id_produto =
					(SELECT id_produto from tab_produtocodbar
					WHERE codbar = :id_produto)";

		} else if (mb_strlen($id_produto) == 6) { //Barcode EAN13 - 6 last digits

			$this->data = [
				"id_produto" => '%' . $id_produto
			];

			$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
				INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
				INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
				INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
				LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
				WHERE tab_produto.id_produto =
					(SELECT id_produto from tab_produtocodbar
					WHERE codbar like :id_produto)";

		} else { //ID product

			$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
				INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
				INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
				INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
				LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
				WHERE tab_produto.id_produto = :id_produto";
		}

		parent::Execute();
	}

	public function ReadAll() {

		$this->data = [];

		$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
			INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
			INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
			LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
			ORDER BY tab_produtosetor.produtosetor, tab_produto.produto";

		parent::Execute();
	}

	public function getList($list) {

		$this->data = $list;

		// $fields = implode(', ', array_keys($this->data));
		$places = implode(',', array_fill(0, count($this->data), '?'));

		$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
			INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
			INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
			LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
			WHERE tab_produto.id_produto in ($places)";

		parent::Execute();
	}

	public function SearchByString($data, $exact = false, $sort = "", $active_only = false) {

		if ($exact) {

			$this->data = [
				"produto" => $data
			];

			$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
				INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
				INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
				INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
				LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
				WHERE tab_produto.produto = :produto";

		} else {

			$data = (string) $data;
			$data = '%' . str_replace('+', '%+%', $data) . '%';
			$this->data = explode('+', $data);

			$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
				INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
				INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
				INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
				LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
				WHERE tab_produto.produto LIKE ?";

			for ($indexCount = count($this->data); $indexCount > 1; $indexCount--) {
				$this->query .= " AND tab_produto.produto LIKE ?";
			}

			if ($active_only == true) {

				$this->query .= " AND tab_produto.ativo = 1";
			}

			switch ($sort) {

				case "sector":

					$this->query .= " ORDER BY tab_produtosetor.produtosetor, tab_produto.produto";

				break;

				case "active":

					$this->query .= " ORDER BY tab_produto.ativo desc, tab_produto.produto";

				break;

				default:

					$this->query .= " ORDER BY tab_produto.produto";

				break;
			}
		}

		parent::Execute();
	}

	public function SearchByCode($id_produto) {

		$this->Read($id_produto);
	}

	public function ToggleActive($id_produto) {

		$this->data = [
			"id_produto" => $id_produto
		];

		$this->query = "UPDATE tab_produto set ativo = not ativo where id_produto = :id_produto";

		parent::Execute();
	}

	public function ToggleMenuDigital($id_produto) {

		$this->data = [
			"id_produto" => $id_produto
		];

		$this->query = "UPDATE tab_produto set cardapio_produto = not cardapio_produto where id_produto = :id_produto";

		parent::Execute();
	}

	public function Update(array $data) {

		$product = new Product();

		$product->Read($data['id_produto']);

		if (!($row = $product->getResult())) {

			Notifier::Add('Erro ao carregar dados do produto!', Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$field = $data['field'];

		$this->data = [
			"id_produto" => $data['id_produto'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_produto set $field = :value where id_produto = :id_produto";

		parent::Execute();

		if ($field == "preco" || $field == "preco_promo") {

			if ($field == "preco") {

				$data['promocao'] = false;
				$data['old_value'] = $row['preco'];

			} else {

				$data['promocao'] = true;
				$data['old_value'] = $row['preco_promo'];
			}

			$log = new Log();

			$log->Preco($GLOBALS['authorized_id_entidade'], $data['id_produto'], $data['old_value'], $data['value'], $data['promocao']);
		}
	}

	public function Create($id_produtosetor) {

		$this->data = [
			'id_produtosetor' => $id_produtosetor,
			'id_produtounidade' => 1,
			'produto' => 'PRODUTO NOVO',
			'preco' => 0,
			'imagem' => '',
			'ativo' => 0,
			'preco_promo' => 0,
			'estoque' => 0,
			'obs' => ''
		];

		// if ($id_produto) {

		// 	$this->data['id_produto'] = $id_produto;
		// }

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produto ($fields) VALUES ($places)";

		parent::Execute();
		$this->SearchByCode(parent::lastInsertId());
	}

	private function privateUpdateStock($id_produto, $qtd) {

		$this->data = [
			"id_produto" => $id_produto,
			"estoque" => $qtd,
		];

		$this->query = "UPDATE tab_produto
						SET estoque = estoque + :estoque
						WHERE id_produto = :id_produto";

		parent::Execute();
	}

	private function privateUpdateStockSec($id_produto, $qtd) {

		$this->data = [
			"id_produto" => $id_produto,
			"estoque_secundario" => $qtd,
		];

		$this->query = "UPDATE tab_produto
						SET estoque_secundario = estoque_secundario + :estoque_secundario
						WHERE id_produto = :id_produto";

		parent::Execute();
	}

	public function UpdateStockFromSale($id_venda, $id_vendaitem, $qtd) {

		$saleItem = new SaleOrderItem();

		$saleItem->Read($id_venda, $id_vendaitem);

		if ($row = $saleItem->getResult()) {

			switch ($row['id_produtotipo']) {

				case ProductType::KIT:

					$productKit = new ProductKit();

					$productKit->getList($row['id_produto']);

					while ($rowKit = $productKit->getResult()) {

						$this->privateUpdateStock($rowKit['id_produto'], $qtd * $rowKit['qtd']);
					}

					break;

				case ProductType::COMPOSICAO:

					$this->UpdateStock($row['id_produto'], $qtd);

					break;

				case ProductType::PRODUTO:

					$this->privateUpdateStock($row['id_produto'], $qtd);

					break;
			}
		}
	}

	public function UpdateStock($id_produto, $qtd) {

		$this->Read($id_produto);

		if ($row = parent::getResult()) {

			switch ($row['id_produtotipo']) {

				case ProductType::PRODUTO:

					$this->privateUpdateStock($id_produto, $qtd);

					break;

				case ProductType::COMPOSICAO:

					$productComposition = new ProductComposition();

					$productComposition->getList($id_produto);

					if ($row = $productComposition->getResult()) {

						do {

							$this->privateUpdateStock($row['id_produto'], $qtd * $row['qtd']);

						} while ($row = $productComposition->getResult());
					}

					break;

				case ProductType::KIT:

					$productKit = new ProductKit();

					$productKit->getList($id_produto);

					while ($row = $productKit->getResult()) {

						$this->privateUpdateStock($row['id_produto'], $qtd * $row['qtd']);

					}

					break;
			}
		}
	}

	public function UpdateStockSec($id_produto, $qtd) {

		$this->Read($id_produto);

		if ($row = parent::getResult()) {

			switch ($row['id_produtotipo']) {

				case ProductType::PRODUTO:

					$this->privateUpdateStockSec($id_produto, $qtd);

					break;

				case ProductType::COMPOSICAO:

					$productComposition = new ProductComposition();

					$productComposition->getList($id_produto);

					if ($row = $productComposition->getResult()) {

						do {

							$this->privateUpdateStockSec($row['id_produto'], $qtd * $row['qtd']);

						} while ($row = $productComposition->getResult());
					}

					break;

				case ProductType::KIT:

					$productKit = new ProductKit();

					$productKit->getList($id_produto);

					while ($row = $productKit->getResult()) {

						$this->privateUpdateStockSec($row['id_produto'], $qtd * $row['qtd']);

					}

					break;
			}
		}
	}

	public function hasProductSector($id_produtosetor) {

		$this->data = [
			'id_produtosetor' => $id_produtosetor,
		];

		$this->query = "SELECT * FROM tab_produto
			WHERE id_produtosetor = :id_produtosetor limit 1";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public function hasPrinter($id_impressora) {

		$ret = false;

		$this->data = [
			'id_impressora' => $id_impressora,
		];

		$this->query = "SELECT * FROM tab_produto
			WHERE id_impressora = :id_impressora limit 1";

		parent::Execute();

		$ret = (parent::rowCount() > 0);

		if ($ret == false) {

			$this->query = "SELECT * FROM tab_impressao
				WHERE id_impressora = :id_impressora limit 1";

			parent::Execute();

			$ret = (parent::rowCount() > 0);
		}

		return $ret;
	}

	public function getProductBySector($id_produtosetor) {

		$this->data = [
			'id_produtosetor' => $id_produtosetor,
		];

		$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
						INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
						WHERE tab_produto.id_produtosetor = :id_produtosetor AND tab_produto.ativo = 1
						ORDER BY tab_produto.produto";

		parent::Execute();
	}

	public function getAllProductsFromSector($id_produtosetor) {

		$this->data = [
			'id_produtosetor' => $id_produtosetor,
		];

		$this->query = "SELECT *, tab_impressora.descricao as printer_desc FROM tab_produto
						INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_produto.id_impressora
						WHERE tab_produto.id_produtosetor = :id_produtosetor
						ORDER BY tab_produto.produto";

		parent::Execute();
	}

	public function getDigitalMenuSector($id_produtosetor) {

		$this->data = [
			'id_produtosetor' => $id_produtosetor,
		];

		$this->query = "SELECT * FROM tab_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE tab_produto.id_produtosetor = :id_produtosetor and tab_produto.cardapio_produto = 1
						ORDER BY tab_produto.produto";

		parent::Execute();
	}

	public static function getImageFromName($string) {

		$caracteres_sem_acento = array(
    		'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Â'=>'Z', 'Â'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    		'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Å'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'Å'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
			'Ä'=>'a', 'È'=>'s', 'È'=>'t', 'Ä'=>'A', 'È'=>'S', 'È'=>'T',
		);

		$string_new = preg_replace("/[^a-zA-Z]/", "", strtr($string, $caracteres_sem_acento));

		return strtolower(substr($string_new, 0, 1)) . ".jpg";
	}

	public static function FormatFields($row) {

		$tplProduct = new View('product');
		// $tplDigitalMenu = new View('digital_menu_config');

		$row['block_product_produto'] = $tplProduct->getContent($row, "BLOCK_PRODUCT_PRODUTO");

		if ($row['ativo'] == 1) {

			$row['extra_block_product_button_status'] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_BUTTON_ATIVO");
			$row['class_status'] = "pseudo-button button-green";

		} else {

			$row['extra_block_product_button_status'] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_BUTTON_INATIVO");
			$row['class_status'] = "pseudo-button button-red";
		}

		if (array_key_exists('cardapio_produto', $row)) {

			if ($row['cardapio_produto'] == 1) {

				$row['cardapio_produto'] = "checked";
				// $row['button_setor_menu'] = $tplDigitalMenu->getContent($row, "EXTRA_BLOCK_PRODUCT_MENU_SHOW");

			} else {

				$row['cardapio_produto'] = "";
				// $row['button_setor_menu'] = $tplDigitalMenu->getContent($row, "EXTRA_BLOCK_PRODUCT_MENU_HIDE");
			}
		}

		if (array_key_exists('id_impressora', $row)) {

			if ($row['id_impressora'] == null) {

				$row['printer_desc'] = "Não imprimir";
			}

			$printer_option = [
				"id_impressora" => '',
				"selected" => $row['id_impressora'] == null?"selected":"",
				"descricao" => "Não imprimir"
			];

			$row['printer_option'] = $tplProduct->getContent($printer_option,'EXTRA_BLOCK_PRINTER_OPTION');

			$printer = new PrinterConfig();

			$printer->getList();

			while ($row_printer = $printer->getResult()) {

				$row_printer['selected'] = $row['id_impressora'] == $row_printer['id_impressora']?"selected":"";

				$row['printer_option'] .= $tplProduct->getContent($row_printer,'EXTRA_BLOCK_PRINTER_OPTION');
			}
		}

		$row['class_saleoff'] = "";
		$row['class_hidden'] = "";

		if (array_key_exists('preco_promo', $row)) {

			if ($row['preco_promo'] > 0) {

				$row['class_saleoff_saleoff'] = "";
				$row['class_saleoff'] = "saleoff";
				$row['preco_venda'] = "<span class='saleoff'>R$ " . number_format($row['preco'],2,",",".") . "</span>R$ " . number_format($row['preco_promo'],2,",",".");
				$row['preco_final'] = $row['preco_promo'];

			} else {

				$row['class_saleoff_saleoff'] = "saleoff";
				$row['class_hidden'] = "hidden";
				$row['preco_venda'] = "R$ " . number_format($row['preco'],2,",",".") ;
				$row['preco_final'] = $row['preco'];
			}

			$row['preco_promo_formatted'] = number_format($row['preco_promo'],2,",",".");
			$row['preco_final_formatted'] = number_format($row['preco_final'],2,",",".");
		}

		$row['product_id_ativo'] = "product_" . $row['id_produto'] ."_ativo";

		if (array_key_exists('preco', $row)) {

			$row['preco_formatted'] = number_format($row['preco'],2,",",".");
		}

		if (array_key_exists('estoque', $row)) {

			$row['estoque_formatted'] = number_format($row['estoque'],3,",",".");
		}

		$row['preco_percent'] = "";
		$row['preco_promo_percent'] = "";

		if (array_key_exists('estoque_secundario', $row)) {

			$row['estoque_secundario_formatted'] = number_format($row['estoque_secundario'],3,",",".");

			$row['extra_block_estoque_secundario'] = "";

			$config = new Config();

			$config->Read();

			if ($rowConfig = $config->getResult()) {

				if ($rowConfig['estoque_secundario'] == 1) {

					$row['extra_block_estoque_secundario'] = $tplProduct->getContent($row, "EXTRA_BLOCK_ESTOQUE_SECUNDARIO");
				}
			}
		}

		$imagem = self::getImageFromName($row['produto']);

		$row['imagem'] = (empty($row['imagem'])) ? $imagem : $row['imagem'];

		if (array_key_exists('id_produtounidade', $row)) {

			$unit = new ProductUnit();

			$unit->getList();

			$row['produtounidade_option'] = "";

			while ($row_unit = $unit->getResult()) {

				$selected = ($row_unit['id_produtounidade'] == $row['id_produtounidade']) ? "selected" : "";

				$row['produtounidade_option'].= "<option value='" . $row_unit['id_produtounidade'] . "' $selected>" . $row_unit['produtounidade'] . "</option>";
			}
		}

		if (array_key_exists('id_produtosetor', $row)) {

			$sector = new ProductSector();

			$sector->getList();

			$row['setor_option'] = "";

			while ($row_setor = $sector->getResult()) {

				$selected = ($row_setor['id_produtosetor'] == $row['id_produtosetor']) ? "selected" : "";

				$row['setor_option'].= "<option value='" . $row_setor['id_produtosetor'] . "' $selected>" . $row_setor['produtosetor'] . "</option>";
			}
		}

		if (array_key_exists('margem_lucro', $row)) {

			$row['margem_lucro_formatted'] = number_format($row['margem_lucro'], 2, ",", ".");
		}

		if (array_key_exists('margem_perda', $row)) {

			$row['margem_perda_formatted'] = number_format($row['margem_perda'], 2, ",", ".");
		}

		return $row;
	}
}