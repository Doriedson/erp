<?php

namespace App\Legacy;

class SaleOrderItem extends Connection {

	public function Create($id_venda, $id_produto, $id_produtotipo, $qtd, $preco, $obs, $id_entidade = null) {

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_id_entidade'];
		}

		$sale = new SaleOrder();

		$discount_percent = $sale->getSaleOff($id_venda);

		$total_item = round($qtd * $preco, 2);

		$desconto = round($total_item * ($discount_percent * 0.01), 2);

		$id_vendaitem = 1;

		$this->getGreaterIdItem($id_venda);

		if($row = parent::getResult()) {

			$id_vendaitem = $row['id_vendaitem'] + 1;
		}

		$this->data = ['id_venda' => $id_venda,
						'id_entidade' => $id_entidade,
						'id_vendaitem' => $id_vendaitem,
						'id_produto' => $id_produto,
						'id_produtotipo' => $id_produtotipo,
						'qtd' => $qtd,
						'preco' => $preco,
						'desconto' => $desconto,
						'obs' => $obs];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_vendaitem ($fields) VALUES ($places)";

		parent::Execute();

		if (parent::rowCount() == 0) {

			$id_vendaitem = 0;

		} else {

			if ($id_produtotipo == ProductType::KIT) {

				$productKit = new ProductKit();

				$productKit->getList($id_produto);

				if ($row = $productKit->getResult()) {

					$saleItemKit = new SaleOrderItemKit();

					do {

						$saleItemKit->Create($id_venda, $id_vendaitem, $row['id_produto'], $row['qtd'], $row['preco']);

					} while ($row = $productKit->getResult());
				}
			}

			$product = new Product();

			$product->UpdateStockFromSale($id_venda, $id_vendaitem, -$qtd);

			$sale = new SaleOrder();

			$sale->applyFreight($id_venda);
			$sale->applyServiceValue($id_venda);
		}

