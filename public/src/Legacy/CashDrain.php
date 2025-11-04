<?php

namespace App\Legacy;

class CashDrain extends Connection {

	public function Read($id_caixasangria) {

		$this->data = ['id_caixasangria' => $id_caixasangria];

		$this->query = "SELECT tab_caixasangria.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixasangria
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixasangria.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixasangria.id_especie
						WHERE id_caixasangria =  :id_caixasangria";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_caixasangria" => $data['id_caixasangria'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_caixasangria
						SET $field = :value
						WHERE id_caixasangria = :id_caixasangria";

		parent::Execute();

		return parent::rowCount();
	}

	public function SearchByDate($date) {

		$this->SearchByDateInterval($date, $date);
	}

	public function SearchByDateInterval($datestart, $dateend) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59"
		];

		$this->query = "SELECT tab_caixasangria.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixasangria
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixasangria.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixasangria.id_especie
						WHERE data between :datestart AND :dateend
						ORDER BY data";

		parent::Execute();
	}

	public function getTotalByCashier($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT IFNULL(sum(valor), 0) as total
						FROM tab_caixasangria
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function ListByCashier($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT tab_caixasangria.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixasangria
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixasangria.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixasangria.id_especie
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function Check($id_caixasangria) {

		$this->data = ['id_caixasangria' => $id_caixasangria];

		$this->query = "UPDATE tab_caixasangria
						SET conferido = 1
						WHERE id_caixasangria = :id_caixasangria";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$tplCashdrain = new View('report_cashdrain');

		$row['data_formatted'] = date_format(date_create($row['data']),'d/m/Y H:i');

		$row['valor_formatted'] = number_format($row['valor'], 2, ",", ".");

		if ($row['conferido'] == 1) {

			$row['conferido'] = "bgcolor-green";
			$row['cashdrain_checked'] = "";
			$row['cashdrain_notchecked'] = "hidden";

		} else {

			$row['conferido'] = "bgcolor-red";
			$row['cashdrain_checked'] = "hidden";
			$row['cashdrain_notchecked'] = "";
		}

		$paymentKind = new PaymentKind();

		$paymentKind->getList();

		$row['extra_block_option'] = "";

		if ($rowPay = $paymentKind->getResult()) {

			$tplCashdrain = new View('report_cashdrain');

			$option = "";

			do {

				if ($rowPay['ativo'] == 1) {

					if ($rowPay['id_especie'] == $row['id_especie']) {

						$rowPay['selected'] = "selected";

					} else {

						$rowPay['selected'] = "";
					}

					$option.= $tplCashdrain->getContent($rowPay, "EXTRA_BLOCK_OPTION");
				}

			} while ($rowPay = $paymentKind->getResult());

			$row['extra_block_option'] = $option;
		}

		return $row;
	}
}