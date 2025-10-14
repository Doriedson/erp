<?php

namespace database;

class PriceTag extends Connection {

	public function Read($id_etiqueta) {

		$this->data = ['id_etiqueta' => $id_etiqueta];
		
		$this->query = "SELECT * FROM tab_etiqueta 
						INNER JOIN tab_produto
						ON tab_produto.id_produto = tab_etiqueta.id_produto
						INNER JOIN tab_produtounidade
						ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
						WHERE id_etiqueta = :id_etiqueta";

		parent::Execute();
	}

	public function has($id_produto) {

		$this->data = ['id_produto' => $id_produto];
		
		$this->query = "SELECT * FROM tab_etiqueta 
						WHERE id_produto = :id_produto";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public function getList($tag_option) {

		$this->data = [];

		switch ($tag_option) {

			case "PRICE":
				$this->query = "SELECT * FROM tab_etiqueta 
								INNER JOIN tab_produto
								ON tab_produto.id_produto = tab_etiqueta.id_produto
								INNER JOIN tab_produtounidade
								ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade AND tab_produto.preco_promo = 0
								ORDER BY tab_produto.produto";
			break;

			case "SALEOFF":
				$this->query = "SELECT * FROM tab_etiqueta 
								INNER JOIN tab_produto
								ON tab_produto.id_produto = tab_etiqueta.id_produto
								INNER JOIN tab_produtounidade
								ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade AND tab_produto.preco_promo > 0
								ORDER BY tab_produto.produto";
			break;

			case "ACTIVE":
				$this->query = "SELECT * FROM tab_etiqueta 
								INNER JOIN tab_produto
								ON tab_produto.id_produto = tab_etiqueta.id_produto
								INNER JOIN tab_produtounidade
								ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade AND tab_produto.ativo = 1
								ORDER BY tab_produto.produto";
			break;

			case "INACTIVE":
				$this->query = "SELECT * FROM tab_etiqueta 
								INNER JOIN tab_produto
								ON tab_produto.id_produto = tab_etiqueta.id_produto
								INNER JOIN tab_produtounidade
								ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade AND tab_produto.ativo = 0
								ORDER BY tab_produto.produto";
			break;

			default:
				$this->query = "SELECT * FROM tab_etiqueta 
								INNER JOIN tab_produto
								ON tab_produto.id_produto = tab_etiqueta.id_produto
								INNER JOIN tab_produtounidade
								ON tab_produtounidade.id_produtounidade = tab_produto.id_produtounidade
								ORDER BY tab_produto.produto";
			break;
		}
		
		parent::Execute();
	}
	
	public function Create($id_produto) {

		$this->data = ['id_produto' => $id_produto];

		$this->query = "INSERT INTO tab_etiqueta 
			(id_produto) VALUES (:id_produto)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function DeleteAll() {

		$this->data = [];

		$this->query = "DELETE FROM tab_etiqueta";		

		parent::Execute();
		return parent::rowCount();
	}

	public function Delete($id_etiqueta) {

		$this->data = ['id_etiqueta' => $id_etiqueta];

		$this->query = "DELETE FROM tab_etiqueta
						WHERE id_etiqueta = :id_etiqueta";		

		parent::Execute();
		return parent::rowCount();
	}
}