		return $id_vendaitem;
	}

	public function Read($id_venda, $id_vendaitem) {

		$this->data = [
			"id_vendaitem" => $id_vendaitem,
			"id_venda" => $id_venda
		];

		$this->query = "SELECT tab_vendaitem.*, round(tab_vendaitem.preco * tab_vendaitem.qtd, 2) as subtotal, tab_produto.produto, tab_produto.ativo, tab_produtounidade.produtounidade
						FROM tab_vendaitem
						INNER JOIN tab_produto ON tab_produto.id_produto = tab_vendaitem.id_produto
						INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_venda= :id_venda and id_vendaitem = :id_vendaitem";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$sale = new SaleOrder();

		if ($field == "qtd" || $field == "preco" || ($field == "estornado" && $data['value'] == 0)) {

			$this->Read($data['id_venda'], $data['id_vendaitem']);

			if ($row = parent::getResult()) {

				if ($field == "qtd") {

					$qtd = $data['value'];
					$preco = $row['preco'];

				} elseif ($field == "preco") {

					$qtd = $row['qtd'];
					$preco = $data['value'];

				} elseif ($field == "estornado") {

					$qtd = $row['qtd'];
					$preco = $row['preco'];
				}
			}

			$discount_percent = $sale->getSaleOff($data['id_venda']);

			$total_item = round($qtd * $preco, 2);

			$desconto = round($total_item * ($discount_percent * 0.01), 2);

			$this->data = [
				"id_venda" => $data['id_venda'],
				"id_vendaitem" => $data['id_vendaitem'],
				"value" => $data['value'],
				"desconto" => $desconto
			];

			$this->query = "UPDATE tab_vendaitem
							SET $field = :value, desconto = :desconto
							WHERE id_venda = :id_venda and id_vendaitem = :id_vendaitem";

			parent::Execute();

		} else {

			$this->data = [
				"id_venda" => $data['id_venda'],
				"id_vendaitem" => $data['id_vendaitem'],
				"value" => $data['value'],
			];

			$this->query = "UPDATE tab_vendaitem
							SET $field = :value
							WHERE id_venda = :id_venda and id_vendaitem = :id_vendaitem";

			parent::Execute();

		}

		$sale->applyFreight($data['id_venda']);
		$sale->applyServiceValue($data['id_venda']);

		return parent::rowCount();
	}

	public function Delete($id_venda, $id_vendaitem, $id_entidade = null) {

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_payload']->id;
		}

		$this->data = [
			"id_venda" => $id_venda,
			"id_vendaitem" => $id_vendaitem,
		];

		$this->query = "UPDATE tab_vendaitem
						SET estornado = 1
						WHERE id_venda = :id_venda and id_vendaitem = :id_vendaitem";

		parent::Execute();

		if ($return = parent::rowCount()) {

			$log = new Log();

			$log->EstornoItem($id_entidade, $id_venda, $id_vendaitem);

			$this->Read($id_venda, $id_vendaitem);

			if ($row = parent::getResult()) {

				$product = new Product();

				$product->UpdateStockFromSale($id_venda, $id_vendaitem, $row['qtd']);
			}

			$saleOrder = new SaleOrder();

			$saleOrder->applyFreight($id_venda);
			$saleOrder->applyServiceValue($id_venda);
		}

		return $return;
	}

	public function getGreaterIdItem($id_venda) {

		$this->data = [
			'id_venda' => $id_venda
		];

		$this->query = "SELECT * FROM tab_vendaitem
						WHERE id_venda= :id_venda
						ORDER BY id_vendaitem
						DESC LIMIT 1";

		parent::Execute();
	}

	public function getList($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT tab_vendaitem.*, round(tab_vendaitem.preco * tab_vendaitem.qtd, 2) as subtotal, tab_produto.produto, tab_produto.ativo, tab_produtounidade.produtounidade
						FROM tab_vendaitem
						INNER JOIN tab_produto ON tab_produto.id_produto = tab_vendaitem.id_produto
						INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_venda = :id_venda";

		parent::Execute();
	}

	public function getListActiveItems($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT tab_vendaitem.*, round(tab_vendaitem.preco * tab_vendaitem.qtd, 2) as subtotal, tab_produto.produto, tab_produtounidade.id_produtounidade, tab_produtounidade.produtounidade
						FROM tab_vendaitem
						INNER JOIN tab_produto ON tab_produto.id_produto = tab_vendaitem.id_produto
						INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_venda = :id_venda and estornado = 0";

		parent::Execute();
	}

	public function getTotal($id_venda) {

		$this->data = [
			"id_venda" => $id_venda,
		];

		$this->query = "SELECT sum(round(qtd * preco, 2)) as total, sum(desconto) as desconto
						FROM tab_vendaitem
						WHERE id_venda = :id_venda and estornado = 0";

		parent::Execute();
	}

	public function countItens($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT *
						FROM tab_vendaitem
						WHERE id_venda = :id_venda AND estornado = 0";

		parent::Execute();

		return parent::rowCount();
	}

	public function ReportItemByDate($id_produto, $date) {

		$date = date_create($date);

		$this->data = [
			"id_produto" => $id_produto,
			"year" => date_format($date, "Y"),
			"month" => date_format($date, "m"),
			"day" => date_format($date, "d"),
		];

		$this->query = "SELECT hour(data) as hora, sum(qtd) as qtd, sum(round(qtd*preco,2)) as total
						FROM tab_venda
						INNER JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda
						WHERE year(data) = :year and month(data) = :month and day(data) = :day
						and id_produto = :id_produto and estornado = 0 and id_vendastatus in (
						" . SaleOrder::STATUS_VENDA_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_VENDA_PAGA . ",
						" . SaleOrder::STATUS_PEDIDO_EFETUADO . ",
						" . SaleOrder::STATUS_PEDIDO_IMPRESSO . ",
						" . SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_PEDIDO_PRODUCAO . ",
						" . SaleOrder::STATUS_PEDIDO_ENTREGA . ",
						" . SaleOrder::STATUS_PEDIDO_PAGO . ",
						" . SaleOrder::STATUS_VENDA_PRAZO . ",
						" . SaleOrder::STATUS_MESA_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_MESA_EM_PAGAMENTO . ",
						" . SaleOrder::STATUS_MESA_PAGA . ",
						" . SaleOrder::STATUS_VENDA_PRAZO_PAGA . "
						)
						group by (hour(data))";

		parent::Execute();

		return parent::rowCount();
	}

	public function ReportItemByDateInterval($id_produto, $date_start, $date_end) {

		$this->data = [
			"id_produto" => $id_produto,
			'date_start' => $date_start . " 00:00:00",
			'date_end' => $date_end . " 23:59:59"
		];

		$this->query = "SELECT year(data) as year, month(data) as month, day(data) as day, sum(qtd) as qtd, sum(round(qtd*preco,2)) as total
						FROM tab_venda
						INNER JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda
						WHERE data BETWEEN :date_start AND :date_end
						and id_produto = :id_produto and estornado = 0 and id_vendastatus in (
						" . SaleOrder::STATUS_VENDA_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_VENDA_PAGA . ",
						" . SaleOrder::STATUS_PEDIDO_EFETUADO . ",
						" . SaleOrder::STATUS_PEDIDO_IMPRESSO . ",
						" . SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_PEDIDO_PRODUCAO . ",
						" . SaleOrder::STATUS_PEDIDO_ENTREGA . ",
						" . SaleOrder::STATUS_PEDIDO_PAGO . ",
						" . SaleOrder::STATUS_VENDA_PRAZO . ",
						" . SaleOrder::STATUS_MESA_EM_ANDAMENTO . ",
						" . SaleOrder::STATUS_MESA_EM_PAGAMENTO . ",
						" . SaleOrder::STATUS_MESA_PAGA . ",
						" . SaleOrder::STATUS_VENDA_PRAZO_PAGA . "
						)
						group by year(data), month(data), day(data)";

		parent::Execute();

		return parent::rowCount();
	}

	public function DiscountClear($id_venda) {

		$this->data = [
			"id_venda" => $id_venda,
		];

		$this->query = "UPDATE tab_vendaitem
						SET desconto = 0
						WHERE id_venda = :id_venda";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['subtotal'] = round($row['qtd']*$row['preco'],2);
		$row['total'] =  $row['subtotal'] - $row['desconto'];

		$row['subtotal_formatted'] = number_format($row['subtotal'],2,',','.');
		$row['total_formatted'] = number_format($row['total'],2,',','.');
		$row['qtd_formatted'] = number_format($row['qtd'],3,',','.');
		$row['preco_formatted'] = number_format($row['preco'],2,',','.');

		if (array_key_exists('data', $row)) {

			$row['data_formatted'] = date_format( date_create($row['data']), 'd/m/Y H:i');
		}

		$row['desconto_formatted'] = number_format($row['desconto'],2,',','.');

		$tplSale = new View('sale_order');

		if ($row['obs'] == "") {

			$row['extra_block_saleorderitem_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDERITEM_OBS_EMPTY");

		} else {

			$row['extra_block_saleorderitem_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDERITEM_OBS");
		}

		$tplProduct = new View('product');

		if (key_exists("ativo", $row)) {

			if ($row['ativo'] == 1) {

				$row['extra_block_product_button_status'] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_BUTTON_ATIVO");

			} else {

				$row['extra_block_product_button_status'] = $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_BUTTON_INATIVO");
			}
		}

		return $row;
	}
}