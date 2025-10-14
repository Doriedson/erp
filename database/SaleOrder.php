<?php

namespace database;

class SaleOrder extends Connection {

	const STATUS_VENDA_PAGA = 1;
	const STATUS_VENDA_CANCELADA = 2;
	const STATUS_PEDIDO_EM_ANDAMENTO = 3;
	const STATUS_PEDIDO_EFETUADO = 4;
	const STATUS_PEDIDO_PAGO = 5;
	const STATUS_PEDIDO_CANCELADO = 6;
	const STATUS_VENDA_PRAZO = 7;
	const STATUS_MESA_EM_ANDAMENTO = 8;
	const STATUS_MESA_EM_PAGAMENTO = 9;
	const STATUS_MESA_PAGA = 10;
	const STATUS_MESA_CANCELADA = 11;
	const STATUS_VENDA_EM_ANDAMENTO = 12;
	const STATUS_VENDA_PRAZO_PAGA = 13;
	const STATUS_PEDIDO_IMPRESSO = 14;
	const STATUS_PEDIDO_PRODUCAO = 15;
	const STATUS_PEDIDO_ENTREGA = 16;
	const STATUS_MESA_TRANSFERIDA = 17;

	public function Create($data) {

		$id_colaborador = $GLOBALS['authorized_id_entidade'];

		if (array_key_exists('id_colaborador', $data)) {

			$id_colaborador = $data['id_colaborador'];
		}

		$obs = '';

		if (array_key_exists('obs', $data)) {

			$obs = $data['obs'];
		}

		$mesa = '';

		if (array_key_exists('mesa', $data)) {

			$mesa = $data['mesa'];
		}

		$this->data = [
			"id_vendastatus" => $data['id_vendastatus'],
			"id_entidade" => $data['id_entidade'],
			"id_colaborador" => $id_colaborador,
			"frete" => $data['frete'], //Should always be zero
			"obs" => $obs,
			"mesa" => $mesa,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_venda
						($fields) VALUES ($places)";

		parent::Execute();

		$id = parent::lastInsertId();

		$this->data = [
			"id_venda" => $id,
			"id_entidade" => $id_colaborador,
			"id_vendastatus" => $data['id_vendastatus']
		];

		$this->ChangeStatus($id, $data['id_vendastatus'], $id_colaborador);
		// $this->query = "INSERT INTO tab_vendastatuschange
		// 				(id_venda, id_vendastatus, id_entidade) VALUES (:id_venda, :id_vendastatus, :id_entidade)";

		// parent::Execute();

		return $id;
	}

	public function Read($id_venda) {

		$this->data = [
			"id_venda" => $id_venda,
		];

		$this->query = "SELECT tab_venda.id_venda, tab_venda.versao, tab_venda.id_vendastatus, tab_venda.id_entidade, tab_venda.id_colaborador, tab_venda.id_caixa as id_caixaresp, tab_venda.data, tab_venda.frete, tab_venda.valor_servico, tab_venda.obs, tab_venda.mesa, tab_vendapaycaixa.id_caixa, IFNULL(sum(round(qtd*preco,2)), 0) as subtotal,
						IFNULL(sum(desconto), 0) as desconto, tab_vendastatus.vendastatus
						FROM tab_venda
						LEFT JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_venda = tab_venda.id_venda
						LEFT JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda and tab_vendaitem.estornado = 0
						INNER JOIN tab_vendastatus on tab_vendastatus.id_vendastatus = tab_venda.id_vendastatus
						WHERE tab_venda.id_venda = :id_venda
						GROUP BY tab_venda.id_venda, tab_vendapaycaixa.id_caixa";


		parent::Execute();
	}

	public function ReadOnly($id_venda) {

		$this->data = [
			"id_venda" => $id_venda,
		];

		$this->query = "SELECT *
						FROM tab_venda
						WHERE id_venda = :id_venda";

		parent::Execute();
	}

	public function getOrderList($id_vendastatus) {

		$this->data = [
			'id_vendastatus' => $id_vendastatus
		];

		$this->query = "SELECT tab_venda.id_venda, tab_venda.versao, tab_venda.frete, tab_venda.valor_servico, IFNULL(sum(round(qtd*preco,2)), 0) as subtotal,
						IFNULL(sum(desconto), 0) as desconto, tab_venda.data, tab_venda.id_entidade,
						tab_entidade.nome, tab_entidade.credito, tab_entidade.ativo, tab_vendastatus.id_vendastatus, tab_vendastatus.vendastatus, tab_venda.obs
						FROM tab_venda
						LEFT JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda  AND tab_vendaitem.estornado = 0
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						INNER JOIN tab_vendastatus on tab_vendastatus.id_vendastatus = tab_venda.id_vendastatus
						WHERE tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda, tab_venda.data,  tab_venda.id_entidade,
						tab_entidade.nome, tab_entidade.credito, tab_entidade.ativo, tab_vendastatus.id_vendastatus, tab_vendastatus.vendastatus, tab_venda.obs,
						tab_venda.frete, tab_venda.valor_servico
						ORDER BY tab_venda.data";

		parent::Execute();
	}

	public function Delete($id_venda, $obs, $id_entidade = null, $table_transf = false) {

		$id_vendastatus = null;

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_payload']->id;
		}

		$this->Read($id_venda);

		$row = parent::getResult();

		switch ($row['id_vendastatus']) {

			case self::STATUS_PEDIDO_EM_ANDAMENTO:
			case self::STATUS_PEDIDO_PAGO:
			case self::STATUS_PEDIDO_EFETUADO:
			case self::STATUS_PEDIDO_IMPRESSO: //DEPRECATED
			case self::STATUS_VENDA_PRAZO:
			case self::STATUS_VENDA_PRAZO_PAGA:
			case self::STATUS_PEDIDO_PRODUCAO:
			case self::STATUS_PEDIDO_ENTREGA:

				$id_vendastatus = self::STATUS_PEDIDO_CANCELADO;

			break;

			case self::STATUS_VENDA_EM_ANDAMENTO:
			case self::STATUS_VENDA_PAGA:

				$id_vendastatus = self::STATUS_VENDA_CANCELADA;

			break;

			case self::STATUS_MESA_PAGA:
			case self::STATUS_MESA_EM_ANDAMENTO:
			case self::STATUS_MESA_EM_PAGAMENTO:

				$table = new Table();

				$table->ReadFromSale($id_venda);

				if ($rowTable = $table->getResult()) {

					$table->Free($rowTable['id_mesa']);
				}

				if ($table_transf == true) {

					$id_vendastatus = self::STATUS_MESA_TRANSFERIDA;

				} else {

					$id_vendastatus = self::STATUS_MESA_CANCELADA;
				}

			break;

			default:

				return 0;
		}

		$salePay = new SaleOrderPay();

		$salePay->DeletePaymentSale($id_venda);

		$this->ChangeStatus($id_venda, $id_vendastatus, $id_entidade);
		// $this->Update($id_venda, "id_caixa", $row['id_caixa']);

		if ($table_transf == false) {

			$log = new Log();

			$log->EstornoVenda($id_entidade, $id_venda, $obs);
		}

		$product = new Product();
		$saleItem = new SaleOrderItem();

		$saleItem->getList($id_venda);

		while ($row = $saleItem->getResult()) {

			if ($row['estornado'] == 0) {

				$product->UpdateStockFromSale($id_venda, $row['id_vendaitem'], $row['qtd']);
			}
		}

		return 1;
	}

	public function Update($id_venda, $field, $value) {

		$this->data = [
			"id_venda" => $id_venda,
			"value"	 => $value,
		];

		$this->query = "UPDATE tab_venda
						set $field = :value
						where id_venda = :id_venda";

		parent::Execute();
	}

	public function ChangeStatus($id_venda, $id_vendastatus, $id_entidade = null) {

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_payload']->id;
		}

		$this->data = [
			"id_venda" => $id_venda,
			"id_vendastatus" => $id_vendastatus
		];

		$this->query = "UPDATE tab_venda
						SET id_vendastatus = :id_vendastatus
						WHERE id_venda = :id_venda";

		parent::Execute();

