<?php

namespace database;

class FidelityProgram extends Connection {

	const RULE_EQUAL = 0;
	const RULE_LESS_THAN = 1;
	const RULE_LESS_EQUAL = 2;
	const RULE_GREATER_THAN = 3;
	const RULE_GREATER_EQUAL = 4;

	public function Create() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_fidelidaderegra
						ORDER BY prioridade
						DESC LIMIT 1";

		parent::Execute();

		$this->data = [
			"prioridade" => 0
		];

		if ($row = parent::getResult()) {

			$this->data['prioridade'] = $row['prioridade'] + 1;
		}

		$this->query = "INSERT INTO tab_fidelidaderegra
						(prioridade) values (:prioridade)";

		parent::Execute();

		return parent::lastInsertId();
    }

	public function Read($id_fidelidaderegra) {

		$this->data = [
			"id_fidelidaderegra" => $id_fidelidaderegra
		];

		$this->query = "SELECT * FROM tab_fidelidaderegra
						WHERE id_fidelidaderegra = :id_fidelidaderegra";

		parent::Execute();
    }

    public function getDays() {

		$this->data = [];
		
        $this->query = "SELECT * FROM tab_fidelidade";

		parent::Execute();
    }

	public function setDays($days) {

		$this->data = [
			"dias_compra" => $days
		];

        $this->query = "UPDATE tab_fidelidade
						SET dias_compra = :dias_compra";

		parent::Execute();
    }

    public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_fidelidaderegra
						ORDER BY prioridade";

		parent::Execute();
    }

	public function Delete($id_fidelidaderegra) {

		$this->Read($id_fidelidaderegra);

		if ($row = parent::getResult()) {

			$this->data = [];
			
			$this->query = "UPDATE tab_fidelidaderegra
							SET prioridade = prioridade - 1
							WHERE prioridade > " . $row['prioridade'];

			parent::Execute();
		}

		$this->data = [
			"id_fidelidaderegra" => $id_fidelidaderegra
		];

		$this->query = "DELETE FROM tab_fidelidaderegra
						WHERE id_fidelidaderegra = :id_fidelidaderegra";

		parent::Execute();

		return parent::rowCount();
	}

	public static function getRules() {

		$rule[self::RULE_EQUAL] = "Se igual a";
		$rule[self::RULE_LESS_THAN] = "Se menor que";
		$rule[self::RULE_LESS_EQUAL] = "Se menor ou igual a";
		$rule[self::RULE_GREATER_THAN] = "Se maior que";
		$rule[self::RULE_GREATER_EQUAL] = "Se maior ou igual a";

		return $rule;
	}

	public function RuleUp($id_fidelidaderegra) {

		$this->getList();

		while ($row = $this->getResult()) {

			$rules[] = $row['id_fidelidaderegra'];

			if ($row['id_fidelidaderegra'] == $id_fidelidaderegra) {

				$position = count($rules) - 1;
			}
		}

		if ($position == 0) {
		
			return;
		}

		$swap = $rules[$position - 1];
		$rules[$position - 1] = $rules[$position];
		$rules[$position] = $swap;

		$this->data = [];
		
		foreach ($rules as $prioridade => $id_prioridade) {

			$this->query = "UPDATE tab_fidelidaderegra
							SET prioridade = $prioridade
							WHERE id_fidelidaderegra = $id_prioridade";

			parent::Execute();
		}
	}
	
	public function RuleDown($id_fidelidaderegra) {

		$this->getList();

		while ($row = $this->getResult()) {

			$rules[] = $row['id_fidelidaderegra'];

			if ($row['id_fidelidaderegra'] == $id_fidelidaderegra) {

				$position = count($rules) - 1;
			}
		}

		if ($position == count($rules) - 1) {
		
			return;
		}

		$swap = $rules[$position + 1];
		$rules[$position + 1] = $rules[$position];
		$rules[$position] = $swap;

		$this->data = [];

		foreach ($rules as $prioridade => $id_prioridade) {

			$this->query = "UPDATE tab_fidelidaderegra
							SET prioridade = $prioridade
							WHERE id_fidelidaderegra = $id_prioridade";

			parent::Execute();
		}
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_fidelidaderegra" => $data['id_fidelidaderegra'],
			"value"	 => $data['value'],
		];

		$this->query = "UPDATE tab_fidelidaderegra 
						set $field = :value 
						where id_fidelidaderegra = :id_fidelidaderegra";

		parent::Execute();

		return parent::rowCount();
	}

	public static function FormatFields($row) {

		$row['valor_formatted'] = number_format($row['valor'],2,',','.');
		$row['desconto_formatted'] = number_format($row['desconto'],2,',','.');
	
		$rules = self::getRules();
	
		$row['regra'] = $rules[$row['condicao']];

		$tplFidelity = new View("templates/fidelity_program");

		$row['option'] = "";

		foreach ($rules as $key => $rule) {

			$data = [
				'id_regra' => $key,
				'regra' => $rule,
				'selected' => ($key == $row['condicao'])? "selected": "",
			];

			$row['option'].= $tplFidelity->getContent($data, "EXTRA_BLOCK_CONDICAO_FORM_OPTION");

		}
	
		return $row;
	}
}