<?php

namespace database;

class Cashier extends Connection {

	public function Read($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT tab_caixa.*, tab_pdv.descricao FROM tab_caixa
						INNER JOIN tab_pdv on tab_pdv.id_pdv = tab_caixa.id_pdv
						WHERE tab_caixa.id_caixa =  :id_caixa";

		parent::Execute();
	}

	public function getOperator($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT tab_entidade.nome
						FROM tab_caixa
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixa.id_entidade
						WHERE tab_caixa.id_caixa =  :id_caixa";

		parent::Execute();
	}

	public function UpdateTrocoIni($id_caixa, $troco) {

		$this->data = [
			'id_caixa' => $id_caixa,
			'trocoini' => $troco,
		];

		$this->query = "UPDATE tab_caixa
						SET trocoini = :trocoini
						WHERE id_caixa = :id_caixa";

		parent::Execute();

	}

	public function UpdateTrocoFim($id_caixa, $troco) {

		$this->data = [
			'id_caixa' => $id_caixa,
			'trocofim' => $troco,
		];

		$this->query = "UPDATE tab_caixa
						SET trocofim = :trocofim
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function UpdateObs($id_caixa, $obs) {

		$this->data = [
			'id_caixa' => $id_caixa,
			'obs' => $obs,
		];

		$this->query = "UPDATE tab_caixa
						SET obs = :obs
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function Close($id_caixa) {

		$cashChange = new CashChange();

		$cashChange->getTotal($id_caixa);

		if ($row = $cashChange->getResult()) {

			$this->data = [
				'id_caixa' => $id_caixa,
				'trocofim' => $row['troco'],
			];

			$this->query = "UPDATE tab_caixa
							SET datafim = now(), trocofim = :trocofim
							WHERE id_caixa = :id_caixa";

			parent::Execute();

		} else {

			Notifier::Add("Erro ao carregar dados do fundo de caixa.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}


	}

	public static function FormatFields($row) {

		$row['trocoini_formatted'] = number_format($row['trocoini'], 2, ",", ".");
		$row['trocofim_formatted'] = number_format($row['trocofim'], 2, ",", ".");

		$row["tooltip"] = "";
		$row["icon_tooltip"] = "fa-regular fa-comment";

		if ($row["obs"] != ""){

			$row["tooltip"] = "tooltip";
			$row["icon_tooltip"] = "fa-solid fa-comment-dots";
		}

		return $row;
	}
}