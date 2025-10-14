<?php

namespace database;

class PurchaseListItem extends Connection {

	public function Create($id_compralista, $id_produto) {

		$this->data = [
			'id_compralista' => $id_compralista,
			'id_produto' => $id_produto
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_compralistaitem 
						($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function Read($id_compralistaitem) {

		$this->data = [
			"id_compralistaitem" => $id_compralistaitem
		];

		$this->query = "SELECT * FROM tab_compralistaitem 
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compralistaitem.id_produto
						WHERE id_compralistaitem = :id_compralistaitem";

		parent::Execute();
	}


	public function getItems($id_compralista) {

		$this->data = ["id_compralista" => $id_compralista];

		$this->query = "SELECT * FROM tab_compralistaitem 
						INNER JOIN tab_produto on tab_produto.id_produto = tab_compralistaitem.id_produto
						WHERE id_compralista = :id_compralista";	
		
		parent::Execute();
	}

	public function DeletePurchase($id_compralista) {

		$this->data = [
			"id_compralista" => $id_compralista
		];
		
		$this->query = "DELETE from tab_compralistaitem 
						WHERE id_compralista = :id_compralista";	
		
		parent::Execute();
	}

	public function Delete($id_compralistaitem) {

		$this->data = [
			"id_compralistaitem" => $id_compralistaitem
		];

		$this->query = "DELETE FROM tab_compralistaitem 
						WHERE id_compralistaitem = :id_compralistaitem";	
		
		parent::Execute();
		
		return parent::rowCount();
	}
}