<?php

namespace database;

class PurchaseList extends Connection {

	public function Create($descricao) {

		$this->data = [
			"descricao" => $descricao
		];
		
		$this->query = "INSERT INTO tab_compralista
						(descricao) VALUES (:descricao)";
		
		parent::Execute();
		return parent::lastInsertId();
	}

	public function Read($id_compralista) {

		$this->data = [
			"id_compralista" => $id_compralista
		];

		$this->query = "SELECT * FROM tab_compralista 
						WHERE id_compralista = :id_compralista";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_compralista
						ORDER BY descricao";

		parent::Execute();
	}

	public function Delete($id_compralista) {

		$item = new PurchaseListItem();

		$item->DeletePurchase($id_compralista);

		$this->data = [
			"id_compralista" => $id_compralista
		];

		$this->query = "DELETE from tab_compralista
						WHERE id_compralista = :id_compralista";	
		
		parent::Execute();
		return parent::rowCount();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_compralista" => $data['id_compralista'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_compralista
			set $field = :value where id_compralista = :id_compralista";
		
		parent::Execute();
	}
}