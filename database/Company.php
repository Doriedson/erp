<?php

namespace database;

class Company extends Connection {

	public function Read() {

		$this->data = [];
		
		$this->query = "SELECT * from tab_empresa";

		parent::Execute();

	}

	public function Update(array $data) {

		$field = $data['field'];
		
		$this->data = [
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_empresa set $field = :value";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$valida_cpf_cnpj = new ValidaCPFCNPJ($row['cnpj']);

		$row['cnpj_formatted'] = $valida_cpf_cnpj->formata();

		$row['ie_formatted'] = $row['ie'];

		$row['telefone_formatted'] = FormatTel($row['telefone']);

		$row['celular_formatted'] = FormatTel($row['celular']);

		$row['cep_formatted'] = FormatCEP($row['cep']);

		return $row;
	}
}