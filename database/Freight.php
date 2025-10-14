<?php

namespace database;

class Freight extends Connection {

    public function Read() {

		$this->data = [];

		$this->query = "SELECT * from tab_frete";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_frete
						SET $field = :value";

		parent::Execute();
	}

	public function ToggleFretegratis() {

		$this->data = [];

		$this->query = "UPDATE tab_frete
						SET fretegratis = not fretegratis";

		parent::Execute();
	}

	public function ToggleDeliveryminimo() {

		$this->data = [];

		$this->query = "UPDATE tab_frete
						SET deliveryminimo = not deliveryminimo";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['fretegratis_valor_formatted'] = number_format($row['fretegratis_valor'], 2, ",", ".");
		$row['deliveryminimo_valor_formatted'] = number_format($row['deliveryminimo_valor'], 2, ",", ".");

		if ($row["fretegratis"] == 1) {

			$row["fretegratis"] = "checked";
			$row["fretegratis_disabled"] = "";

		} else {

			$row["fretegratis"] = "";
			$row["fretegratis_disabled"] = "disabled";
		}

		if ($row["deliveryminimo"] == 1) {

			$row["deliveryminimo"] = "checked";
			$row["deliveryminimo_disabled"] = "";

		} else {

			$row["deliveryminimo"] = "";
			$row["deliveryminimo_disabled"] = "disabled";
		}

		return $row;
	}
}