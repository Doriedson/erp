<?php

namespace database;

class FreightValue extends Connection {

	public function Create($descricao, $valor) {

		$this->data = [
			'descricao' => $descricao,
			'valor' => $valor
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_fretevalor ($fields) VALUES ($places)";

		parent::Execute();

		return parent::lastInsertId();
	}

	public function Read($id_fretevalor) {

		$this->data = [
			"id_fretevalor" => $id_fretevalor
		];

		$this->query = "SELECT * from tab_fretevalor
						WHERE id_fretevalor = :id_fretevalor";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_fretevalor" => $data["id_fretevalor"],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_fretevalor
						SET $field = :value
						WHERE id_fretevalor = :id_fretevalor";

		parent::Execute();
	}

	public function Delete($id_fretevalor) {

		$freightCep = new FreightCEP();

		if ($freightCep->hasIdFreightValue($id_fretevalor) == true) {

			Notifier::Add("Não é possível remover valor em uso.", Notifier::NOTIFIER_ERROR);
			return 0;
		}

		$this->data = [
			"id_fretevalor" => $id_fretevalor
		];

		$this->query = "Delete from tab_fretevalor
						WHERE id_fretevalor = :id_fretevalor";

		parent::Execute();

		return parent::rowCount();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * from tab_fretevalor order by valor";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['valor_formatted'] = number_format($row['valor'], 2, ",", ".");

		return $row;
	}
}