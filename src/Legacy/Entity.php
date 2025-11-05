<?php

namespace App\Legacy;

class Entity extends Connection {

	public function Read($id_entidade) {

		$this->data = [
			"id_entidade" => $id_entidade
		];

		$this->query = "SELECT * FROM tab_entidade
			WHERE id_entidade = :id_entidade";

		parent::Execute();
	}

	public function ReadName($name) {

		$this->data = [
			"nome" => $name
		];

		$this->query = "SELECT * FROM tab_entidade
			WHERE nome = :nome";

		parent::Execute();
	}

	public function Search($data) {

		$data = (string) $data;
		$data = '%' . str_replace('+', '%+%', $data) . '%';
		$this->data = explode('+', $data);

		$this->query = "SELECT * FROM tab_entidade
			WHERE nome LIKE ?";

		for ($indexCount = count($this->data); $indexCount > 1; $indexCount--) {

			$this->query .= " AND nome LIKE ?";
		}

		$this->query .= " ORDER BY nome";

		parent::Execute();
	}

	public function SearchByCode($id_entidade) {

		$this->data = [
			'id_entidade' => $id_entidade
		];

		if (mb_strlen($id_entidade) < 7) {

			$this->query = "SELECT * FROM tab_entidade
			WHERE id_entidade = :id_entidade
			ORDER BY id_entidade, nome";

		} else {

			$this->data['phone'] = "%" . $id_entidade;

			if (mb_strlen($id_entidade) < 11) {

				$this->query = "SELECT * FROM tab_entidade
				WHERE id_entidade = :id_entidade
				OR telcelular LIKE :phone
				OR telresidencial LIKE :phone
				OR telcomercial LIKE :phone
				ORDER BY id_entidade, nome";

			} else {

				$this->query = "SELECT * FROM tab_entidade
				WHERE id_entidade = :id_entidade
				OR cpfcnpj = :id_entidade
				OR telcelular LIKE :phone
				OR telresidencial LIKE :phone
				OR telcomercial LIKE :phone
				ORDER BY id_entidade, nome";
			}
		}

		parent::Execute();
	}

	public function SearchByPhone($phone) {

		$this->data = ['phone' => "%" . $phone];


		$this->query = "SELECT * FROM tab_entidade
						WHERE telcelular like :phone OR telresidencial like :phone OR telcomercial like :phone";


		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_entidade
						ORDER BY nome";

		parent::Execute();
	}

	public function setLimite($id_entidade, $valor) {

		$this->data = [
			"id_entidade" => $id_entidade,
			"limite" => $valor
		];

		$this->query = "UPDATE tab_entidade
						SET limite = :limite where id_entidade = :id_entidade";

		parent::Execute();

		if (parent::rowCount() > 0) {

			$log = new Log();

			$log->EntidadeLimite($GLOBALS['authorized_payload']->id, $id_entidade, $valor);
		}
	}

	public function ToggleActive($id_entity) {

		$this->data = [
			"id_entidade" => $id_entity
		];

		$this->query = "UPDATE tab_entidade
						SET ativo = not ativo
						WHERE id_entidade = :id_entidade";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_entidade" => $data['id_entidade'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_entidade set $field = :value where id_entidade = :id_entidade";

		parent::Execute();
	}

	public function Create($data) {

		$this->data = $data;

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_entidade ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function setCredito($id_entidade, $credito, $obs, $id_auth) {

		$this->data = [
			"id_entidade" => $id_entidade,
			"valor" => $credito,
		];

		$this->query = "UPDATE tab_entidade
						SET credito = credito + :valor
						WHERE id_entidade = :id_entidade";

		parent::Execute();

		$this->data["obs"] = $obs;
		$this->data["id_colaborador"] = $id_auth;

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_entidadecredito
						($fields) VALUES ($places)";

		parent::Execute();
	}

	public function getEntityCreditByDateInterval($dataini, $datafim) {

		$this->data = [
			'datestart' => $dataini . " 00:00:00",
			'dateend' => $datafim . " 23:59:59"
		];

		$this->query = "SELECT tab_entidadecredito.*, cli.nome, collab.nome as colaborador, tab_entidadecredito.obs as obs_credit from tab_entidadecredito
										INNER JOIN tab_entidade cli on tab_entidadecredito.id_entidade = cli.id_entidade
										INNER JOIN tab_entidade collab on tab_entidadecredito.id_colaborador = collab.id_entidade
										WHERE data BETWEEN :datestart AND :dateend
										ORDER BY cli.nome, tab_entidadecredito.data";

		parent::Execute();
	}

	public static function FormatFields($row) {

		if (array_key_exists('datacad', $row)) {

			$row['datacad_formatted'] = date_format(date_create($row['datacad']), 'd/m/Y');
			$row['datacad'] = date_format(date_create($row['datacad']), 'Y-m-d');
		}

		if (array_key_exists('limite', $row)) {

			$row['limite_formatted'] = number_format($row['limite'], 2, ",", ".");
		}

		if (array_key_exists('credito', $row)) {

			$row['credito_formatted'] = number_format($row['credito'], 2, ",", ".");
		}

		if (array_key_exists('cpfcnpj', $row)) {

			$cpfcnpj = new ValidaCPFCNPJ($row['cpfcnpj']);

			$row['cpfcnpj_formatted'] = $cpfcnpj->formata();
		}

		if (array_key_exists('ativo', $row)) {

			$tplEntity = new View('entity');

			if ($row['ativo'] == 1) {

				$row['extra_block_entity_button_status'] = $tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_BUTTON_ATIVO");
				$row['class_status'] = "pseudo-button button-green";

			} else {

				$row['extra_block_entity_button_status'] = $tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_BUTTON_INATIVO");
				$row['class_status'] = "pseudo-button button-red";
			}
		}

		if (array_key_exists('telcelular', $row)) {

			$row['telcelular_formatted'] = FormatTel($row['telcelular']);
		}

		if (array_key_exists('telresidencial', $row)) {

			$row['telresidencial_formatted'] = FormatTel($row['telresidencial']);
		}

		if (array_key_exists('telcomercial', $row)) {

			$row['telcomercial_formatted'] = FormatTel($row['telcomercial']);
		}

		return $row;
	}
}