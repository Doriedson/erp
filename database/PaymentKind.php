<?php

namespace database;

class PaymentKind extends Connection {

	public function Create($especie) {

		$this->data = [
			"especie" => $especie
		];
		
		$this->query = "INSERT INTO tab_especie
                        (especie, ativo) values (:especie, 1)";

		parent::Execute();

		return parent::lastInsertId();
	}

	public function Read($id_especie) {

		$this->data = [
			"id_especie" => $id_especie
		];
		
		$this->query = "SELECT *
						FROM tab_especie
                        WHERE id_especie = :id_especie";

		parent::Execute();
	}

	public function Update($id_especie, $especie) {

		$this->data = [
			"id_especie" => $id_especie,
			"especie" => $especie
		];

        $this->query = "UPDATE tab_especie
                        set especie = :especie
                        where id_especie = :id_especie";

        parent::Execute();
    }

	public function Delete($id_especie) {

		$this->data = [
			'id_especie' => $id_especie
		];

		$this->query = "DELETE from tab_especie 
						WHERE id_especie = :id_especie";	
		
		parent::Execute();
		
		return parent::rowCount();
	}

	public function ToggleActive($id_especie) {

		$this->data = [
			"id_especie" => $id_especie
		];
		
		$this->query = "UPDATE tab_especie
                        SET ativo = not ativo
                        WHERE id_especie = :id_especie";

		parent::Execute();
	}

	public function isInUse($id_especie) {

		$this->data = [
			"id_especie" => $id_especie
		];

		$this->query = "SELECT * from tab_vendapay
						WHERE id_especie = :id_especie limit 1";

		parent::Execute();

		if (parent::rowCount() > 0) {

			return true;
		}

		$this->query = "SELECT * from tab_caixasangria
						WHERE id_especie = :id_especie limit 1";

		parent::Execute();

		if (parent::rowCount() > 0) {

			return true;
		}

		$this->query = "SELECT * from tab_caixareforco
						WHERE id_especie = :id_especie limit 1";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public function getList() {

		$this->data = [];
		
		$this->query = "SELECT *
						FROM tab_especie
                        ORDER BY especie";

		parent::Execute();
	}

	public static function FormatFields($row) {

		if ($row['ativo'] == 1) {

			$row['ativo'] = "checked";

		} else {

			$row['ativo'] = "";
		}

		return $row;
	}
}