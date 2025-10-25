<?php

namespace App\Legacy;

class Provider extends Connection {

    public function Create() {

		$this->data = [
			'razaosocial' => 'Novo Fornecedor',
			'nomefantasia' => 'Novo Fornecedor',
		];

		// $this->data = $data;
		// $this->data['datacad'] = date("Y-m-d");

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_fornecedor ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function Read($id_fornecedor) {

		$this->data = [
			"id_fornecedor" => $id_fornecedor
		];

		$this->query = "SELECT * FROM tab_fornecedor
			WHERE id_fornecedor = :id_fornecedor
			OR cpfcnpj = :id_fornecedor";

		parent::Execute();
	}

	public function ReadName($razaosocial) {

		$this->data = [
			"razaosocial" => $razaosocial
		];

		$this->query = "SELECT * FROM tab_fornecedor
			WHERE razaosocial = :razaosocial";

		parent::Execute();
	}

	public function Search($data) {

		$data = (string) $data;
		// $data = '%' . str_replace('+', '%+%', $data) . '%';
		$data = str_replace('+', '|', $data);
		// $this->data = explode('+', $data);
        $this->data = [
            "data" => $data
        ];

		$this->query = "SELECT * FROM tab_fornecedor
			WHERE razaosocial RLIKE :data or nomefantasia RLIKE :data
            ORDER BY razaosocial, nomefantasia";

		// for ($indexCount = count($this->data); $indexCount > 1; $indexCount--) {

		// 	$this->query .= " AND nome LIKE ?";
		// }

		// $this->query .= " ORDER BY nome";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_fornecedor
						ORDER BY razaosocial, nomefantasia";

		parent::Execute();
	}

	public function ToggleActive($id_provider) {

		$this->data = [
			"id_fornecedor" => $id_provider
		];

		$this->query = "UPDATE tab_fornecedor
						SET ativo = not ativo
						WHERE id_fornecedor = :id_fornecedor";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_fornecedor" => $data['id_fornecedor'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_fornecedor
						SET $field = :value
						WHERE id_fornecedor = :id_fornecedor";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$tplProvider = new View('provider');

		if ($row['ativo'] == 1) {

			$row['extra_block_provider_button_status'] = $tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER_BUTTON_ATIVO");
			$row['class_status'] = "pseudo-button button-green";

		} else {

			$row['extra_block_provider_button_status'] = $tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER_BUTTON_INATIVO");
			$row['class_status'] = "pseudo-button button-red";
		}

		$uf_array = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');

		$row["uf_option"] = "";

		foreach ($uf_array as $uf) {

			if ($row['uf'] == $uf) {

				$row["uf_option"].= "<option selected value='$uf'>$uf</option>";

			} else {

				$row["uf_option"].= "<option value='$uf'>$uf</option>";
			}
		}

		$row['datacad_formatted'] = date_format(date_create($row['datacad']), 'd/m/Y');

		$valida_cpf_cnpj = new ValidaCPFCNPJ($row['cpfcnpj']);

		$row['ie_formatted'] = $row['ie'];

		$row['cpfcnpj_formatted'] = $valida_cpf_cnpj->formata();

		$row['cep_formatted'] = FormatCEP($row['cep']);

		$row['telefone1_formatted'] = FormatTel($row['telefone1']);
		$row['telefone2_formatted'] = FormatTel($row['telefone2']);
		$row['telefone3_formatted'] = FormatTel($row['telefone3']);

		return $row;
	}
}