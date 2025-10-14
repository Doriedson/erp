<?php

namespace database;

class EntityAddress extends Connection {

	public function Read($id_address) {

		$this->data = ['id_endereco' => $id_address];

		$this->query = "SELECT * FROM tab_entidadeendereco
						WHERE id_endereco = :id_endereco";

		parent::Execute();
    }

	public function Delete($id_address) {

		$this->data = ['id_endereco' => $id_address];

		$this->query = "DELETE FROM tab_entidadeendereco
						WHERE id_endereco = :id_endereco";

		parent::Execute();
		return parent::rowCount();
	}

    public function getList($id_entity) {

		$this->data = ['id_entidade' => $id_entity];

		$this->query = "SELECT * FROM tab_entidadeendereco
						WHERE id_entidade = :id_entidade";

		parent::Execute();
    }

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_endereco" => $data['id_endereco'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_entidadeendereco set $field = :value
						WHERE id_endereco = :id_endereco";

		parent::Execute();
	}

	public function Create($id_entidade, $nickname = "", $logradouro = "", $numero = null, $complemento = "", $bairro = "", $cidade = "", $uf = "SP", $cep = "", $obs = "") {

		$this->data = [
			'id_entidade' => $id_entidade,
			'nickname' => $nickname,
			'logradouro' => $logradouro,
			"numero" => $numero,
			"complemento" => $complemento,
			'bairro' => $bairro,
			'cidade' => $cidade,
			'uf' => $uf,
			'cep' => $cep,
			'obs' => $obs
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_entidadeendereco ($fields) VALUES ($places)";

		parent::Execute();

		if (parent::rowCount() == 1) {

			return parent::lastInsertId();

		} else {

			return 0;
		}
	}

	public function CreateFrom($data) {

		$this->data = $data;

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_entidadeendereco ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function getListCEP($cep) {

		$this->data = [
			'cep' => $cep
		];

		$this->query = "SELECT * FROM tab_entidadeendereco
						WHERE cep = :cep";

		parent::Execute();
    }

	public function getListDistinctCEP() {

		$this->data = [];

		$this->query = "SELECT cep FROM tab_entidadeendereco
						WHERE CHAR_LENGTH(cep) = 8
						GROUP BY cep ORDER BY cep";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['cep_formatted'] = FormatCEP($row['cep']);

		$endereco = "";

		// if ($row["nickname"] != "") {

		// 	$endereco = $row["nickname"];

		// }

		if ($row["logradouro"] != "") {

			// if ($endereco != "") {

				// $endereco .= " - ";
			// }

			$endereco = $row["logradouro"];
		}

		if ($row["numero"] != null) {

			if ($endereco != "") {

				$endereco .= ", ";
			}

			$endereco .= $row["numero"];
		}

		if ($row["complemento"] != "") {

			if ($endereco != "") {

				$endereco .= " ";
			}

			$endereco .= $row["complemento"];
		}

		if ($row["bairro"] != "") {

			if ($endereco != "") {

				$endereco .= " - ";
			}

			$endereco .= $row["bairro"];
		}

		if ($row["cidade"] != "") {

			if ($endereco != "") {

				$endereco .= " - ";
			}

			$endereco .= $row["cidade"];
		}

		if ($row["uf"] != "") {

			if ($endereco != "") {

				$endereco .= " - ";
			}

			$endereco .= $row["uf"];
		}

		if ($row['cep_formatted'] != "") {

			if ($endereco != "") {

				$endereco .= " - ";
			}

			$endereco .= "CEP: " . $row['cep_formatted'];
		}

		if ($row["obs"] != "") {

			if ($endereco != "") {

				$endereco .= " - ";
			}

			$endereco .= "Obs.: " . $row["obs"];
		}

		$row["endereco"] = $endereco;

		return $row;
	}
}