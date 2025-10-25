<?php

namespace App\Legacy;

class BlackFriday extends Connection {

	public function Create($data) {

		$this->data = [
			"data" => $data['data'],
			"desconto" => $data['desconto'],
			"acumulativo" => $data['acumulativo'],
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_blackfriday
						($fields) VALUES ($places)";

		parent::Execute();

		return parent::lastInsertId();
	}

	public function Read($id_blackfriday) {

		$this->data = [
			"id_blackfriday" => $id_blackfriday,
		];

		$this->query = "SELECT * from tab_blackfriday
						WhERE id_blackfriday = :id_blackfriday";

		parent::Execute();
	}

	public function Delete($id_blackfriday) {

		$this->data = [
			"id_blackfriday" => $id_blackfriday
		];

		$this->query = "DELETE from tab_blackfriday
						WHERE id_blackfriday = :id_blackfriday";

		parent::Execute();
		return parent::rowCount();
	}

	public function hasDate($date) {

		$this->data = [
			"data" => $date
		];

		$this->query = "Select * from tab_blackfriday
						where data = :data";

		parent::Execute();

		return parent::rowCount() > 0;
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * from tab_blackfriday order by data";

		parent::Execute();
	}

	public function isToday() {

		$this->data = [];

		$this->query = "SELECT * from tab_blackfriday
						where year(data)=year(now()) and month(data)=month(now()) and day(data)=day(now())";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['data_formatted'] = date_format(date_create($row['data']),'d/m/Y');
		$row['desconto_formatted'] = number_format($row['desconto'], 2, ',', '.');
		$row['acumula'] = $row['acumulativo']?"Sim":"Não";

		return $row;
	}
}