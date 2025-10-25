<?php

namespace App\Legacy;

class CashChange extends Connection {

	public function Read($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT *
						FROM tab_caixatroco
						WHERE id_caixa =  :id_caixa";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_caixa" => $data['id_caixa'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_caixatroco set $field = :value where id_caixa = :id_caixa";

		parent::Execute();

		$rowCount = parent::rowCount();

		$this->getTotal($data['id_caixa']);

		$row = parent::getResult();

		$cashier = new Cashier();

		$cashier->UpdateTrocoFim($data['id_caixa'], $row['troco']);

		return $rowCount;
	}

	public function getTotal($id_caixa) {

		$this->data = [
			"id_caixa" => $id_caixa,
		];

		$this->query = "SELECT (moeda_1 + moeda_5 + moeda_10 + moeda_25 + moeda_50 + cedula_1 + cedula_2 + cedula_5 + cedula_10 + cedula_20 + cedula_50 + cedula_100 + cedula_200) as troco
						FROM tab_caixatroco
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function UpdateAll($id_caixa, $moeda_1, $moeda_5, $moeda_10, $moeda_25, $moeda_50, $cedula_1, $cedula_2, $cedula_5, $cedula_10, $cedula_20, $cedula_50, $cedula_100, $cedula_200) {

		$this->data = [
			'id_caixa' => $id_caixa,
			'moeda_1' => $moeda_1,
			'moeda_5' => $moeda_5,
			'moeda_10' => $moeda_10,
			'moeda_25' => $moeda_25,
			'moeda_50' => $moeda_50,
			'cedula_1' => $cedula_1,
			'cedula_2' => $cedula_2,
			'cedula_5' => $cedula_5,
			'cedula_10' => $cedula_10,
			'cedula_20' => $cedula_20,
			'cedula_50' => $cedula_50,
			'cedula_100' => $cedula_100,
			'cedula_200' => $cedula_200,
		];

		$this->query = "UPDATE tab_caixatroco
						SET moeda_1 = :moeda_1, moeda_5 = :moeda_5, moeda_10 = :moeda_10, moeda_25 = :moeda_25, moeda_50 = :moeda_50,
						cedula_1 = :cedula_1, cedula_2 = :cedula_2, cedula_5 = :cedula_5, cedula_10 = :cedula_10, cedula_20 = :cedula_20,
						cedula_50 = :cedula_50, cedula_100 = :cedula_100, cedula_200 = :cedula_200
						WHERE id_caixa =  :id_caixa";

		parent::Execute();

		$rowCount = parent::rowCount();

		$this->data = [
			"id_caixa" => $id_caixa,
		];

		$this->query = "SELECT (moeda_1 + moeda_5 + moeda_10 + moeda_25 + moeda_50 + cedula_1 + cedula_2 + cedula_5 + cedula_10 + cedula_20 + cedula_50 + cedula_100 + cedula_200) as troco
						FROM tab_caixatroco
						WHERE id_caixa = :id_caixa";

		parent::Execute();

		$row = parent::getResult();

		$cashier = new Cashier();

		$cashier->UpdateTrocoFim($id_caixa, $row['troco']);

		return $rowCount;
	}

	public static function FormatFields($row) {

		$row['moeda_1_formatted'] = number_format($row['moeda_1'], 2, ",", ".");
		$row['moeda_5_formatted'] = number_format($row['moeda_5'], 2, ",", ".");
		$row['moeda_10_formatted'] = number_format($row['moeda_10'], 2, ",", ".");
		$row['moeda_25_formatted'] = number_format($row['moeda_25'], 2, ",", ".");
		$row['moeda_50_formatted'] = number_format($row['moeda_50'], 2, ",", ".");
		$row['cedula_1_formatted'] = number_format($row['cedula_1'], 2, ",", ".");
		$row['cedula_2_formatted'] = number_format($row['cedula_2'], 2, ",", ".");
		$row['cedula_5_formatted'] = number_format($row['cedula_5'], 2, ",", ".");
		$row['cedula_10_formatted'] = number_format($row['cedula_10'], 2, ",", ".");
		$row['cedula_20_formatted'] = number_format($row['cedula_20'], 2, ",", ".");
		$row['cedula_50_formatted'] = number_format($row['cedula_50'], 2, ",", ".");
		$row['cedula_100_formatted'] = number_format($row['cedula_100'], 2, ",", ".");
		$row['cedula_200_formatted'] = number_format($row['cedula_200'], 2, ",", ".");

		$row['troco_total'] = $row['moeda_1'] + $row['moeda_5'] + $row['moeda_10'] + $row['moeda_25'] + $row['moeda_50'] + $row['cedula_1'] + $row['cedula_2'] + $row['cedula_5'] + $row['cedula_10'] + $row['cedula_20'] + $row['cedula_50'] + $row['cedula_100'] + $row['cedula_200'];

		$row['troco_total_formatted'] = number_format($row['troco_total'], 2, ",", ".");

		return $row;
	}
}