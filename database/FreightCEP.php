<?php

namespace database;

class FreightCEP extends Connection {

	public function Create($descricao, $cep_de, $cep_ate, $id_fretevalor, $ativo = 1) {

		if ($this->hasCEPConflict($cep_de, $cep_ate) == true) {

			Notifier::Add("Faixa de CEP em conflito com CEP cadastrado.", Notifier::NOTIFIER_ERROR);
			return null;
		}

		$this->data = [
			"descricao" => $descricao,
			"cep_de" => $cep_de,
			"cep_ate" => $cep_ate,
			"id_fretevalor" => $id_fretevalor,
			"ativo" => $ativo
		];

		$this->query = "INSERT into tab_fretecep
						(descricao, cep_de, cep_ate, id_fretevalor, ativo)
						values (:descricao, :cep_de, :cep_ate, :id_fretevalor, :ativo)";

		parent::Execute();

		if (parent::rowCount() > 0) {

			return parent::lastInsertId();

		} else {

			Notifier::Add("Erro ao cadastrar faixa de CEP.", Notifier::NOTIFIER_ERROR);
			return null;
		}
	}

    public function Read($id_fretecep) {

		$this->data = [
			"id_fretecep" => $id_fretecep
		];

		$this->query = "SELECT tab_fretecep.*, tab_fretevalor.valor from tab_fretecep
						INNER JOIN tab_fretevalor on tab_fretevalor.id_fretevalor = tab_fretecep.id_fretevalor
						WHERE id_fretecep = :id_fretecep";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_fretecep" => $data['id_fretecep'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_fretecep
						SET $field = :value
						WHERE id_fretecep = :id_fretecep";

		parent::Execute();
	}

	public function Delete($id_fretecep) {

		$this->data = [
			"id_fretecep" => $id_fretecep,
		];

		$this->query = "DELETE from tab_fretecep
						WHERE id_fretecep = :id_fretecep";

		parent::Execute();

		return parent::rowCount();
	}

	public function hasCEPConflict($cep_de, $cep_ate, $id_fretecep_ignores = null) {

		$this->data = [
			"cep_de" => $cep_de,
			"cep_ate" => $cep_ate,
		];

		$this->query = "SELECT * FROM tab_fretecep
						WHERE cep_de <= :cep_ate AND cep_ate >= :cep_de";

		if ($id_fretecep_ignores) {

			$this->data["id_fretecep"] = $id_fretecep_ignores;

			$this->query .= " AND id_fretecep != :id_fretecep";
		}

		parent::Execute();

		return parent::rowCount() > 0;
	}

	public function getCEPValue($cep) {

		$this->data = [
			"cep" => $cep
		];

		$this->query = "SELECT tab_fretecep.*, tab_fretevalor.valor from tab_fretecep
						INNER JOIN tab_fretevalor on tab_fretevalor.id_fretevalor = tab_fretecep.id_fretevalor
						WHERE :cep BETWEEN cep_de AND cep_ate";

		parent::Execute();
	}

	public function getListNoRules() {

		$this->data = [];

		$this->query = "SELECT tab_fretecep.*, tab_fretevalor.valor from tab_fretecep
						INNER JOIN tab_fretevalor on tab_fretevalor.id_fretevalor = tab_fretecep.id_fretevalor
						WHERE :cep BETWEEN cep_de AND cep_ate";

		parent::Execute();
	}

	public function ToggleAtivo($id_fretecep) {

		$this->data = [
			"id_fretecep" => $id_fretecep
		];

		$this->query = "UPDATE tab_fretecep
						SET ativo = not ativo
						WHERE id_fretecep = :id_fretecep";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT tab_fretecep.*, tab_fretevalor.valor from tab_fretecep
						INNER JOIN tab_fretevalor on tab_fretevalor.id_fretevalor = tab_fretecep.id_fretevalor
						ORDER BY tab_fretecep.descricao";

		parent::Execute();
	}

	public function hasIdFreightValue($id_fretevalor) {

		$this->data = [
			"id_fretevalor" => $id_fretevalor
		];

		$this->query = "SELECT * from tab_fretecep
						WHERE id_fretevalor = :id_fretevalor";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public static function FormatFields($row) {

		if (key_exists("valor", $row)) {

			$row['valor_formatted'] = number_format($row['valor'], 2, ",", ".");
		}

		$row['cep_de'] = str_repeat("0", 8 - mb_strlen($row['cep_de'])) . $row["cep_de"];
		$row['cep_ate'] = str_repeat("0", 8 - mb_strlen($row['cep_ate'])) . $row["cep_ate"];

		$row['cep_de_formatted'] = FormatCEP($row['cep_de']);
		$row['cep_ate_formatted'] = FormatCEP($row['cep_ate']);

		if ($row["ativo"] == 1) {

			$row["ativo"] = "checked";

		} else {

			$row["ativo"] = "";
		}

		return $row;
	}
}