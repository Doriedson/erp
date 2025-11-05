<?php

namespace App\Legacy;

class ProductComposition extends Connection {

	public function Create($id_composicao, $id_produto, $qtd) {

		$this->data = [
			"id_composicao" => $id_composicao,
			"id_produto" => $id_produto,
			"qtd" => $qtd,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produtocomposicao
			($fields) VALUES ($places)";

		parent::Execute();
		return parent::rowCount();
	}

	public function Read($id_composicao, $id_produto) {

		$this->data = [
			"id_composicao" => $id_composicao,
			"id_produto" => $id_produto,
		];

		$this->query = "SELECT * FROM tab_produtocomposicao
			INNER JOIN tab_produto
			ON tab_produto.id_produto = tab_produtocomposicao.id_produto
			INNER JOIN tab_produtounidade
			ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			WHERE tab_produtocomposicao.id_composicao = :id_composicao AND tab_produtocomposicao.id_produto = :id_produto";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_composicao" => $data['id_composicao'],
			"id_produto" => $data['id_produto'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_produtocomposicao
			SET $field = :value
			WHERE id_composicao = :id_composicao AND id_produto = :id_produto";

		parent::Execute();
	}

	public function Delete($id_composicao, $id_produto) {

		$this->data = [
			"id_composicao" => $id_composicao,
			"id_produto" => $id_produto,
		];

		$this->query = "DELETE FROM tab_produtocomposicao
			WHERE id_composicao = :id_composicao AND id_produto = :id_produto";

		parent::Execute();
		return parent::rowCount();
	}

	public function has($id_produto) {

		$this->data = ["id_produto" => $id_produto];

		$this->query = "SELECT * FROM tab_produtocomposicao
			WHERE id_produto = :id_produto";

		parent::Execute();
		return (parent::rowCount() > 0);
	}

	public function having($id_produto) {

		$this->data = ["id_produto" => $id_produto];

		$this->query = "SELECT * FROM tab_produtocomposicao
			INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtocomposicao.id_composicao
			INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			WHERE tab_produtocomposicao.id_produto = :id_produto";

		parent::Execute();
	}

	public function getList($id_composicao) {

		$this->data = ["id_composicao" => $id_composicao];

		$this->query = "SELECT * FROM tab_produtocomposicao
			INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtocomposicao.id_produto
			INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
			WHERE tab_produtocomposicao.id_composicao = :id_composicao";

		parent::Execute();
	}
}