<?php

namespace database;

class ProductComplement extends Connection {

	public function addComplement($id_produto, $id_complementogrupo) {

		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$preco = $row["preco"];

			if ($row["preco_promo"] > 0) {

				$preco = $row["preco_promo"];
			}

			$this->data = [
				"id_produto" => $id_produto,
				"id_complementogrupo" => $id_complementogrupo,
				"preco" => $preco
			];

			$fields = implode(', ', array_keys($this->data));
			$places = ':' . implode(", :", array_keys($this->data));

			$this->query = "INSERT INTO tab_produtocomplemento
				($fields) VALUES ($places)";

			parent::Execute();
			return parent::lastInsertId();

		} else {

			Notifier::Add("Erro ao ler dados do produtos!", Notifier::NOTIFIER_ERROR);
		}

		return null;
	}

	public function getComplement($id_produtocomplemento) {

		$this->data = [
			"id_produtocomplemento" => $id_produtocomplemento
		];

		$this->query = "SELECT tab_produto.*, tab_produtocomplemento.id_complementogrupo, tab_produtocomplemento.id_produtocomplemento, tab_produtocomplemento.preco as preco_complemento, tab_produtounidade.produtounidade FROM tab_produtocomplemento
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtocomplemento.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_produtocomplemento = :id_produtocomplemento";

		parent::Execute();
	}

	public function getComplements($id_complementogrupo) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "SELECT tab_produto.*, tab_produtocomplemento.id_complementogrupo, tab_produtocomplemento.id_produtocomplemento, tab_produtocomplemento.preco as preco_complemento, tab_produtounidade.produtounidade FROM tab_produtocomplemento
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtocomplemento.id_produto
						INNER JOIN tab_produtounidade on tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
	}

	public function updatePriceComplement($id_produtocomplemento, $preco) {

		$this->data = [
			"id_produtocomplemento" => $id_produtocomplemento,
			"preco" => $preco
		];

		$this->query = "UPDATE tab_produtocomplemento
						SET preco = :preco
						WHERE id_produtocomplemento = :id_produtocomplemento";

		parent::Execute();
		return parent::rowCount();
	}

	public function delComplement($id_produtocomplemento) {

		$this->data = [
			"id_produtocomplemento" => $id_produtocomplemento
		];

		$this->query = "DELETE FROM tab_produtocomplemento
						WHERE id_produtocomplemento = :id_produtocomplemento";

		parent::Execute();
		return parent::rowCount();
	}

	public function CreateGroup() {

		$this->data = [
			"descricao" => "Grupo de Complementos"
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_complementogrupo
			($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function LinkProduct($id_produto, $id_complementogrupo) {

		$this->data = [
			"id_produto" => $id_produto,
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "INSERT INTO tab_produtocomplementogrupo
			(id_produto, id_complementogrupo) VALUES (:id_produto, :id_complementogrupo)";

		parent::Execute();
		return parent::rowCount();
	}

	public function UnlinkProduct($id_produto, $id_complementogrupo) {

		$this->data = [
			"id_produto" => $id_produto,
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "DELETE FROM tab_produtocomplementogrupo
			WHERE id_produto = :id_produto AND id_complementogrupo = :id_complementogrupo";

		parent::Execute();
		return parent::rowCount();
	}

	public function UpdateComplementGroupDescricao($id_complementogrupo, $descricao) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo,
			"descricao" => $descricao
		];

		$this->query = "UPDATE tab_complementogrupo
			SET descricao = :descricao WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
		return parent::rowCount();
	}

	public function ReadGroups() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_complementogrupo order by descricao";

		parent::Execute();

	}

	public function ReadGroupsNotIn($id_produto) {

		$this->data = [
			"id_produto" => $id_produto
		];

		$this->query = "select * from tab_complementogrupo where id_complementogrupo not in (select id_complementogrupo from tab_produtocomplementogrupo where id_produto= :id_produto)";

		parent::Execute();

	}

	public function getGroup($id_complement_group) {

		$this->data = [
			"id_complementogrupo" => $id_complement_group
		];

		$this->query = "SELECT * FROM tab_complementogrupo
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();

	}

	public function getGroups($id_produto) {

		$this->data = [
			"id_produto" => $id_produto
		];

		$this->query = "SELECT * FROM tab_produtocomplementogrupo
						INNER JOIN tab_complementogrupo on tab_complementogrupo.id_complementogrupo = tab_produtocomplementogrupo.id_complementogrupo
						WHERE tab_produtocomplementogrupo.id_produto = :id_produto";

		parent::Execute();

	}

	public function addQtdMin($id_complementogrupo) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "UPDATE tab_complementogrupo
						SET qtd_min = qtd_min + 1
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
	}

	public function delQtdMin($id_complementogrupo) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "UPDATE tab_complementogrupo
						SET qtd_min = qtd_min - 1
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
	}

	public function addQtdMax($id_complementogrupo) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "UPDATE tab_complementogrupo
						SET qtd_max = qtd_max + 1
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
	}

	public function delQtdMax($id_complementogrupo) {

		$this->data = [
			"id_complementogrupo" => $id_complementogrupo
		];

		$this->query = "UPDATE tab_complementogrupo
						SET qtd_max = qtd_max - 1
						WHERE id_complementogrupo = :id_complementogrupo";

		parent::Execute();
	}

	// public function Read($id_composicao, $id_produto) {

	// 	$this->data = [
	// 		"id_composicao" => $id_composicao,
	// 		"id_produto" => $id_produto,
	// 	];

	// 	$this->query = "SELECT * FROM tab_produtocomposicao
	// 		INNER JOIN tab_produto
	// 		ON tab_produto.id_produto = tab_produtocomposicao.id_produto
	// 		INNER JOIN tab_produtounidade
	// 		ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
	// 		WHERE tab_produtocomposicao.id_composicao = :id_composicao AND tab_produtocomposicao.id_produto = :id_produto";

	// 	parent::Execute();
	// }

	// public function Update(array $data) {

	// 	$field = $data['field'];

	// 	$this->data = [
	// 		"id_composicao" => $data['id_composicao'],
	// 		"id_produto" => $data['id_produto'],
	// 		"value" => $data['value'],
	// 	];

	// 	$this->query = "UPDATE tab_produtocomposicao
	// 		SET $field = :value
	// 		WHERE id_composicao = :id_composicao AND id_produto = :id_produto";

	// 	parent::Execute();
	// }

	// public function Delete($id_composicao, $id_produto) {

	// 	$this->data = [
	// 		"id_composicao" => $id_composicao,
	// 		"id_produto" => $id_produto,
	// 	];

	// 	$this->query = "DELETE FROM tab_produtocomposicao
	// 		WHERE id_composicao = :id_composicao AND id_produto = :id_produto";

	// 	parent::Execute();
	// 	return parent::rowCount();
	// }

	// public function getList($id_composicao) {

	// 	$this->data = ["id_composicao" => $id_composicao];

	// 	$this->query = "SELECT * FROM tab_produtocomposicao
	// 		INNER JOIN tab_produto ON tab_produto.id_produto = tab_produtocomposicao.id_produto
	// 		INNER JOIN tab_produtounidade ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
	// 		WHERE tab_produtocomposicao.id_composicao = :id_composicao";

	// 	parent::Execute();
	// }

	public static function FormatFieldsComplementGroup($row) {

		$tplProduct = new View('templates/product');

		if ($row["qtd_min"] == 0) {

			$row["extra_block_complementgroup_qtdmin_min"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN_DISABLED");

		} else {

			$row["extra_block_complementgroup_qtdmin_min"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN");
		}

		if ($row["qtd_min"] < $row["qtd_max"]) {

			$row["extra_block_complementgroup_qtdmin_max"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX");

		} else {

			$row["extra_block_complementgroup_qtdmin_max"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX_DISABLED");
		}

		if ($row["qtd_max"] > $row["qtd_min"] && $row["qtd_max"] > 1) {

			$row["extra_block_complementgroup_qtdmax_min"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN");

		} else {

			$row["extra_block_complementgroup_qtdmax_min"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN_DISABLED");
		}

		// if ($row["qtd_max"] < $row["qtd_max"]) {

		// 	$row["extra_block_complementgroup_qtdmax_max"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MAX");

		// } else {

		// 	$row["extra_block_complementgroup_qtdmax_max"] = $tplProduct->getContent($row, "EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MAX_DISABLED");
		// }

		return $row;
	}
}