		$this->AddStatus($id_venda, $id_vendastatus, $id_entidade);
	}

	public function AddStatus($id_venda, $id_vendastatus, $id_entidade = null) {

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_payload']->id;
		}

		$this->data = [
			"id_venda" => $id_venda,
			"id_vendastatus" => $id_vendastatus,
			"id_entidade" => $id_entidade
		];

		$this->query = "INSERT INTO tab_vendastatuschange
						(id_venda, id_vendastatus, id_entidade) VALUES (:id_venda, :id_vendastatus, :id_entidade)";

		parent::Execute();
	}

	public function getListStatus() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_vendastatus
						ORDER BY vendastatus";

		parent::Execute();
	}

	public function VendaPrazo($id_venda, $id_entidade = null) {

		$ret = false;

		if ($id_entidade == null) {

			$id_entidade = $GLOBALS['authorized_payload']->id;
		}

		$table = new Table();

		$table->ReadFromSale($id_venda);

		if ($rowTable = $table->getResult()) {

			$table->Free($rowTable['id_mesa']);
		}

		$sale = new SaleOrder();

		$sale->ReadOnly($id_venda);

		if ($rowSale = $sale->getResult()) {

			switch ($rowSale['id_vendastatus']) {

				case SaleOrder::STATUS_PEDIDO_EFETUADO:
				case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED
				case SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO:
				case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
				case SaleOrder::STATUS_MESA_EM_PAGAMENTO:
				case SaleOrder::STATUS_VENDA_EM_ANDAMENTO:
				case SaleOrder::STATUS_PEDIDO_PRODUCAO:
				case SaleOrder::STATUS_PEDIDO_ENTREGA:

					$salePay = new SaleOrderPay();

					$salePay->DeletePaymentSale($id_venda);

					$sale->ChangeStatus($id_venda, SaleOrder::STATUS_VENDA_PRAZO);

					$log = new Log();

					$log->VendaPrazo($id_entidade, $id_venda);

					$ret = true;

					break;
			}
		}

		return $ret;
	}

	public function getTotalPaymentSales($id_entidade, $last_days) {

		$this->data = [
			"id_entidade" => $id_entidade,
			"last_days"	 => $last_days + 1,
		];

		$this->query = "SELECT sum(valor) as total from tab_venda
						INNER JOIN tab_vendapay ON tab_vendapay.id_venda = tab_venda.id_venda
						WHERE id_entidade = :id_entidade
						AND data between now() - interval :last_days day and now() - interval 1 day";

		parent::Execute();
	}

	public function getPaymentsByCashier($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa,
		];

		$this->query = "SELECT IFNULL(sum(valor), 0) as total, tab_vendapay.id_especie, especie
						FROM tab_vendapay
						INNER JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_venda = tab_vendapay.id_venda
						INNER JOIN tab_especie on tab_especie.id_especie = tab_vendapay.id_especie
						WHERE tab_vendapaycaixa.id_caixa = :id_caixa
						GROUP BY tab_vendapay.id_especie, tab_especie.especie";

		parent::Execute();
	}

	public function getTotalPaymentsByCashier($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa,
		];

		$this->query = "SELECT IFNULL(sum(valor), 0) as total
						FROM tab_vendapay
						INNER JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_venda = tab_vendapay.id_venda
						WHERE tab_vendapaycaixa.id_caixa = :id_caixa";

		parent::Execute();
	}

	public function SearchTotalSaleByDate($date, $pdv) {

		$this->SearchTotalSaleByDateInterval($date, $date, $pdv);
	}

	public function SearchTotalSaleByDateInterval($datestart, $dateend, $pdv) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		if ($pdv) {

			$this->query = "SELECT tab_especie.especie, tab_result.*
							FROM tab_especie
							INNER JOIN
								(
								SELECT sum(IFNULL(tab_vendapay.valor,0)) AS valor, IFNULL(tab_vendapay.id_especie, 1) as id_especie, tab_caixa.id_caixa, tab_caixa.obs, tab_entidade.nome, tab_caixa.dataini, tab_caixa.datafim, tab_caixa.trocoini, tab_caixa.trocofim
								FROM tab_caixa
								LEFT JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_caixa = tab_caixa.id_caixa
								LEFT JOIN tab_vendapay on tab_vendapay.id_venda = tab_vendapaycaixa.id_venda
								INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixa.id_entidade
								WHERE tab_caixa.dataini BETWEEN :datestart AND :dateend
								GROUP BY tab_caixa.id_caixa, tab_vendapay.id_especie, tab_entidade.nome, tab_caixa.dataini, tab_caixa.datafim, tab_caixa.trocoini, tab_caixa.trocofim
								) as tab_result
							ON tab_result.id_especie = tab_especie.id_especie
							ORDER BY tab_result.id_caixa, tab_especie.especie";

		} else {

			$this->query = "SELECT tab_especie.*, sum(tab_result.valor) as valor
							FROM tab_especie
							INNER JOIN
								(SELECT IFNULL(valor, 0) AS valor, IFNULL(id_especie, 1) as id_especie
								FROM tab_caixa
								LEFT JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_caixa = tab_caixa.id_caixa
								LEFT JOIN tab_vendapay on tab_vendapay.id_venda = tab_vendapaycaixa.id_venda
								WHERE tab_caixa.dataini BETWEEN :datestart AND :dateend
								) AS tab_result
							ON tab_result.id_especie = tab_especie.id_especie
							GROUP BY id_especie
							ORDER BY especie";
		}

		parent::Execute();
	}

	public function SearchTotalSaleByPDV($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "SELECT tab_especie.especie, tab_result.*
						FROM tab_especie
						INNER JOIN
							(
							SELECT IFNULL(sum(tab_vendapay.valor),0) AS valor, IFNULL(tab_vendapay.id_especie, 1) as id_especie, tab_caixa.id_caixa, tab_caixa.obs, tab_entidade.nome, tab_caixa.dataini, tab_caixa.datafim, tab_caixa.trocoini, tab_caixa.trocofim
							FROM tab_caixa
							LEFT JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_caixa = tab_caixa.id_caixa
							LEFT JOIN tab_vendapay on tab_vendapay.id_venda = tab_vendapaycaixa.id_venda
							INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixa.id_entidade
							WHERE tab_caixa.id_caixa = :id_caixa
							GROUP BY tab_caixa.id_caixa, tab_vendapay.id_especie, tab_entidade.nome, tab_caixa.dataini, tab_caixa.datafim, tab_caixa.trocoini, tab_caixa.trocofim
							) as tab_result
						ON tab_result.id_especie = tab_especie.id_especie
						ORDER BY tab_result.id_caixa, tab_especie.especie";

		parent::Execute();
	}

	public function countCouponByStatus($id_vendastatus) {

		$this->data = [
			'id_vendastatus' => $id_vendastatus,
		];

		$this->query = "SELECT id_venda
						from tab_venda
						WHERE id_vendastatus = :id_vendastatus";

		parent::Execute();

		return parent::rowCount();
	}

	public function getTotalDiscountByPDV($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "SELECT IFNULL(sum(tab_vendaitem.desconto), 0) as desconto
						from tab_vendapaycaixa
						INNER JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_vendapaycaixa.id_venda
						WHERE tab_vendapaycaixa.id_caixa = :id_caixa
						AND tab_vendaitem.estornado = 0";

		parent::Execute();
	}

	public function getTotalDiscountByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT IFNULL(sum(desconto), 0) as desconto FROM tab_caixa
						INNER JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_caixa = tab_caixa.id_caixa
						INNER JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_vendapaycaixa.id_venda
						WHERE tab_caixa.dataini BETWEEN :datestart AND :dateend AND tab_vendaitem.estornado = 0";

		parent::Execute();
	}

	public function getCouponsByDate($date, $id_vendastatus) {

		$this->getCouponsByDateInterval	($date . " 00:00:00", $date . " 23:59:59", $id_vendastatus);
	}

	public function getCouponsByDateInterval($datestart, $dateend, $id_vendastatus) {

		$this->data = [
			'date_start' => $datestart,
			'date_end' => $dateend
		];

		if ($id_vendastatus == -1) {

			$this->query = "SELECT tab_venda.*, c.nome as colaborador, sum(round(qtd*preco, 2)) AS subtotal, sum(desconto) as desconto, tab_vendastatus.vendastatus, tab_entidade.nome, tab_entidade.ativo
						FROM tab_venda
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						INNER JOIN tab_vendastatus ON tab_vendastatus.id_vendastatus = tab_venda.id_vendastatus
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						INNER JOIN tab_entidade c on c.id_entidade = tab_venda.id_colaborador
						WHERE tab_venda.data between :date_start and :date_end
						GROUP BY tab_venda.id_venda";

		} else {

			$this->data['id_vendastatus'] = $id_vendastatus;

			$this->query = "SELECT tab_venda.*, c.nome as colaborador, sum(round(qtd*preco, 2)) AS subtotal, sum(desconto) as desconto, tab_vendastatus.vendastatus, tab_entidade.nome, tab_entidade.ativo
						FROM tab_venda
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						INNER JOIN tab_vendastatus ON tab_vendastatus.id_vendastatus = tab_venda.id_vendastatus
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						INNER JOIN tab_entidade c on c.id_entidade = tab_venda.id_colaborador
						WHERE tab_venda.data between :date_start and :date_end and tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda";
		}

		parent::Execute();
	}

	public function getCouponsByCasher($id_caixa, $id_vendastatus) {

		$this->data = [
			"id_caixa" => $id_caixa,
			"id_vendastatus" => $id_vendastatus,
		];

		$this->query = "SELECT tab_entidade.nome, tab_venda.id_venda, tab_venda.id_entidade, tab_venda.mesa, tab_venda.frete, tab_venda.valor_servico, sum(round(IFNULL(tab_vendaitem.preco, 0) * IFNULL(tab_vendaitem.qtd, 0), 2)) as subtotal, sum(IFNULL(desconto, 0)) as desconto
						FROM tab_venda
						LEFT JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda and tab_vendaitem.estornado = 0
						INNER JOIN tab_vendapaycaixa ON tab_venda.id_venda = tab_vendapaycaixa.id_venda
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						WHERE tab_vendapaycaixa.id_caixa = :id_caixa AND tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda, tab_entidade.nome, tab_venda.id_entidade";

		parent::Execute();
	}

	public function getCouponsTableOpenedUntil($data) {

		$this->data = [
			"data" => $data
		];

		$this->query = "SELECT tab_entidade.nome, tab_venda.id_venda, tab_venda.id_entidade, tab_venda.mesa, tab_venda.frete, tab_venda.valor_servico, sum(round(IFNULL(tab_vendaitem.preco, 0) * IFNULL(tab_vendaitem.qtd, 0), 2)) as subtotal, sum(IFNULL(tab_vendaitem.desconto, 0)) as desconto
						FROM tab_venda
						LEFT JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda and tab_vendaitem.estornado = 0
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						WHERE tab_venda.data < :data AND tab_venda.id_vendastatus in (" . self::STATUS_MESA_EM_ANDAMENTO . "," . self::STATUS_MESA_EM_PAGAMENTO . ")
						GROUP BY tab_venda.id_venda, tab_entidade.nome, tab_venda.id_entidade";

		parent::Execute();
	}

	public function getReprints($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa,
		];

		$this->query = "SELECT tab_venda.id_venda, count(tab_vendastatuschange.id_vendastatus) AS prints
										FROM tab_venda INNER JOIN tab_vendapaycaixa ON tab_venda.id_venda = tab_vendapaycaixa.id_venda
										INNER JOIN tab_vendastatuschange ON tab_vendastatuschange.id_venda = tab_venda.id_venda
										WHERE tab_vendapaycaixa.id_caixa = :id_caixa AND tab_vendastatuschange.id_vendastatus = " . SaleOrder::STATUS_PEDIDO_IMPRESSO . "
										GROUP BY tab_venda.id_venda HAVING prints > 1";

		parent::Execute();
	}

	public function getCouponsByEntity($id_entidade, $page) {

		$this->data = [
			'id_entidade' => $id_entidade,
		];

		$linesPage = 5;
		$page *= $linesPage;

		$this->query = "SELECT tab_venda.*, c.nome as colaborador, sum(round(qtd*preco, 2)) AS subtotal, sum(desconto) as desconto, tab_vendastatus.vendastatus, tab_entidade.nome
						FROM tab_venda
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						INNER JOIN tab_vendastatus ON tab_vendastatus.id_vendastatus = tab_venda.id_vendastatus
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						INNER JOIN tab_entidade c on c.id_entidade = tab_venda.id_colaborador
						WHERE tab_venda.id_entidade = :id_entidade
						GROUP BY tab_venda.id_venda ORDER BY tab_venda.data desc limit $page, $linesPage";
//AND tab_venda.id_vendastatus in (" . self::STATUS_PEDIDO_PAGO . ", " . self::STATUS_VENDA_PAGA . ", " . self::STATUS_VENDA_PRAZO . ", " . self::STATUS_MESA_PAGA . ", " . self::STATUS_VENDA_PRAZO_PAGA . ")
		parent::Execute();
	}

	public function getSaleProductByDate($date) {

		$this->getSaleProductByDateInterval($date . " 00:00:00", $date . " 23:59:59");
	}

	public function getSaleProductByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart,
			'dateend' => $dateend
		];

		$this->query = "SELECT tab_vendaitem.id_produto, tab_produto.produto, tab_produto.ativo, tab_produtosetor.produtosetor, sum(round(tab_vendaitem.qtd * tab_vendaitem.preco, 2) - tab_vendaitem.desconto) as subtotal, sum(qtd) as qtd, tab_produtounidade.produtounidade
						FROM tab_vendaitem
						INNER JOIN tab_venda on tab_venda.id_venda = tab_vendaitem.id_venda
						INNER JOIN tab_produto on tab_produto.id_produto = tab_vendaitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
						WHERE tab_venda.data between :datestart and :dateend
							and tab_vendaitem.estornado = 0
							and tab_venda.id_vendastatus not in (" . self::STATUS_PEDIDO_CANCELADO . "," . self::STATUS_VENDA_CANCELADA . "," . self::STATUS_MESA_CANCELADA . "," . self::STATUS_MESA_TRANSFERIDA . ")
						GROUP BY tab_vendaitem.id_produto
						ORDER BY tab_produtosetor.produtosetor, tab_produto.id_produtounidade, sum(qtd) desc";

		parent::Execute();
	}

	public function SearchStockInOutByDate($date) {

		$this->SearchStockInOutByDateInterval($date, $date);
	}

	public function SearchStockInOutByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT tab_inout.*, tab_produto.produto, tab_produto.ativo, tab_produtounidade.produtounidade, tab_produtosetor.produtosetor
			FROM
				(
				SELECT id_produto, sum(compra_qtd) as compra_qtd, sum(compra_subtotal) as compra_subtotal, sum(venda_qtd) as venda_qtd, sum(venda_subtotal) as venda_subtotal
				FROM
				(
					(
					SELECT id_produto, 0 as compra_qtd, 0 as compra_subtotal, sum(qtd) as venda_qtd,
					sum(round(qtd*preco, 2) - desconto) as venda_subtotal
					FROM tab_venda
					INNER JOIN tab_vendaitem on tab_vendaitem.id_venda = tab_venda.id_venda
					WHERE tab_venda.data BETWEEN :datestart and :dateend
						AND tab_vendaitem.estornado = 0
						AND tab_venda.id_vendastatus in (" . self::STATUS_PEDIDO_EFETUADO . ", " . self::STATUS_PEDIDO_IMPRESSO . ", " . self::STATUS_PEDIDO_PAGO . ", " . self::STATUS_VENDA_PAGA . ", " . self::STATUS_VENDA_PRAZO . ", " . self::STATUS_MESA_PAGA . ", " . self::STATUS_VENDA_PRAZO_PAGA . ", " . self::STATUS_PEDIDO_PRODUCAO . ", " . self::STATUS_PEDIDO_ENTREGA . ", " . self::STATUS_PEDIDO_EM_ANDAMENTO . ")
					GROUP BY id_produto
					)
					UNION
					(
					SELECT id_produto, sum(round(tab_compraitem.vol * tab_compraitem.qtdvol, 2)) as compra_qtd,
					sum(round(tab_compraitem.vol * tab_compraitem.custo, 2)) as compra_subtotal, 0 as venda_qtd, 0 as venda_subtotal
					FROM tab_compra
					INNER JOIN tab_compraitem on tab_compraitem.id_compra = tab_compra.id_compra
					WHERE tab_compra.data BETWEEN :datestart and :dateend
						AND tab_compra.id_comprastatus = " . PurchaseOrder::COMPRA_STATUS_FINALIZADA . "
					GROUP BY tab_compraitem.id_produto
					)
				) as tab_union
				GROUP BY id_produto
				) as tab_inout
			INNER JOIN tab_produto on tab_produto.id_produto = tab_inout.id_produto
			INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
			INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			ORDER BY produtosetor, produto";

		parent::Execute();
	}

	public function SearchWaitertipByDate($date) {

		$this->SearchWaitertipByDateInterval($date, $date);
	}

	public function SearchWaitertipByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT *
						FROM tab_venda
						INNER JOIN tab_entidade ON tab_entidade.id_entidade = tab_venda.id_colaborador
						WHERE tab_venda.data between :datestart and :dateend
						AND tab_venda.id_vendastatus = " . self::STATUS_MESA_PAGA . "
						ORDER BY tab_entidade.nome";

		parent::Execute();
	}

	public function SearchCashBreakByDate($date) {

		$this->SearchCashBreakByDateInterval($date,$date);
	}

	public function SearchCashBreakByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT tab_caixa.id_caixa, tab_caixa.dataini, tab_entidade.nome, tab_entidade.id_entidade, tab_entidade.ativo, tab_caixa.trocoini, tab_caixa.trocofim, IFNULL(sum(tab_vendapay.valor), 0) as total
			FROM tab_caixa
			INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixa.id_entidade
			LEFT JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_caixa = tab_caixa.id_caixa
			LEFT JOIN tab_venda on tab_vendapaycaixa.id_venda = tab_venda.id_venda and tab_venda.id_vendastatus in (
				" . SaleOrder::STATUS_VENDA_PAGA . ",
				" . SaleOrder::STATUS_PEDIDO_PAGO . ",
				" . SaleOrder::STATUS_MESA_PAGA . ",
				" . SaleOrder::STATUS_VENDA_PRAZO_PAGA . "
			)
			LEFT JOIN tab_vendapay on tab_vendapay.id_venda = tab_venda.id_venda  and tab_vendapay.id_especie not in (2)
			WHERE tab_caixa.dataini between :datestart and :dateend
			GROUP BY tab_caixa.id_caixa
			ORDER BY tab_entidade.nome, tab_caixa.dataini";

		parent::Execute();
	}

	public function getTotalSalesOnCreditByEntity() {

		$this->data = [
			"id_vendastatus" => self::STATUS_VENDA_PRAZO
		];

		$this->query = "SELECT tab_venda.id_entidade, tab_entidade.nome, tab_entidade.ativo, tab_entidade.credito, tab_venda.frete, tab_venda.valor_servico,
						sum(round(qtd*preco, 2)) AS subtotal, sum(desconto) as desconto,
						(sum(round(qtd*preco, 2)) - sum(desconto)) AS total
						FROM tab_venda
						inner join tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						WHERE tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda
						ORDER BY tab_entidade.nome";

		parent::Execute();
	}

	public function getSalesOnCreditByEntity($id_entidade, $order_reverse = false) {

		$this->data = [
			"id_entidade" => $id_entidade,
			"id_vendastatus" => self::STATUS_VENDA_PRAZO
		];

		$this->query = "SELECT tab_venda.*, IFNULL(sum(round(qtd*preco,2)), 0) as subtotal, IFNULL(sum(desconto), 0) as desconto
						FROM tab_venda
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						WHERE tab_venda.id_entidade = :id_entidade and tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda";

		if ($order_reverse == true) {

			$this->query .= " ORDER BY tab_venda.data desc";
		}

		parent::Execute();
	}

	public function getSalesOnCreditByCashier($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa,
			"id_vendastatus" => self::STATUS_VENDA_PRAZO
		];

		$this->query = "SELECT tab_venda.*, c.nome as colaborador, IFNULL(sum(round(qtd*preco,2)), 0) as subtotal, IFNULL(sum(desconto), 0) as desconto, tab_entidade.nome
						FROM tab_venda
						LEFT JOIN tab_vendaitem ON tab_vendaitem.id_venda = tab_venda.id_venda AND tab_vendaitem.estornado = 0
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_venda.id_entidade
						INNER JOIN tab_entidade c on c.id_entidade = tab_venda.id_colaborador
						WHERE tab_venda.id_caixa = :id_caixa and tab_venda.id_vendastatus = :id_vendastatus
						GROUP BY tab_venda.id_venda";

		parent::Execute();
	}

	public function setCashierSalesOnCredit($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa
		];

		$this->query = "update tab_venda
						set id_caixa = :id_caixa
						WHERE id_vendastatus = " . self::STATUS_VENDA_PRAZO . " and id_caixa is null";

		return parent::Execute();
	}

	public function getSaleOff($id_venda) {

		$discount_percent = 0;
		$acumulative = true;

		$blackfriday = new BlackFriday();

		$blackfriday->isToday();

		if ($row = $blackfriday->getResult()) {

			$discount_percent = $row['desconto'];

			$acumulative = ($row['acumulativo'] == 0)? false: true;
		}

		$id_entidade = null;

		$this->ReadOnly($id_venda);

		if ($row = parent::getResult()) {

			$id_entidade = $row['id_entidade'];
		}

		if ($acumulative && $id_entidade) {

			$fidelity = new FidelityProgram();

			$fidelity->getDays();

			$row = $fidelity->getResult();

			$last_days = $row['dias_compra'];

			$fidelity->getList();

			if ($row = $fidelity->getResult()) {

				do {

					$rules[] = [
						$row['condicao'],
						$row['valor'],
						$row['desconto'],
					];

				} while ($row = $fidelity->getResult());

				$this->getTotalPaymentSales($id_entidade, $last_days);

				$row = parent::getResult();

				$total_sale = 0;

				if ($row['total']) {

					$total_sale = $row['total'];
				}

				$rule_applied = false;

				foreach ($rules as $rule) {

					switch ($rule[0]) {

						case FidelityProgram::RULE_EQUAL:

							if ($total_sale == $rule[1]) {

								$discount_percent += $rule[2];
								$rule_applied = true;
							}

						break;

						case FidelityProgram::RULE_LESS_THAN:

							if ($total_sale < $rule[1]) {

								$discount_percent += $rule[2];
								$rule_applied = true;
							}

						break;

						case FidelityProgram::RULE_LESS_EQUAL:

							if ($total_sale <= $rule[1]) {

								$discount_percent += $rule[2];
								$rule_applied = true;
							}

						break;

						case FidelityProgram::RULE_GREATER_THAN:

							if ($total_sale > $rule[1]) {

								$discount_percent += $rule[2];
								$rule_applied = true;
							}

						break;

						case FidelityProgram::RULE_GREATER_EQUAL:

							if ($total_sale >= $rule[1]) {

								$discount_percent += $rule[2];
								$rule_applied = true;
							}

						break;
					}

					if ($rule_applied == true) {

						break;
					}
				}
			}
		}

		return $discount_percent;
	}

	public function applyFidelityProgram($id_venda) {

		$discount_value = 0;

		$discount_percent = $this->getSaleOff($id_venda);

		$saleItem = new SaleOrderItem();

		if ($discount_percent > 0) {

			$saleItem->getTotal($id_venda);

			$row = $saleItem->getResult();

			$total_sale = $row['total'];

			if ($total_sale == 0) {

				// echo Error ("Não há valor para aplicar desconto no pedido.");
				return $discount_value;
			}

			$discount_value = round($total_sale * ($discount_percent * 0.01), 2);

			//Arredonda o desconto para remover os centavos menor que 5
			// $discount_value += ((round(($total_sale - $discount_value) * 100,0) % 5) / 100);

			$discount = $discount_value;

			$discount_percent = $discount_value / $total_sale;

			$saleItem->getListActiveItems($id_venda);

			// $row_count = $saleItem->rowCount();

			$discount_item = 0;

			while ($row = $saleItem->getResult()) {

				$discount_item = round(round($row['qtd'] * $row['preco'], 2) * $discount_percent, 2);

				$discount = round($discount - $discount_item, 2);

				$items[] = [
					"id_vendaitem" => $row['id_vendaitem'],
					"desconto" => $discount_item,
				];
			}

			if ($discount != 0) {

				$last_item = count($items) - 1;

				$items[$last_item]["desconto"] = $items[$last_item]["desconto"] + $discount;
			}

			foreach ($items as $item) {

				$saleItem->Update([
					"id_venda" => $id_venda,
					"id_vendaitem" => $item['id_vendaitem'],
					"field" => "desconto",
					"value" => $item['desconto'],
				]);
			}
		} else {

			$saleItem->DiscountClear($id_venda);
		}

		return $discount_value;
	}

	public function applyFreight($id_venda) {

		$valor_frete = 0;

		$saleOrderAddress = new SaleOrderAddress();

		$saleOrderAddress->Read($id_venda);

		if ($row = $saleOrderAddress->getResult()) {

			if ($row["cep"] == "") {

				Notifier::Add("Endereço sem CEP! Não foi possível verificar valor do frete.", Notifier::NOTIFIER_ALERT);

			} else {

				$cep = intval($row["cep"]);

				$freight = new Freight();

				$freight->Read();

				if ($rowfreight = $freight->getResult()) {

					$this->Read($id_venda);

					if ($row = $this->getResult()) {

						$subtotal = Calc::Sum([
							$row["subtotal"],
							-$row["desconto"]
						]);

						// Notifier::Add("Total $subtotal", Notifier::NOTIFIER_INFO);

						if ($rowfreight["fretegratis"] == 1 && $subtotal >= $rowfreight["fretegratis_valor"] ) {

							// Notifier::Add("Frete grátis!", Notifier::NOTIFIER_INFO);

						} else {

							$freightCep = new FreightCEP();

							$freightCep->getCEPValue($cep);

							if ($rowCep = $freightCep->getResult()) {

								$valor_frete = $rowCep["valor"];

								// Notifier::Add("Frete calculado R$ $valor_frete.", Notifier::NOTIFIER_INFO);

							} else {

								Notifier::Add("CEP não localizado para cálculo do frete!", Notifier::NOTIFIER_ALERT);
							}
						}
					}

				} else {

					Notifier::Add("Erro ao ler dados de frete.", Notifier::NOTIFIER_ERROR);
				}
			}

		} else {

			// Notifier::Add("Cliente retira.", Notifier::NOTIFIER_INFO);
		}

		$this->Update($id_venda, "frete", $valor_frete);

		return $valor_frete;
	}

	public function applyServiceValue($id_venda) {

		$this->Read($id_venda);

		if ($row = $this->getResult()) {

			switch ($row['id_vendastatus']) {

				case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
				case SaleOrder::STATUS_MESA_CANCELADA:
				case SaleOrder::STATUS_MESA_EM_PAGAMENTO:
				case SaleOrder::STATUS_MESA_PAGA:
				case SaleOrder::STATUS_MESA_TRANSFERIDA:

					$config = new Config();

					$config->Read();

					if ($rowConfig = $config->getResult()) {

						if ($rowConfig['taxa_servico'] > 0) {

							$valor_servico = $row['subtotal'] * $rowConfig['taxa_servico'] * 0.01;

							$this->Update($id_venda, "valor_servico", round($valor_servico, 2));
						}
					}
				break;
			}
		}
	}

	public function getTotalCouponsByCashier($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "SELECT IFNULL(count(id_venda), 0) as total
						FROM tab_vendapaycaixa
						WHERE id_caixa = :id_caixa";

		parent::Execute();

	}

	public function getTotalServiceByCashier($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "SELECT tab_entidade.nome, IFNULL(sum(valor_servico), 0) as total
						FROM tab_venda
						INNER JOIN tab_vendapaycaixa on tab_vendapaycaixa.id_venda = tab_venda.id_venda
						INNER JOIN tab_entidade ON tab_entidade.id_entidade = tab_venda.id_colaborador
						WHERE tab_vendapaycaixa.id_caixa = :id_caixa
						GROUP BY tab_venda.id_colaborador";

		parent::Execute();
	}

	public static function LoadSaleOrderPayment($rowSale, $only_payment_details = false) {

		$tplSale = new View('templates/sale_order');

		$paymentKind = new PaymentKind();

		$paymentKind->getList();

		$extra_block_especie = "";

		while ($rowPaymentKind = $paymentKind->getResult()) {

			if ($rowPaymentKind['ativo'] == 1) {

				$extra_block_especie .= $tplSale->getContent($rowPaymentKind, "EXTRA_BLOCK_ESPECIE");
			}
		}

		$salePayment = new SaleOrderPay();

		$salePayment->getList($rowSale['id_venda']);

		$ret = "";

		$extra_block_saleorder_payment = "";

		$totalPayment = 0;

		while ($rowPayment = $salePayment->getResult()) {

			$totalPayment += $rowPayment['valor_recebido'];

			$rowPayment['extra_block_saleorder_bt_payment_del'] = "";
			$rowPayment['id_entidade'] = $rowSale['id_entidade'];

			if ($rowSale['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

				$rowPayment['extra_block_saleorder_bt_payment_del'] = $tplSale->getContent($rowPayment, "EXTRA_BLOCK_SALEORDER_BT_PAYMENT_DEL");
			}

			$rowPayment['valorrecebido_formatted'] = number_format($rowPayment['valor_recebido'], 2, ",", ".");

			$extra_block_saleorder_payment .= $tplSale->getContent($rowPayment, "EXTRA_BLOCK_SALEORDER_PAYMENT");
		}

		$total = Calc::Sum([
			$rowSale['subtotal'],
			- $rowSale['desconto'],
			$rowSale['valor_servico'],
			$rowSale['frete']
		]);

		$ret .= $tplSale->getContent(["total_formatted" => number_format($total, 2, ",", ".")], "EXTRA_BLOCK_TOTAL");

		$subtotal = Calc::Sum([
			$total,
			- $totalPayment
		]);

		if ($subtotal < 0) {

			$troco_formatted = number_format(-$subtotal, 2, ",", ".");

			$extra_block_saleorder_payment .= $tplSale->getContent(["troco_formatted" => $troco_formatted], "EXTRA_BLOCK_SALEORDER_PAYMENT_TROCO");
		}

		$ret .= $extra_block_saleorder_payment;

		$extra_block_saleorder_payment_close_button = "";

		if ($rowSale['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

			if ($subtotal > 0) {

				$subtotal_formatted = number_format($subtotal, 2, ",", ".");

				// $ret .= $tplSale->getContent(["subtotal_formatted" => $subtotal_formatted], "EXTRA_BLOCK_SALEORDER_PAYMENT_SUBTOTAL");

				$data = [
					"subtotal_formatted" => $subtotal_formatted,
					'credito_formatted' => "0,00",
					'id_venda' => $rowSale['id_venda'],
					'id_entidade' => $rowSale['id_entidade'],
					'extra_block_especie' => $extra_block_especie,
					"block_entity_credit" => ""
				];

				if (!is_null($rowSale["id_entidade"])){

					$entity = new Entity();

					$entity->Read($rowSale['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$data['credito_formatted'] = number_format($rowEntity['credito'],2,",",".");
					}

					$tplEntity= new View("templates/entity");

					$data["block_entity_credit"] = $tplEntity->getContent($data, "BLOCK_ENTITY_CREDIT");
				}

				$ret .= $tplSale->getContent($data, "EXTRA_BLOCK_SALEORDER_PAYMENT_FORM");

			} else {

				$extra_block_saleorder_payment_close_button = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE_BUTTON");
			}
		}

		if ($subtotal <= 0) {

			$data = [
				"id_venda" => $rowSale['id_venda'],
				"extra_block_saleorder_payment_close_button" => $extra_block_saleorder_payment_close_button
			];

			$ret .= $tplSale->getContent($data, "EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE");
		}

		if ($only_payment_details == true) {

			$ret = $extra_block_saleorder_payment;

		} else {

			$data = [
				"extra_block_popup_saleorder_payment" => $ret
			];

			$ret = $tplSale->getContent($data, "EXTRA_BLOCK_POPUP_SALEORDER_PAYMENT");
		}

		return $ret;
	}

	public static function SaleOrderExpand($rowSale, $editable) {

		$rowSale = self::FormatFields($rowSale);
		SaleOrder::getMenu($rowSale, false);

		$tplSale = new View("templates/sale_order");

		$saleItem = new SaleOrderItem();

		$saleItem->getList($rowSale['id_venda']);

		$tr = "";

		if ($rowItem = $saleItem->getResult()) {

			do {

				$rowItem = Product::FormatFields($rowItem);

				$rowItem = SaleOrderItem::FormatFields($rowItem);

				if ($editable && $rowSale['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO) {

					if ($rowItem['estornado'] == 0) {

						$tr.= $tplSale->getContent($rowItem, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS");

					} else {

						$tr.= $tplSale->getContent($rowItem, "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_REVERSED");
					}

				} else {

					if ($rowItem['estornado'] == 0) {

						$tr.= $tplSale->getContent($rowItem, "EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS");

					} else {

						$tr.= $tplSale->getContent($rowItem, "EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS_REVERSED");
					}
				}
			}  while ($rowItem = $saleItem->getResult());

		} else {

			$tr = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_NONE");

		}

		$rowSale['extra_block_saleaddress'] = SaleOrderAddress::getSaleAddress($rowSale['id_venda']);

		$rowSale["extra_block_tab_content_tr"] = $tr;

		// $rowSale['extra_block_saleorder_payment'] = self::LoadSaleOrderPayment($rowSale);
		$rowSale["block_entity_credit"] = "";

		$entity = new Entity();

		if (is_null($rowSale['id_entidade'])) {

			$rowSale['nome'] = "Varejo";
			$rowSale['extra_block_entity_button_status'] = "";
			$rowSale['credito_formatted'] = "";

		} else {

			$entity->Read($rowSale['id_entidade']);

			if ($rowEntity = $entity->getResult()) {

				$rowEntity = Entity::FormatFields($rowEntity);

				$rowSale['nome'] = $rowEntity['nome'];
				$rowSale['extra_block_entity_button_status'] = $rowEntity['extra_block_entity_button_status'];
				$rowSale['credito_formatted'] = $rowEntity['credito_formatted'];
			}

			$tplEntity = new View("templates/entity");

			$rowSale["block_entity_credit"] = $tplEntity->getContent($rowSale, "BLOCK_ENTITY_CREDIT");
		}

		$entity->Read($rowSale['id_colaborador']);

		if ($rowEntity = $entity->getResult()) {

			$rowSale['colaborador'] = $rowEntity['nome'];
		}

		$ret = "";

		switch($rowSale['id_vendastatus']) {

			case SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO:

				$tplProduct = new View('templates/product');

				$rowSale["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_EDIT");

				break;

			case SaleOrder::STATUS_PEDIDO_EFETUADO:

				$rowSale['extra_block_saleorder_payment'] = SaleOrder::LoadSaleOrderPayment($rowSale, true);

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_PEDIDO_PRODUCAO:

				$rowSale['extra_block_saleorder_payment'] = SaleOrder::LoadSaleOrderPayment($rowSale, true);

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_PEDIDO_ENTREGA:

				$rowSale['extra_block_saleorder_payment'] = SaleOrder::LoadSaleOrderPayment($rowSale, true);

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_VENDA_PRAZO:

				$rowSale['extra_block_saleorder_payment'] = "Não definido.";

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_PEDIDO_PAGO:
			case SaleOrder::STATUS_VENDA_PAGA:
			case SaleOrder::STATUS_MESA_PAGA:
			case SaleOrder::STATUS_VENDA_PRAZO_PAGA:

				$rowSale['extra_block_saleorder_payment'] = SaleOrder::LoadSaleOrderPayment($rowSale, true);

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_PEDIDO_CANCELADO:
			case SaleOrder::STATUS_VENDA_CANCELADA:
			case SaleOrder::STATUS_MESA_CANCELADA:
			case SaleOrder::STATUS_MESA_TRANSFERIDA:

				$rowSale['extra_block_saleorder_payment'] = SaleOrder::LoadSaleOrderPayment($rowSale, true);

				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
			case SaleOrder::STATUS_MESA_EM_PAGAMENTO:
			case SaleOrder::STATUS_VENDA_EM_ANDAMENTO:

				$rowSale['extra_block_saleorder_payment'] = "Não definido.";
				$ret = $tplSale->getContent($rowSale, "EXTRA_BLOCK_SALEORDER_SHOW");

				break;

			default:

				$ret = "TODO:";
				break;
		}

		return $ret;
	}

	public function setCashierReversedSales($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "UPDATE tab_venda
						SET id_caixa = :id_caixa
						WHERE id_vendastatus IN (" . self::STATUS_MESA_CANCELADA . "," . self::STATUS_PEDIDO_CANCELADO . "," . self::STATUS_VENDA_CANCELADA . "," . self::STATUS_MESA_TRANSFERIDA . ")
						AND id_caixa is null";

		return parent::Execute();
	}

	public function getReversedSales($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		// STATUS_MESA_TRANSFERIDA - Não é necessário colocar nessa consulta.

		$this->query = "SELECT *
						FROM tab_venda
						WHERE tab_venda.id_caixa = :id_caixa
						AND tab_venda.id_vendastatus IN (" . self::STATUS_MESA_CANCELADA . "," . self::STATUS_PEDIDO_CANCELADO . "," . self::STATUS_VENDA_CANCELADA . ")";

		parent::Execute();
	}

	public function getReversedItems($id_caixa) {

		$this->data = [
			'id_caixa' => $id_caixa,
		];

		$this->query = "SELECT tab_vendaitem.*, tab_produto.produto, tab_produtounidade.produtounidade FROM tab_vendaitem
						INNER JOIN tab_produto on tab_produto.id_produto = tab_vendaitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_venda in (
							SELECT tab_venda.id_venda FROM tab_vendapaycaixa
							INNER JOIN tab_venda ON tab_venda.id_venda = tab_vendapaycaixa.id_venda
							WHERE tab_vendapaycaixa.id_caixa = :id_caixa
							AND tab_venda.id_vendastatus IN (" . self::STATUS_MESA_PAGA . "," . self::STATUS_PEDIDO_PAGO . "," . self::STATUS_VENDA_PAGA . "," . self::STATUS_VENDA_PRAZO_PAGA . "))
						AND estornado = 1";

		parent::Execute();
	}

	public function CheckVersion($id_venda, $versao) {

		$this->data = [
			"id_venda" => $id_venda,
			"versao" => $versao,
		];

		$this->query = "UPDATE tab_venda
						SET versao = versao + 1
						WHERE id_venda = :id_venda and versao = :versao";

		parent::Execute();

		if (parent::rowCount() > 0) {

			$this->ReadOnly($id_venda);

			if ($row = parent::getResult()) {

				return $row["versao"];
			}
		}

		Notifier::Add("Não foi possível concluir a operação. Pedido sendo alterado por outro usuário!", Notifier::NOTIFIER_ERROR);
		return null;
	}

	public static function DoPrint($id_venda, $id_impressora, $bell = true) {

		$printing = new Printing($id_impressora);

		if (!$printing->initialize()) {

			return false;
		}

		$company = new Company();

		$company->Read();

		$row = $company->getResult();

		$row = Company::FormatFields($row);

		$empresa = strtoupper($row['empresa']);

		$cnpj = "";

		if (!empty($row['cnpj'])) {

			$cnpj = "CNPJ " . $row['cnpj_formatted'];
		}

		$telefone = "";

		if (!empty($row['telefone']) || !empty($row['celular'])) {

			if (!empty($row['telefone'])) {

				$telefone = $row['telefone_formatted'];

				if (!empty($row['celular'])) {

					$telefone .= " / ";
				}
			}

			if (!empty($row['celular'])) {

				$telefone .= $row['celular_formatted'];
			}
		}

		$endereco = strtoupper($row['rua']);
		$bairro = strtoupper($row['bairro']);
		$rodape_1 = strtoupper($row['cupomlinha1']);
		$rodape_2 = strtoupper($row['cupomlinha2']);

		$sale = new SaleOrder();

		$sale->Read($id_venda);

		if ($row = $sale->getResult()) {

			$id_venda = $row['id_venda'];
			$coupon_obs = $row['obs'];

			if (!empty($empresa)) {

				$printing->textCenter($empresa);
			}

			if (!empty($cnpj)) {

				$printing->textCenter($cnpj);
			}

			if (!empty($endereco)) {

				$printing->textCenter($endereco);
			}

			if (!empty($bairro)) {

				$printing->textCenter($bairro);
			}

			if (!empty($empresa)) {

				$printing->textCenter($telefone);
			}

			$printing->linedashspaced();

			if ($row['id_caixa'] == null) {

				$printing->text("Data: " . date_format(date_create($row['data']), "d/m/Y H:i"));

			} else {

				$cashier = new Cashier();

				$cashier->Read($row['id_caixa']);

				if ($rowCashier = $cashier->getResult()) {

					$printing->textSpaceBetween("Data: " . date_format(date_create($row['data']), "d/m/Y H:i"), "PDV: " . $rowCashier['descricao']);
				}
			}

			if (empty($row['mesa'])) {

				$printing->text("Cupom: " . $row['id_venda']);

			} else {

				$printing->textSpaceBetween("Cupom: " . $row['id_venda'], "Mesa: " . $row['mesa']);
			}

			$entity = new Entity();

			$entity->Read($row['id_colaborador']);

			if ($rowEntity = $entity->getResult()) {

				// $printing->text("Operador: " . substr($rowEntity['nome'], 0, 30));
				$printing->textTruncate("Op.: " . $rowEntity['nome']);
			}

			if ($row['id_entidade'] == null) {

				$row['nome'] = "Varejo";

			} else {

				$entity->Read($row['id_entidade']);

				$rowEntity = $entity->getResult();

				$rowEntity = Entity::FormatFields($rowEntity);

				$row['nome'] = $rowEntity['nome'];
			}

			// $printing->text(substr("Cliente: " . $row['nome'], 0, 40));
			$printing->textTruncate("Cliente: " . $row['nome']);

			$delivery = false;

			if (empty($row['mesa'])) {

				$endereco = "Endereço: Cliente retira na loja.";

				$saleAddress = new SaleOrderAddress();

				$saleAddress->Read($id_venda);

				if ($row2 = $saleAddress->getResult()) {

					$delivery = true;

					$row2 = EntityAddress::FormatFields($row2);

					$endereco = "Endereço: " . $row2['endereco'];

					// if (!empty($row2['obs'])) {

					// 	$endereco_obs = "Obs.: " . $row2['obs'];
					// }
				}

				if ($delivery || $row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_EFETUADO || $row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_ENTREGA || $row['id_vendastatus'] == SaleOrder::STATUS_PEDIDO_PRODUCAO) {

					$printing->text($endereco);
				}

				// if (!empty($endereco_obs)) {

				// 	$printing->text($endereco_obs);
				// }
			}

			if ($row['id_entidade'] != null) {

				if(!empty($rowEntity['telcelular'])) {

					$printing->text("Cel.: " . $rowEntity['telcelular_formatted']);

				} else if(!empty($rowEntity['telresidencial'])) {

					$printing->text("Tel.: " . $rowEntity['telresidencial_formatted']);

				} else if(!empty($rowEntity['telcomercial'])) {

					$printing->text("Tel.: " . $rowEntity['telcomercial_formatted']);
				}
			}

			$printing->linedashspaced();
			$printing->textCenter("SEM VALOR FISCAL");

			$saleStatus = new SaleOrderStatusChange();

			if ($saleStatus->countPrints($id_venda) > 0) {

				$printing->textCenter("REIMPRESSAO");
			}

			$printing->linedashspaced();

			$printing->text("Itens");
			$printing->line(1);

			$sale_item = new SaleOrderItem();

			$sale_item->getList($row['id_venda']);

			$coupon_frete = $row['frete'];
			$coupon_servico = $row['valor_servico'];
			$coupon_total = 0;

			$desconto = 0;
			$first_line = true;

			$itens = 0;

			while ($rowItem = $sale_item->getResult()) {

				if ($rowItem['estornado'] == 0) {

					if (!$first_line) {

						$printing->line(1);
					}

					$itens++;

					// $printing->textTruncate("[" . str_pad($itens, 3, "0", STR_PAD_LEFT) . "] " . strtoupper($rowItem['produto']));
					$printing->textTruncate(strtoupper($rowItem['produto']));

					$linha = number_format($rowItem['qtd'],3, ",", ".") . " " . $rowItem['produtounidade'] . " x R$ " . number_format($rowItem['preco'], 2, ",", ".");

					$total=number_format(round($rowItem['qtd'] * $rowItem['preco'],2), 2, ",", ".");

					$coupon_total += round($rowItem['qtd'] * $rowItem['preco'],2);

					if ($rowItem['desconto'] > 0) {

						$desconto += $rowItem['desconto'];
					}

					// $space = mb_strlen($linha) + 5 + mb_strlen($total);

					// $linha = $linha . str_repeat(" ", 40 - $space) . "= R$ " . $total;

					// $printing->text($linha);
					$printing->textSpaceBetween($linha, "= R$ " . $total);

					if (empty($rowItem['obs']) == false) {

						$obs = "Obs.: " . $rowItem['obs'];

						$printing->text($obs);
					}

					$first_line = false;
				}
			}

			$printing->linedashspaced();

			if ($desconto > 0 || $coupon_servico > 0 || $coupon_frete > 0 || $delivery) {

				$printing->textSpaceBetween("Subtotal:", "R$ " . number_format($coupon_total, 2, ",", "."));
			}

			if ($desconto > 0) {

				$linha = "R$ " . number_format($desconto, 2, ",", ".");
				// $printing->text("Desconto [Fidelidade]:" . str_repeat(" ", 18 - mb_strlen($linha)) . $linha);
				$printing->textSpaceBetween("Desconto [Fidelidade]:", $linha);

				$coupon_total = Calc::Sum([
					$coupon_total,
					-$desconto
				]);
			}

			if ($coupon_servico > 0) {

				$linha = "R$ " . number_format($coupon_servico, 2, ",", ".");
				// $printing->text("Serviço:" . str_repeat(" ", 32 - mb_strlen($linha)) . $linha);
				$printing->textSpaceBetween("Serviço:", $linha);

				$coupon_total = Calc::Sum([
					$coupon_total,
					$coupon_servico
				]);
			}

			if ($coupon_frete > 0) {

				$linha = "R$ " . number_format($coupon_frete, 2, ",", ".");
				$printing->textSpaceBetween("Frete:", $linha);

				$coupon_total = Calc::Sum([
					$coupon_total,
					$coupon_frete
				]);

			} else if ($delivery) {

				$printing->textSpaceBetween("Frete:", "Grátis");
			}

			$linha = "R$ " . number_format($coupon_total, 2, ",", ".");

			$printing->textSpaceBetween("Total:", $linha);

			$salePay = new SaleOrderPay();

			$salePay->getList($id_venda);

			if ($rowPay = $salePay->getResult()) {

				$printing->line(1);

				$printing->text("Forma de Pagamento:");

				$total_recebido = 0;

				do {

					$printing->textSpaceBetween($rowPay['especie'], "R$ " . number_format($rowPay['valor_recebido'], 2, ",", "."));

					$total_recebido = Calc::Sum([
						$total_recebido,
						$rowPay['valor_recebido']
					]);

				} while ($rowPay = $salePay->getResult());

				$troco = Calc::Sum([
					$total_recebido,
					-$coupon_total
				]);

				if ($troco > 0) {

					$printing->textSpaceBetween("Troco", "R$ " . number_format($troco, 2, ",", "."));
				}
			}

			if (!empty($coupon_obs)) {

				$printing->line(1);

				$obs = "Obs.: " . $coupon_obs;

				$printing->text($obs);
			}

			$printing->linedashspaced();

			if (!empty($rodape_1)) {

				$printing->textCenter($rodape_1);
			}

			if (!empty($rodape_2)) {

				$printing->textCenter($rodape_2);
			}

			$printing->close();

			if ($bell == true) {

				$sound = new Sound();

				$sound->Play(1);
			}
			// if ($bell == true && !OS::isWindows()) {

			// 	$bell_file = realpath("./assets/sound") . "/bell.mp3";

			// 	shell_exec("mplayer -volume 75 $bell_file >/dev/null 2>&1 ");
				//para o mplayer conseguir executar o audio precisa: sudo adduser www-data audio
			// }

			$sale->AddStatus($id_venda, SaleOrder::STATUS_PEDIDO_IMPRESSO);

			return (sizeof(Notifier::getMessages()) == 0);
		}

		return false;
	}

	public static function getMenu(&$row, $menu_view = false) {

		$menu_close = false;
		$menu_production = false;
		$menu_delivery = false;
		$menu_reopen = false;
		$menu_print = false;
		$menu_reverse = false;
		$menu_prazo = false;
		$menu_whats = false;
		$menu_copypaste = false;
		$menu_statushistory = true;

		switch ($row['id_vendastatus']) {

			case SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO:

				$menu_close = true;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = false;
				$menu_print = false;
				$menu_reverse = true;
				$menu_prazo = true;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_PEDIDO_EFETUADO:
			case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED

				$menu_close = false;
				$menu_production = true;
				$menu_delivery = true;
				$menu_reopen = true;
				$menu_print = true;
				$menu_reverse = true;
				$menu_prazo = true;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_PEDIDO_PRODUCAO:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = true;
				$menu_reopen = true;
				$menu_print = true;
				$menu_reverse = true;
				$menu_prazo = true;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_PEDIDO_ENTREGA:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = true;
				$menu_print = true;
				$menu_reverse = true;
				$menu_prazo = true;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_VENDA_PRAZO:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = true;
				$menu_print = false;
				$menu_reverse = true;
				$menu_prazo = false;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_PEDIDO_PAGO:
			case SaleOrder::STATUS_VENDA_PAGA:
			case SaleOrder::STATUS_VENDA_PRAZO_PAGA:
			case SaleOrder::STATUS_MESA_PAGA:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = false;
				$menu_print = true;
				$menu_reverse = true;
				$menu_prazo = false;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_VENDA_CANCELADA:
			case SaleOrder::STATUS_MESA_CANCELADA:
			case SaleOrder::STATUS_PEDIDO_CANCELADO:
			case SaleOrder::STATUS_MESA_TRANSFERIDA:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = false;
				$menu_print = false;
				$menu_reverse = false;
				$menu_prazo = false;
				$menu_whats = true;
				$menu_copypaste = true;
				break;

			case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
			case SaleOrder::STATUS_MESA_EM_PAGAMENTO:
			case SaleOrder::STATUS_VENDA_EM_ANDAMENTO:

				$menu_close = false;
				$menu_production = false;
				$menu_delivery = false;
				$menu_reopen = false;
				$menu_print = false;
				$menu_reverse = false;
				$menu_prazo = false;
				$menu_whats = false;
				$menu_copypaste = false;
				break;

		}

		$tplSaleOrder = new View("templates/sale_order");

		$menu = "";

		if ($menu_view) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_VIEW");
		}

		if ($menu_close) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_CLOSE");
		}

		if ($menu_production) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_PRODUCTION");
		}

		if ($menu_delivery) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_DELIVERY");
		}

		if ($menu_reopen) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_REOPEN");
		}

		if (!is_null($row["id_entidade"])) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_ENTITY");
		}

		if ($menu_print) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_PRINT");
		}

		if ($menu_reverse) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_REVERSE");
		}

		if ($menu_statushistory) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_STATUSHISTORY");
		}

		if ($menu_prazo) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_PRAZO");
		}

		if ($menu_whats) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_WHATS");
		}

		if ($menu_copypaste) {

			$menu .= $tplSaleOrder->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_COPYPASTE");
		}

		$row['extra_block_saleorder_menu'] = $tplSaleOrder->getContent(["extra_block_saleorder_menulist" => $menu], "EXTRA_BLOCK_SALEORDER_MENU");
	}

	public static function FormatFields($row) {

		// if (is_null($row['id_entidade'])) {

		// 	$row['id_entidade'] = 0;
		// }

		if (array_key_exists('credito', $row)) {

			$row['credito_formatted'] = number_format($row['credito'], 2, ",", ".");
		}

		if (!key_exists("subtotal", $row) || $row['subtotal'] == null) {

			$row['subtotal'] = 0;
		}

		if (!key_exists("desconto", $row) || $row['desconto'] == null) {

			$row['desconto'] = 0;
		}

		if ($row['id_vendastatus'] != self::STATUS_PEDIDO_CANCELADO && $row['id_vendastatus'] != self::STATUS_VENDA_CANCELADA && $row['id_vendastatus'] != self::STATUS_MESA_CANCELADA && $row['id_vendastatus'] != self::STATUS_MESA_TRANSFERIDA) {

			$row['reversed'] = "";

		} else {

			$row['reversed'] = "reversed";
		}

		$row['subtotal_formatted'] = number_format($row['subtotal'],2,",",".");
		$row['frete_formatted'] = number_format($row['frete'],2,",",".");
		$row['valor_servico_formatted'] = number_format($row['valor_servico'],2,",",".");
		$row['desconto_formatted'] = number_format($row['desconto'],2,",",".");
		$row['total'] = $row['subtotal'] + $row['frete'] + $row['valor_servico'] - $row['desconto'];
		$row['total_formatted'] = number_format($row['total'], 2, ",", ".");

		$row['data_formatted'] = date_format(date_create($row['data']),'d/m/Y H:i');
		$row['dataonly_formatted'] = date_format(date_create($row['data']),'d/m/Y');

		$tplSale = new View('templates/sale_order');

		if ($row['obs'] == "") {

			$row['extra_block_saleorder_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_OBS_EMPTY");

		} else {

			$row['extra_block_saleorder_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_OBS");
		}

		switch ($row['id_vendastatus']) {

			case SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ANDAMENTO");
				break;

			case SaleOrder::STATUS_PEDIDO_EFETUADO:
			case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_EFETUADO");
				break;

			case SaleOrder::STATUS_PEDIDO_PRODUCAO:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRODUCAO");
				break;

			case SaleOrder::STATUS_PEDIDO_ENTREGA:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ENTREGA");
				break;

			case SaleOrder::STATUS_VENDA_PRAZO:

				// $row['extra_block_saleorder_menu'] = $tplSale->getContent($row, "EXTRA_BLOCK_SALEORDER_MENU_PRAZO");
				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRAZO");
				break;

			case SaleOrder::STATUS_PEDIDO_PAGO:
			case SaleOrder::STATUS_VENDA_PAGA:
			case SaleOrder::STATUS_VENDA_PRAZO_PAGA:
			case SaleOrder::STATUS_MESA_PAGA:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PAGO");
				break;

			case SaleOrder::STATUS_VENDA_CANCELADA:
			case SaleOrder::STATUS_MESA_CANCELADA:
			case SaleOrder::STATUS_PEDIDO_CANCELADO:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_CANCELADO");
				break;

			case SaleOrder::STATUS_MESA_TRANSFERIDA:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_TRANSFERIDO");
				break;

			case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
			case SaleOrder::STATUS_MESA_EM_PAGAMENTO:
			case SaleOrder::STATUS_VENDA_EM_ANDAMENTO:

				$row['extra_block_saleorder_show_ticket'] = $tplSale->getContent([], "EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ANDAMENTO");

				break;

		}

		$row['class_button'] = 'hidden';

		switch ($row['id_vendastatus']) {

			case self::STATUS_PEDIDO_EFETUADO:
			case self::STATUS_PEDIDO_IMPRESSO:
			case self::STATUS_PEDIDO_PAGO:
			case self::STATUS_VENDA_PAGA:
			case self::STATUS_MESA_PAGA:
			case self::STATUS_VENDA_PRAZO_PAGA:

				$row['class_button'] = '';
			break;
		}

		switch ($row['id_vendastatus']) {

			case self::STATUS_VENDA_PAGA:
			case self::STATUS_VENDA_CANCELADA:
			case self::STATUS_VENDA_EM_ANDAMENTO:

				$row["salelegend"] = "PDV";
				break;

			case self::STATUS_PEDIDO_EM_ANDAMENTO:
			case self::STATUS_PEDIDO_EFETUADO:
			case self::STATUS_PEDIDO_PAGO:
			case self::STATUS_PEDIDO_CANCELADO:
			case self::STATUS_PEDIDO_IMPRESSO:
			case self::STATUS_PEDIDO_PRODUCAO:
			case self::STATUS_PEDIDO_ENTREGA:
			case self::STATUS_VENDA_PRAZO_PAGA:
			case self::STATUS_VENDA_PRAZO:

				$row["salelegend"] = "Delivery";
				break;

			case self::STATUS_MESA_EM_ANDAMENTO:
			case self::STATUS_MESA_EM_PAGAMENTO:
			case self::STATUS_MESA_PAGA:
			case self::STATUS_MESA_CANCELADA:
			case self::STATUS_MESA_TRANSFERIDA:

				$row["salelegend"] = "Mesa";
				break;
		}

		// $row["salelegend"] = "Cupom";
		return $row;
	}
}