<?php

namespace App\Legacy;

class CashAdd extends Connection {

	public function Read($id_caixareforco) {

		$this->data = ['id_caixareforco' => $id_caixareforco];

		$this->query = "SELECT tab_caixareforco.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixareforco
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixareforco.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixareforco.id_especie
						WHERE id_caixareforco =  :id_caixareforco";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_caixareforco" => $data['id_caixareforco'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_caixareforco
						SET $field = :value
						WHERE id_caixareforco = :id_caixareforco";

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

		$this->query = "SELECT tab_caixareforco.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixareforco
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixareforco.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixareforco.id_especie
						WHERE data between :datestart AND :dateend
						ORDER BY data";

		parent::Execute();
	}

	public function getTotalByCashier($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT IFNULL(sum(valor), 0) as total
						FROM tab_caixareforco
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function ListByCashier($id_caixa) {

		$this->data = ['id_caixa' => $id_caixa];

		$this->query = "SELECT tab_caixareforco.*, tab_entidade.nome, tab_especie.especie
						FROM tab_caixareforco
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_caixareforco.id_entidade
						INNER JOIN tab_especie on tab_especie.id_especie = tab_caixareforco.id_especie
						WHERE id_caixa = :id_caixa";

		parent::Execute();
	}

	public function Check($id_caixareforco) {

		$this->data = ['id_caixareforco' => $id_caixareforco];

		$this->query = "UPDATE tab_caixareforco
						SET conferido = 1
						WHERE id_caixareforco = :id_caixareforco";

		parent::Execute();
	}

	public static function FormatFields($row) {

		// $tplCashAdd = new View('report_cashadd');

		$row['data_formatted'] = date_format(date_create($row['data']),'d/m/Y H:i');

		$row['valor_formatted'] = number_format($row['valor'], 2, ",", ".");

		if ($row['conferido'] == 1) {

			$row['conferido'] = "bgcolor-green";
			$row['cashadd_checked'] = "";
			$row['cashadd_notchecked'] = "hidden";

		} else {

			$row['conferido'] = "bgcolor-red";
			$row['cashadd_checked'] = "hidden";
			$row['cashadd_notchecked'] = "";
		}
		// $paymentKind = new PaymentKind();

		// $paymentKind->getList();

		// $row['extra_block_option'] = "";

		// if ($rowPay = $paymentKind->getResult()) {

		// 	$tplCashAdd = new View('report_cashadd');

		// 	$option = "";

		// 	do {

		// 		if ($rowPay['ativo'] == 1) {

		// 			if ($rowPay['id_especie'] == $row['id_especie']) {

		// 				$rowPay['selected'] = "selected";

		// 			} else {

		// 				$rowPay['selected'] = "";
		// 			}

		// 			$option.= $tplCashAdd->getContent($rowPay, "EXTRA_BLOCK_OPTION");
		// 		}

		// 	} while ($rowPay = $paymentKind->getResult());

		// 	$row['extra_block_option'] = $option;
		// }

		return $row;
	}
}