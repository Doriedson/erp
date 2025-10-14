<?php

namespace database;

class PurchaseOrderItem extends Connection {

	public function DeleteAll($id_compra) {

		$this->data = [
			"id_compra" => $id_compra
		];

		$this->query = "DELETE from tab_compraitem
						WHERE id_compra = :id_compra";

		parent::Execute();
	}

	public function getLastProductEntry($id_produto) {

		$this->data = [
			"id_produto" => $id_produto,
			"id_comprastatus" => PurchaseOrder::COMPRA_STATUS_FINALIZADA
		];

		$this->query = "SELECT tab_compraitem.*, tab_compra.data, tab_produtounidade.produtounidade, tab_produto.margem_perda
						FROM tab_compraitem
						INNER JOIN tab_compra ON tab_compra.id_compra = tab_compraitem.id_compra
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compraitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE tab_compraitem.id_produto = :id_produto and tab_compra.id_comprastatus = :id_comprastatus
						ORDER BY tab_compra.data DESC LIMIT 1";

		parent::Execute();
	}

	public function Create($data) {

		$this->data = [
			'id_compra' => $data['id_compra'],
			'id_produto' => $data['id_produto'],
			'qtdvol' => $data['qtdvol'],
			'vol' => $data['vol'],
			'custo' => $data['custo'],
			'obs' => $data['obs']
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_compraitem ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function getItems($id_compra) {

		$this->data = ["id_compra" => $id_compra];

		$this->query = "SELECT *, tab_compraitem.obs as obs, tab_produtotipo.produtotipo
						FROM tab_compraitem
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compraitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
						WHERE id_compra = :id_compra
						order by tab_compraitem.id_compraitem";

		parent::Execute();
	}

	public function Read($id_compraitem) {

		$this->data = [
			"id_compraitem" => $id_compraitem
		];

		$this->query = "SELECT *, tab_compraitem.obs as obs, tab_produtotipo.produtotipo
						FROM tab_compraitem
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compraitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
						WHERE id_compraitem = :id_compraitem";

		parent::Execute();
	}

	public function Delete($id_compraitem) {

		$this->data = [
			"id_compraitem" => $id_compraitem
		];

		$this->query = "DELETE FROM tab_compraitem
						WHERE id_compraitem = :id_compraitem";

		parent::Execute();

		return parent::rowCount();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_compraitem" => $data['id_compraitem'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_compraitem set $field = :value where id_compraitem = :id_compraitem";

		parent::Execute();
	}

	public function isValidItems($id_compra) {

		$ret = true;

		$this->data = ["id_compra" => $id_compra];

		$this->query = "SELECT * FROM tab_compraitem
						WHERE id_compra = :id_compra AND (vol = 0 or custo = 0 or qtdvol = 0)";


		parent::Execute();
		$ret = (parent::rowCount() == 0);

		if ($ret == false) {

			Notifier::Add("Há produtos com volume, qtd/vol ou custo com valor zero!", Notifier::NOTIFIER_INFO);

		} else {

			$this->data = ["id_compra" => $id_compra];

			$this->query = "SELECT * FROM tab_compraitem
							INNER JOIN tab_produto on tab_produto.id_produto = tab_compraitem.id_produto
							WHERE id_compra = :id_compra AND tab_produto.id_produtotipo != " . ProductType::PRODUTO;

			parent::Execute();
			$ret = (parent::rowCount() == 0);

			if ($ret == false) {

				Notifier::Add("Não é possível finalizar ordem de compra com Produtos Composição ou Kit!", Notifier::NOTIFIER_INFO);
			}
		}

		return $ret;
	}

	public static function getCompositionCost($id_compraitem, $id_composicao) {

		$custo = 0;
		$custo_ajustado = 0;

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->Read($id_compraitem);

		if ($rowItem = $purchaseItem->getResult()) {

			$id_produto = $rowItem['id_produto'];

			$main_custoun = 0;

			if ($rowItem['qtdvol'] > 0) {

				$main_custoun = Calc::Div($rowItem['custo'], $rowItem['qtdvol'], 2);
			}

			$main_custoun_ajustado = Calc::Div(
				$main_custoun,
				Calc::Mult(
					Calc::Sum([
						100,
						-$rowItem["margem_perda"]
					]),
					0.01,
					2
				),
				2
			);

			$productComposition = new ProductComposition();

			$productComposition->getList($id_composicao);

			while ($row = $productComposition->getResult()) {

				//Já pegou o custo do produto principal, levantar custo do restante dos produtos se houver.
				if ($row['id_produto'] == $id_produto) {

					$custo = Calc::Sum([
						$custo,
						Calc::Mult($main_custoun, $row['qtd'], 2)
					]);

					$custo_ajustado = Calc::Sum([
						$custo_ajustado,
						Calc::Mult($main_custoun_ajustado, $row['qtd'], 2)
					]);

				} else {

					$purchaseItem->getLastProductEntry($row['id_produto']);

					if ($rowItem = $purchaseItem->getResult()) {

						$custoun = 0;

						if ($rowItem['qtdvol'] > 0) {

							$custoun = Calc::Div($rowItem['custo'], $rowItem['qtdvol'], 2);
						}

						$custoun_ajustado = Calc::Div(
							$custoun,
							Calc::Mult(
								Calc::Sum([
									100,
									-$rowItem["margem_perda"]
								]),
								0.01,
								2
							),
							2
						);

						$custo = Calc::Sum([
							Calc::Mult($custoun, $row['qtd'], 2),
							$custo
						]);

						$custo_ajustado = Calc::Sum([
							Calc::Mult($custoun_ajustado, $row['qtd'], 2),
							$custo_ajustado
						]);
					}
				}
			}

		} else {

			Notifier::Add("Erro ao calcular custo do produto.", Notifier::NOTIFIER_ERROR);
		}

		return [$custo, $custo_ajustado];
	}

	public static function FormatFields($row) {

		$row['vol_formatted'] = number_format($row['vol'],3,',','.');

		$row['qtdvol_formatted'] = number_format($row['qtdvol'],3,',','.');

		$row['estoque_formatted'] = number_format($row['estoque'],3,',','.');

		$row['custo_formatted'] = number_format($row['custo'],2,',','.');
		$row['preco_formatted'] = number_format($row['preco'],2,',','.');
		// $row['venda_formatted'] = number_format($row['venda'],2,',','.');

		$row['custo_unidade'] = 0;
		$row['custo_unidade_ajustado'] = 0;

		if ($row["qtdvol"] > 0) {

			$row['custo_unidade'] = round($row["custo"] / $row["qtdvol"], 2);

			$row['custo_unidade_ajustado'] = Calc::Div(
				Calc::Div(
					$row["custo"],
					Calc::Mult(
					Calc::Sum([
							100 ,
							- $row["margem_perda"]
						]),
						0.01,
						2
					),
					2),
				$row["qtdvol"],
				2
			);
		}

		if ($row['custo_unidade'] == $row['custo_unidade_ajustado']) {

			$row['custo_ajustado_visible'] = "hidden";
		}

		// if ($row['qtdvol'] > 0) {

		// 	$row['custo_unidade'] = round($row["custo"] / $row["qtdvol"], 2);

		// } else {

		// 	$row['custo_unidade'] = 0;
		// }

		$row = PurchaseOrder::FormatCost($row);

		// $row['product_class_preco'] = "product_" . $row['id_produto'] . "_preco";
		// $row['product_class_preco_promo'] = "product_" . $row['id_produto'] . "_preco_promo";

		$tplPurchase = new View("templates/purchase_order");

		if ($row['obs'] == "") {

			$row['extra_block_purchaseorderitem_obs'] = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASEORDERITEM_OBS_EMPTY");

		} else {

			$row['extra_block_purchaseorderitem_obs'] = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASEORDERITEM_OBS");
		}

		if (array_key_exists('custohistoryun', $row)) {

			if ($row['custo_unidade'] < $row['custohistoryun']) {

				$row['custo_arrow'] = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_DOWN");

			} else if ($row['custo_unidade'] > $row['custohistoryun']) {

				$row['custo_arrow'] = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_UP");

			} else {

				$row['custo_arrow'] = $tplPurchase->getContent($row, "EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_EQUAL");
			}
		}

		return $row;
	}
}