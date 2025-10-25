<?php

namespace App\Legacy;

class PurchaseOrder extends Connection {

	const COMPRA_STATUS_ABERTA = 1;
	const COMPRA_STATUS_FINALIZADA = 2;
	const COMPRA_STATUS_CANCELADA = 3;

	public function Read($id_compra) {

		$this->data = [
			"id_compra" => $id_compra
		];

		$this->query = "SELECT c.*, f.*, cs.*, COALESCE(sum(round(ci.vol*ci.custo,2)), 0) as total, a.nome, c.obs as obs
						FROM tab_compra c
						INNER JOIN tab_entidade a on a.id_entidade = c.id_entidade
						INNER JOIN tab_fornecedor f on f.id_fornecedor = c.id_fornecedor
						INNER JOIN tab_comprastatus cs on cs.id_comprastatus = c.id_comprastatus
						LEFT JOIN tab_compraitem ci on ci.id_compra = c.id_compra
						WHERE c.id_compra = :id_compra
						ORDER BY c.data, c.id_compra";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT c.*, f.*, cs.*, COALESCE(sum(round(ci.vol*ci.custo,2)), 0) as total, a.nome, c.obs as obs
						FROM tab_compra c
						INNER JOIN tab_entidade a on a.id_entidade = c.id_entidade
						INNER JOIN tab_fornecedor f on f.id_fornecedor = c.id_fornecedor
						INNER JOIN tab_comprastatus cs on cs.id_comprastatus = c.id_comprastatus
						LEFT JOIN tab_compraitem ci on ci.id_compra = c.id_compra
						WHERE c.id_comprastatus = 1
						GROUP BY c.id_compra
						ORDER BY c.data, c.id_compra";

		parent::Execute();
	}

	public function getListStatus() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_comprastatus
						ORDER BY comprastatus";

		parent::Execute();
	}

	public function Create(array $data) {

		$this->data = $data;

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_compra ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function Delete($id_compra) {

		// $item = new PurchaseOrderItem;

		// $item->DeleteAll($id_compra);

		$this->data = [
			"id_compra" => $id_compra
		];

		// $this->query = "DELETE from tab_compra
		// 				WHERE id_compra = :id_compra";

		$this->query = "UPDATE tab_compra
						SET id_comprastatus = " . self::COMPRA_STATUS_CANCELADA . "
						WHERE id_compra = :id_compra";

		parent::Execute();
		return parent::rowCount();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_compra" => $data['id_compra'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_compra set $field = :value where id_compra = :id_compra";

		parent::Execute();
	}

	public function SearchByDate($id_comprastatus, $datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		if ($id_comprastatus == 0) {

			$this->query = "SELECT c.*, f.*, cs.*, COALESCE(sum(round(ci.vol*ci.custo,2)), 0) as total, a.nome, c.obs as obs
							FROM tab_compra c
							INNER JOIN tab_entidade a on a.id_entidade = c.id_entidade
							INNER JOIN tab_fornecedor f on f.id_fornecedor = c.id_fornecedor
							INNER JOIN tab_comprastatus cs on cs.id_comprastatus = c.id_comprastatus
							LEFT JOIN tab_compraitem ci on ci.id_compra = c.id_compra
							WHERE c.data BETWEEN :datestart AND :dateend
							GROUP BY c.id_compra
							ORDER BY c.data, c.id_compra";

		} else {

			$this->data['id_comprastatus'] = $id_comprastatus;

			$this->query = "SELECT c.*, f.*, cs.*, COALESCE(sum(round(ci.vol*ci.custo,2)), 0) as total, a.nome, c.obs as obs
							FROM tab_compra c
							INNER JOIN tab_entidade a on a.id_entidade = c.id_entidade
							INNER JOIN tab_fornecedor f on f.id_fornecedor = c.id_fornecedor
							INNER JOIN tab_comprastatus cs on cs.id_comprastatus = c.id_comprastatus
							LEFT JOIN tab_compraitem ci on ci.id_compra = c.id_compra
		 					WHERE c.data BETWEEN :datestart AND :dateend AND c.id_comprastatus = :id_comprastatus
							GROUP BY c.id_compra
		 					ORDER BY c.data, c.id_compra";
		}

		parent::Execute();
	}

	public function SearchStockInByDate($date) {

		$this->SearchStockInByDateInterval($date, $date);
	}

	public function SearchStockInByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT tab_compraitem.id_produto, tab_produto.produto, tab_produto.ativo, tab_produtounidade.produtounidade, tab_produtosetor.produtosetor,
						sum(round(tab_compraitem.vol * tab_compraitem.qtdvol, 2)) as qtd, sum(round(tab_compraitem.vol * tab_compraitem.custo, 2)) as subtotal
						FROM tab_compra
						INNER JOIN tab_compraitem on tab_compraitem.id_compra = tab_compra.id_compra
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compraitem.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						INNER JOIN tab_produtosetor on tab_produtosetor.id_produtosetor = tab_produto.id_produtosetor
						WHERE tab_compra.data BETWEEN :datestart and :dateend and tab_compra.id_comprastatus = " . $this::COMPRA_STATUS_FINALIZADA . "
						GROUP BY tab_compraitem.id_produto
						having qtd > 0
						ORDER BY tab_produtosetor.produtosetor, tab_produto.produto";

		parent::Execute();
	}

	public static function FormatCost($row) {

		if ($row["custo_unidade_ajustado"] > 0) {

			$row['preco_percent'] = round(($row['preco'] / $row["custo_unidade_ajustado"]) * 100 - 100, 2);
			$row['preco_promo_percent'] = round(($row['preco_promo'] / $row["custo_unidade_ajustado"]) * 100 - 100, 2);

		} else {

			$row['preco_percent'] = 0;
			$row['preco_promo_percent'] = 0;
		}

		$row['preco_percent'] = "(" . number_format($row['preco_percent'], 2, ",", ".") . "%)";
		$row['preco_promo_percent'] = "(" . number_format($row['preco_promo_percent'], 2, ",", ".") . "%)";

		$row["custo_unidade_formatted"] = number_format($row['custo_unidade'], 2, ",", ".");
		$row["custo_unidade_ajustado_formatted"] = number_format($row['custo_unidade_ajustado'], 2, ",", ".");

		$custo0 = Calc::Mult(
			Calc::Mult(
				$row["custo_unidade_ajustado"],
				Calc::Sum([$row["margem_lucro"], 100]),
				2
			),
			0.01
		);

		$row['custo0'] = number_format($custo0, 2, ",", ".");
		// $row['custo1'] = number_format(round($row['custo_unidade'] * 1.3, 2), 2, ",", ".");
		// $row['custo2'] = number_format(round($row['custo_unidade'] * 1.4, 2), 2, ",", ".");
		// $row['custo3'] = number_format(round($row['custo_unidade'] * 1.5, 2), 2, ",", ".");
		// $row['custo4'] = number_format(round($row['custo_unidade'] * 1.6, 2), 2, ",", ".");

		return $row;
	}

	public static function FormatFields($row) {

		$row['data'] = date_format( date_create($row['data']), 'Y-m-d');
		$row['data_formatted'] = date_format( date_create($row['data']), 'd/m/Y');

		$row['total_formatted'] = number_format($row['total'],2,',','.');

		$tplSale = new View('purchase_order');

		if ($row['obs'] == "") {

			$row['extra_block_purchaseorder_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_PURCHASEORDER_OBS_EMPTY");

		} else {

			$row['extra_block_purchaseorder_obs'] = $tplSale->getContent($row, "EXTRA_BLOCK_PURCHASEORDER_OBS");
		}

		return $row;
	}
}