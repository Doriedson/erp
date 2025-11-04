<?php

namespace App\Legacy;

use Escpos\Printer;

class Pdv extends Connection {

	public function Read($id_pdv) {

		$this->data = [
			"id_pdv" => $id_pdv
		];

		$this->query = "SELECT tab_pdv.*, tab_impressora.descricao as printer_desc from tab_pdv
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_pdv.id_impressora
						WHERE id_pdv = :id_pdv";

		parent::Execute();
	}

    public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_pdv" => $data['id_pdv'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_pdv set $field = :value WHERE id_pdv = :id_pdv";

		parent::Execute();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT tab_pdv.*, tab_impressora.descricao as printer_desc from tab_pdv
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_pdv.id_impressora
						ORDER BY id_pdv";

		parent::Execute();
	}

	public function TrocoiniToggleActive($id_pdv) {

		$this->data = [
			"id_pdv" => $id_pdv
		];

		$this->query = "UPDATE tab_pdv set trocoini = not trocoini where id_pdv = :id_pdv";

		parent::Execute();
	}

	public function BalancaToggleActive($id_pdv) {

		$this->data = [
			"id_pdv" => $id_pdv
		];

		$this->query = "UPDATE tab_pdv set balanca = not balanca where id_pdv = :id_pdv";

		parent::Execute();
	}

	public function ImpressoraToggleActive($id_pdv) {

		$this->data = [
			"id_pdv" => $id_pdv
		];

		$this->query = "UPDATE tab_pdv set impressora = not impressora where id_pdv = :id_pdv";

		parent::Execute();
	}

	public function GavetaToggleActive($id_pdv) {

		$this->data = [
			"id_pdv" => $id_pdv
		];

		$this->query = "UPDATE tab_pdv set gaveteiro = not gaveteiro where id_pdv = :id_pdv";

		parent::Execute();
	}

	public function get($hash) {

		$this->data = [
			"hash" => $hash
		];

		$this->query = "SELECT * from tab_pdv
						WHERE hash = :hash";

		parent::Execute();
	}

    public static function FormatFields($row) {

		$tplPdv = new View("pdv_config");

		$row['bt_trocoini'] = ($row['trocoini'] == 1)?"checked":"";

		$row['bt_balanca'] = "";

		if ($row['balanca'] == 1) {

			$row['bt_balanca'] = "checked";
		// 	$row['bt_balanca'] = $tplPdv->getContent($row, "EXTRA_BLOCK_BALANCA_ON");

		// } else {

		// 	$row['bt_balanca'] = $tplPdv->getContent($row, "EXTRA_BLOCK_BALANCA_OFF");
		}

		$row['bt_impressora'] = "";

		if ($row['impressora'] == 1) {

			$row['bt_impressora'] = "checked";
			// $row['bt_impressora'] = $tplPdv->getContent($row, "EXTRA_BLOCK_IMPRESSORA_ON");

		// } else {

		// 	$row['bt_impressora'] = $tplPdv->getContent($row, "EXTRA_BLOCK_IMPRESSORA_OFF");
		}

		if (key_exists("impressora_cutter", $row)) {

			if ($row['impressora_cutter'] == 1) {

				$row['bt_guilhotina'] = $tplPdv->getContent($row, "EXTRA_BLOCK_GUILHOTINA_ON");

			} else {

				$row['bt_guilhotina'] = $tplPdv->getContent($row, "EXTRA_BLOCK_GUILHOTINA_OFF");
			}
		}

		$row['bt_gaveta'] = "";

		if ($row['gaveteiro'] == 1) {

			$row['bt_gaveta'] = "checked";
		// 	$row['bt_gaveta'] = $tplPdv->getContent($row, "EXTRA_BLOCK_GAVETA_ON");

		// } else {

		// 	$row['bt_gaveta'] = $tplPdv->getContent($row, "EXTRA_BLOCK_GAVETA_OFF");
		}

		$printer = new PrinterConfig();

		$printer->getList();

		$printing_option = [
			"id_impressora" => '',
			"selected" => $row['id_impressora'] == null?"selected":"",
			"descricao" => "Sem impressora"
		];

		$printer_select = $tplPdv->getContent($printing_option,'EXTRA_BLOCK_PRINTER_OPTION');

		while ($row_printer = $printer->getResult()) {

			$row_printer['selected'] = $row['id_impressora'] == $row_printer['id_impressora']?"selected":"";

			$printer_select .= $tplPdv->getContent($row_printer,'EXTRA_BLOCK_PRINTER_OPTION');
		}

		$row['extra_block_printer_option'] = $printer_select;

		if ($row['id_impressora'] == null) {

			$row['printer_desc'] = "Sem impressora";
		}

		$cashdrawer = new CashDrawer();

		$cashdrawer->Read($row['id_gaveteiro']);

		if ($row_cashdrawer = $cashdrawer->getResult()) {

			$row['gaveteiro_desc'] = $row_cashdrawer['descricao'];

		} else {

			$row['gaveteiro_desc'] = "Sem gaveteiro";
		}

		$cashdrawer->getList();

		$cashdrawer_option = [
			"id_gaveteiro" => '',
			"selected" => $row['id_gaveteiro'] == null?"selected":"",
			"descricao" => "Sem gaveteiro"
		];

		$cashdrawer_select = $tplPdv->getContent($cashdrawer_option,'EXTRA_BLOCK_CASHDRAWER_OPTION');

		while ($row_cashdrawer = $cashdrawer->getResult()) {

			$row_cashdrawer['selected'] = $row['id_gaveteiro'] == $row_cashdrawer['id_gaveteiro']?"selected":"";

			$cashdrawer_select .= $tplPdv->getContent($row_cashdrawer,'EXTRA_BLOCK_CASHDRAWER_OPTION');
		}

		$row['extra_block_cashdrawer_option'] = $cashdrawer_select;

        return $row;
    }
}