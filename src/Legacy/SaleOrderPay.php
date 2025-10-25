<?php

namespace App\Legacy;

class SaleOrderPay extends Connection {

	public function Create($id_venda, $id_especie, $valor, $valor_recebido) {

		$this->data = [
			"id_venda" => $id_venda,
			"id_especie" => $id_especie,
			"valor" => $valor,
			"valor_recebido" => $valor_recebido
		];

		$this->query = "INSERT INTO tab_vendapay
						(id_venda, id_especie, valor, valor_recebido) VALUES (:id_venda, :id_especie, :valor, :valor_recebido)";

		parent::Execute();
		return parent::rowCount();
	}

	public function Read($id_vendapay) {

		$this->data = [
			"id_vendapay" => $id_vendapay,
		];

		$this->query = "SELECT * FROM tab_vendapay
						WHERE id_vendapay = :id_vendapay";

		parent::Execute();
	}

	public function Delete($id_vendapay) {

		$salePay = new SaleOrderPay();

		$salePay->Read($id_vendapay);

		if ($row = $salePay->getResult()) {

			// Entity credit
			if ($row['id_especie'] == 2) {

				$sale = new SaleOrder();

				$sale->ReadOnly($row['id_venda']);

				if ($rowSale = $sale->getResult()) {

					$entity = new Entity();

					$entity->setCredito($rowSale['id_entidade'], $row['valor'], "Estorno de pagamento do pedido " . $rowSale['id_venda'], $GLOBALS["authorized_id_entidade"]);
				}
			}
		}

		$this->data = [
			"id_vendapay" => $id_vendapay,
		];

		$this->query = "DELETE from tab_vendapay where id_vendapay = :id_vendapay";

		parent::Execute();
		return parent::rowCount();
	}

	public function DeletePaymentSale($id_venda) {

		$count = 0;

		$salePay = new SaleOrderPay();
		$salePay->getList($id_venda);

		while ($row = $salePay->getResult()) {

			if ($this->Delete($row['id_vendapay'])) {

				$count++;
			}
		}

		// $this->data = [
		// 	"id_venda" => $id_venda,
		// ];

		// $this->query = "DELETE from tab_vendapay where id_venda = :id_venda";

		// parent::Execute();
		return $count;
	}

	public function getList($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT *
						FROM tab_vendapay
						INNER JOIN tab_especie ON tab_especie.id_especie = tab_vendapay.id_especie
						WHERE id_venda= :id_venda";

		parent::Execute();
	}

	public function getTotal($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT IFNULL(sum(valor), 0) as total, IFNULL(sum(valor_recebido), 0) as total_recebido
						FROM tab_vendapay
						WHERE id_venda= :id_venda";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['valor_formatted'] = number_format($row['valor'], 2, ",", ".");
		$row['valor_recebido_formatted'] = number_format($row['valor_recebido'], 2, ",", ".");

		return $row;
	}
}