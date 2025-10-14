<?php

namespace database;

class BillsToPaySector extends Connection {

	public function Create($contasapagarsetor) {

		$this->data = [
			"contasapagarsetor" => $contasapagarsetor
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_contasapagarsetor
						($fields) VALUES ($places)";
		
		parent::Execute();

		return parent::lastInsertId();
	}

	public function Read($id_contasapagarsetor) {

		$this->data = [
			"id_contasapagarsetor" => $id_contasapagarsetor
		];

		$this->query = "SELECT * FROM tab_contasapagarsetor
						WHERE id_contasapagarsetor = :id_contasapagarsetor";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * from tab_contasapagarsetor
						ORDER BY contasapagarsetor";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data["field"];
		
		$this->data = [
			"id_contasapagarsetor" => $data['id_contasapagarsetor'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_contasapagarsetor
				SET $field = :value 
				WHERE id_contasapagarsetor = :id_contasapagarsetor";

		parent::Execute();
	}

	public function Delete($id_contasapagarsetor) {

		$this->data = [
			'id_contasapagarsetor' => $id_contasapagarsetor
		];

		$this->query = "DELETE FROM tab_contasapagarsetor 
						WHERE id_contasapagarsetor = :id_contasapagarsetor";	
		
		parent::Execute();
		
		return parent::rowCount();
	}	
}