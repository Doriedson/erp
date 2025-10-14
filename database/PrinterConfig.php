<?php

namespace database;

use database\OS;
// use Escpos\Printer;
use Escpos\PrintConnectors\WindowsPrintConnector;
// use Escpos\PrintConnectors\CupsPrintConnector;

class PrinterConfig extends Connection {

	const PRINTING_PURCHASEORDER = 1;
	const PRINTING_SALEORDER = 2;
	const PRINTING_TABLE = 3;
	const PRINTING_PRODUCTEXPIRATE = 4;

	private $coupon = [];
	private $acento1 = array("Á","Í","Ó","Ú","É","Ä","Ï","Ö","Ü","Ë","À","Ì","Ò","Ù","È","Ã","Õ","Â","Î","Ô","Û","Ê","á","í","ó","ú","é","ä","ï","ö","ü","ë","à","ì","ò","ù","è","ã","õ","â","î","ô","û","ê","Ç","ç","º");
	private $acento2 = array("A","I","O","U","E","A","I","O","U","E","A","I","O","U","E","A","O","A","I","O","U","E","a","i","o","u","e","a","i","o","u","e","a","i","o","u","e","a","o","a","i","o","u","e","C","c","o");

	public function Create($descricao, $impressora) {

		$this->data = [
			'descricao' => $descricao,
			'impressora' => $impressora,
			'guilhotina' => 0 //$guilhotina,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_impressora ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}

	public function Read($id_impressora) {

		$this->data = ["id_impressora" => $id_impressora];

		$this->query = "SELECT * FROM tab_impressora
                                WHERE id_impressora = :id_impressora";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_impressora" => $data['id_impressora'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_impressora
						SET $field = :value
						WHERE id_impressora = :id_impressora";

		parent::Execute();
	}

	public function Delete($id_impressora) {

		$this->data = [
			"id_impressora" => $id_impressora,
		];

		$this->query = "DELETE FROM tab_impressora
			WHERE id_impressora = :id_impressora";

		parent::Execute();
		return parent::rowCount();
	}

	public function getPrinting($id_impressao) {

		$this->data = ["id_impressao" => $id_impressao];

		$this->query = "SELECT tab_impressora.* FROM tab_impressao
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_impressao.id_impressora
						WHERE id_impressao = :id_impressao";

		parent::Execute();
	}

	public function PrintingRead($id_impressao) {

		$this->data = ["id_impressao" => $id_impressao];

		$this->query = "SELECT tab_impressao.*, tab_impressora.descricao as printer_desc FROM tab_impressao
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_impressao.id_impressora
						WHERE id_impressao = :id_impressao";

		parent::Execute();
	}

	public function PrintingUpdate(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_impressao" => $data['id_impressao'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_impressao
						SET $field = :value
						WHERE id_impressao = :id_impressao";

		parent::Execute();
	}

	public function getPrintingList() {

		$this->data = [];

		$this->query = "SELECT tab_impressao.*, tab_impressora.descricao as printer_desc FROM tab_impressao
						LEFT JOIN tab_impressora on tab_impressora.id_impressora = tab_impressao.id_impressora
						ORDER BY descricao";

		parent::Execute();
	}

	public function ToggleActive($id_impressora) {

		$this->data = [
			"id_impressora" => $id_impressora
		];

		$this->query = "UPDATE tab_impressora set guilhotina = not guilhotina where id_impressora = :id_impressora";

		parent::Execute();
	}

	public function ToggleBigfont($id_impressora) {

		$this->data = [
			"id_impressora" => $id_impressora
		];

		$this->query = "UPDATE tab_impressora set bigfont = not bigfont where id_impressora = :id_impressora";

		parent::Execute();
	}

	public static function getPrinters() {

		$printer = [];

		if (OS::isWindows()) {

			// $str = shell_exec('wmic printer get '.$key.' /value');

		} else {

			exec("lpstat -a | cut -d' ' -f1", $printer);
		}

		return $printer;
	}

	public function getList() {

		$this->data = [];

		$this->query = "select * from tab_impressora order by descricao";

		parent::Execute();
	}

	public static function PrintingFormatFields($row) {

		$tplPrinting = new View('templates/printing');

        $printing_option = [
			"id_impressora" => '',
			"selected" => $row['id_impressora'] == null?"selected":"",
			"descricao" => "Não imprimir"
		];

		$row['printing_option'] = $tplPrinting->getContent($printing_option,'EXTRA_BLOCK_PRINTER_OPTION');

		$printer = new PrinterConfig();

		$printer->getList();

		while ($row_printer = $printer->getResult()) {

			$row_printer['selected'] = $row['id_impressora'] == $row_printer['id_impressora']?"selected":"";

			$row['printing_option'] .= $tplPrinting->getContent($row_printer,'EXTRA_BLOCK_PRINTER_OPTION');
		}

		if ($row['id_impressora'] == null) {

			$row['printer_desc'] = "Não imprimir";
		}

		return $row;
	}

	public static function FormatFields($row) {

		$tplPrinter = new View('templates/printer');

		if ($row['guilhotina'] == 1) {

			$row['guilhotina'] = "checked";

		} else {

			$row['guilhotina'] = "";
		}

		if ($row['bigfont'] == 1) {

			$row['bigfont'] = "checked";

		} else {

			$row['bigfont'] = "";
		}

		$row['selected_1'] = "";
		$row['selected_2'] = "";

		if ($row['copies'] == 1) {

			$row['selected_1'] = "selected";

		} else {

			$row['selected_2'] = "selected";
		}

		$printers = self::getPrinters();

        $printer_select = "";

        foreach($printers as $print) {

			$data = [
				"impressora" => $print,
				"selected" => $row['impressora'] == $print?"selected":""
			];

			$printer_select.= $tplPrinter->getContent($data, "EXTRA_BLOCK_PRINTERS");
		}

		$row["printer_local_checked"] = "";
		$row["printer_local_disabled"] = "disabled";
		$row["printer_share_desc"] = "";
		$row["printer_share_checked"] = "";
		$row["printer_share_disabled"] = "disabled";
		$row["printer_ip_desc"] = "";
		$row["printer_ip_checked"] = "";
		$row["printer_ip_disabled"] = "disabled";

		if (preg_match(WindowsPrintConnector::REGEX_SMB, $row['impressora']) == 1) {

			$row["printer_share_checked"] = "checked";
			$row["printer_share_desc"] = $row['impressora'];
			$row["printer_share_disabled"] = "";

		} else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?::(\d{1,5}))?\z/', $row["impressora"]) == 1) {

			$row["printer_ip_checked"] = "checked";
			$row["printer_ip_desc"] = $row['impressora'];
			$row["printer_ip_disabled"] = "";

		} else {

			$row["printer_local_checked"] = "checked";
			$row["printer_local_disabled"] = "";
		}

		$row['extra_block_printers'] = $printer_select;

		return $row;
	}
}