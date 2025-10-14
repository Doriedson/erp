<?php

namespace database;

class BillsToPay extends Connection {

	public function Create(array $data) {

		$this->data = [
			"id_entidade" => $data['id_entidade'],
			"vencimento" => $data['vencimento'],
			"id_contasapagarsetor" => $data['id_contasapagarsetor'],
			"descricao" => $data['descricao'],
			"valor" => $data['valor'],
		];

		if ($data['pago']) {
			$this->data["valorpago"] = $data['valor'];
			$this->data["datapago"] = $data['vencimento'];
		}

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_contasapagar
						($fields) VALUES ($places)";
		
		parent::Execute();

		return parent::lastInsertId();
	}	

	public function Read($id_contasapagar) {

		$this->data = [
			"id_contasapagar" => $id_contasapagar
		];

		$this->query = "SELECT tab_contasapagar.*, tab_contasapagarsetor.contasapagarsetor,
						tab_entidade.nome
						FROM tab_contasapagar
						INNER JOIN tab_contasapagarsetor 
						ON tab_contasapagarsetor.id_contasapagarsetor = tab_contasapagar.id_contasapagarsetor
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_contasapagar.id_entidade
						WHERE tab_contasapagar.id_contasapagar = :id_contasapagar";

		parent::Execute();
	}

	public function getPendingList() {

		$this->data = [];

		$this->query = "SELECT tab_contasapagar.*, tab_contasapagarsetor.contasapagarsetor,
						tab_entidade.nome
						FROM tab_contasapagar
						INNER JOIN tab_contasapagarsetor 
						ON tab_contasapagarsetor.id_contasapagarsetor = tab_contasapagar.id_contasapagarsetor
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_contasapagar.id_entidade
						WHERE datapago IS null
						ORDER BY tab_contasapagar.vencimento";
		
		parent::Execute();

	}

	public function SearchByDate($date, $fieldSearch, $descricao, $setor, $ordered = false) {

		$this->SearchByDateInterval($date, $date, $fieldSearch, $descricao, $setor, $ordered);
	}	

	public function SearchByDateInterval($datestart, $dateend, $fieldSearch, $descricao, $id_contasapagarsetor, $ordered = false) {
		
		$this->data = [
						"datastart" => $datestart . " 00:00:00",
						"dataend" => $dateend . " 23:59:59",
					];

		switch ($fieldSearch) {

			case 0:
				$field = "tab_contasapagar.datacad";
				break;

			case 1:
				$field = "tab_contasapagar.datapago";
				break;

			default:
				$field = "tab_contasapagar.vencimento";
		}

		$order = "";

		if ($ordered) {

			$order = "tab_contasapagarsetor.contasapagarsetor,";
		}

		$this->query = "SELECT tab_contasapagar.*, tab_contasapagarsetor.contasapagarsetor,
						tab_entidade.nome
						FROM tab_contasapagar
						INNER JOIN tab_contasapagarsetor 
						ON tab_contasapagarsetor.id_contasapagarsetor = tab_contasapagar.id_contasapagarsetor
						INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_contasapagar.id_entidade
						WHERE $field between :datastart AND :dataend";

		if ($descricao != null) {

			$this->data["descricao"] = '%' . str_replace('+', '%', $descricao) . '%';

			$this->query .= " AND tab_contasapagar.descricao like :descricao";
		}

		if ($id_contasapagarsetor != null) {

			$this->data["id_contasapagarsetor"] = $id_contasapagarsetor;

			$this->query .= " AND tab_contasapagar.id_contasapagarsetor = :id_contasapagarsetor";
		}

		$this->query .= " ORDER BY $order tab_contasapagar.vencimento";


		parent::Execute();
	}

	public function Delete($id_contasapagar) {

		$this->data = [
			'id_contasapagar' => $id_contasapagar
		];

		$this->query = "DELETE from tab_contasapagar 
						WHERE id_contasapagar = :id_contasapagar";	
		
		parent::Execute();
		
		return parent::rowCount();
	}
	
	public function Update(array $data) {

		$field = $data["field"];
		
		$this->data = [
			"id_contasapagar" => $data['id_contasapagar'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_contasapagar 
				SET $field = :value 
				WHERE id_contasapagar = :id_contasapagar";

		parent::Execute();
	}

	public function setPayment($data) {

		$this->data = $data;

		$this->query = "UPDATE tab_contasapagar 
						SET valorpago = :valorpago, datapago = :datapago 
						WHERE id_contasapagar = :id_contasapagar";

		parent::Execute();
	}

	public function hasSectorInUse($id_contasapagarsetor) {
		
		$this->data = [
			"id_contasapagarsetor" => $id_contasapagarsetor,
		];

		$this->query = "SELECT * FROM tab_contasapagar
						WHERE id_contasapagarsetor = :id_contasapagarsetor
						LIMIT 1";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public static function FormatFields($row) {

		$tplBillsToPay = new View('templates/bills_to_pay');

		$row['datacad_formatted'] = date_format( date_create($row['datacad']), 'd/m/Y');
		$row['datacad'] = date_format( date_create($row['datacad']), 'Y-m-d');
	
		$row['vencimento_formatted'] = date_format( date_create($row['vencimento']), 'd/m/Y');
		$row['vencimento'] = date_format( date_create($row['vencimento']), 'Y-m-d');
		
		if ($row['datapago']) {

			$row['datapago_formatted'] = date_format( date_create($row['datapago']), 'd/m/Y');
			$row['valorpago_formatted'] = number_format($row['valorpago'],2,',','.');
			$row['datapago'] = date_format( date_create($row['datapago']), 'Y-m-d');

			// $row['extra_block_billstopay_status'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_STATUS_CLOSED");
			// $row['extra_block_billstopay_payment_button'] = "";
			$row['extra_block_billstopay_valorpago'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_VALORPAGO");
			$row['extra_block_billstopay_datapago'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_DATAPAGO");

			$row['extra_block_billstopay_payment'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAYMENT_DONE");

		} else {

			// $row['extra_block_billstopay_status'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_STATUS_OPENED");
			// $row['extra_block_billstopay_payment_button'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAYMENT_BUTTON");
			$row['extra_block_billstopay_valorpago'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_NULL");
			$row['extra_block_billstopay_datapago'] = $tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_NULL");

			$row['extra_block_billstopay_payment'] = ""; //$tplBillsToPay->getContent($row, "EXTRA_BLOCK_BILLSTOPAY_PAYMENT_PENDING");
		}

		$row['valor_formatted'] = number_format($row['valor'],2,',','.');
	
		$billsSector = new BillsToPaySector();
	
		$billsSector->getList();
		
		$sectorList = "";
		
		while ($row_sector = $billsSector->getResult()) {
			
			$selected = ($row_sector['id_contasapagarsetor'] == $row['id_contasapagarsetor'])? "selected" : "";

			$sectorList.= "<option value='" . $row_sector['id_contasapagarsetor'] . "' $selected>" . $row_sector['contasapagarsetor'] . "</option>";
		}

		$row['setor_lista'] = $sectorList;

		return $row;
	}
}