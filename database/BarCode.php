<?php

namespace database;

class BarCode extends Connection {

	public function getList($id_produto) {

		$this->data = ["id_produto" => $id_produto];

		$this->query = "SELECT * FROM tab_produtocodbar
			WHERE id_produto = :id_produto";

		parent::Execute();
	}

	public function Read($codbar) {

		$this->data = ["codbar" => $codbar];

		$this->query = "SELECT * FROM tab_produtocodbar
			INNER JOIN tab_produto on tab_produto.id_produto = tab_produtocodbar.id_produto
			WHERE codbar = :codbar";

		parent::Execute();
	}

	public function Create($id_produto, $codbar) {

		$this->data = [
			'id_produto' => $id_produto,
			'codbar' => $codbar,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produtocodbar ($fields) VALUES ($places)";

		parent::Execute();
	}

	public function Delete($codbar) {

		$this->data = ["codbar" => $codbar];

		$this->query = "DELETE FROM tab_produtocodbar
			WHERE codbar = :codbar";

		parent::Execute();
		return parent::rowCount();
	}

	static public function Format($row) {

		switch (mb_strlen($row["codbar"])) {

			case 8:

				$row["barcode_legend"] = "EAN-8";
			break;

			case 12:

				$row["barcode_legend"] = "UPC-12";
			break;

			case 13:

				$row["barcode_legend"] = "EAN-13";
			break;

			default:

				$row["barcode_legend"] = "Código não identificado";
			break;
		}

		return $row;
	}
}