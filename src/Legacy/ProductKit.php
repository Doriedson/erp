<?php

namespace App\Legacy;

class ProductKit extends Connection {

    public function Read($id_kit, $id_produto) {

		$this->data = [
			"id_kit" => $id_kit,
			"id_produto" => $id_produto,
		];

		$this->query = "SELECT tab_produtokit.*, tab_produto.produto, tab_produtounidade.produtounidade, tab_produto.ativo FROM tab_produtokit
                        INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtokit.id_produto
                        INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
                        WHERE tab_produtokit.id_kit = :id_kit AND tab_produtokit.id_produto = :id_produto";

		parent::Execute();
	}

    public function getList($id_kit) {

		$this->data = ["id_kit" => $id_kit];

		$this->query = "SELECT tab_produtokit.*, tab_produto.produto, tab_produtounidade.produtounidade, tab_produto.ativo FROM tab_produtokit
                        INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtokit.id_produto
                        INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
                        WHERE tab_produtokit.id_kit = :id_kit";

		parent::Execute();
	}

	public function Create($id_kit, $id_produto, $qtd, $preco) {

		$this->data = [
			"id_kit" => $id_kit,
			"id_produto" => $id_produto,
			"qtd" => $qtd,
			"preco" => $preco,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produtokit
			            ($fields) VALUES ($places)";

		parent::Execute();

		return parent::rowCount();
	}

	public function Delete($id_kit, $id_produto) {

		$this->data = [
			"id_kit" => $id_kit,
			"id_produto" => $id_produto,
		];

		$this->query = "DELETE FROM tab_produtokit
						WHERE id_kit = :id_kit AND id_produto = :id_produto";

		parent::Execute();
		return parent::rowCount();
	}

	public function having($id_produto) {

		$this->data = ["id_produto" => $id_produto];

		$this->query = "SELECT * FROM tab_produtokit
			INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtokit.id_kit
			INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			WHERE tab_produtokit.id_produto = :id_produto";

		parent::Execute();
	}

	public function has($id_produto) {

		$this->data = [
			"id_produto" => $id_produto,
		];

		$this->query = "SELECT * FROM tab_produtokit
						WHERE id_produto = :id_produto";

		parent::Execute();
		return (parent::rowCount() > 0);
	}

	public function getTotal($id_kit) {

		$this->data = ["id_kit" => $id_kit];

		$this->query = "SELECT IFNULL(sum(round(preco * qtd, 2)), 0) as total FROM tab_produtokit
                        WHERE id_kit = :id_kit";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_kit" => $data['id_kit'],
			"id_produto" => $data['id_produto'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_produtokit
			SET $field = :value
			WHERE id_kit = :id_kit AND id_produto = :id_produto";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['qtd_formatted'] = number_format($row['qtd'],3,",",".");
		$row['item_kit_total_formatted'] = number_format(round($row['qtd'] * $row['preco'], 2), 2, ",", ".");

		return $row;
	}
}