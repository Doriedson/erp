<?php

namespace database;

class ProductSector extends Connection {

	public function getList() {

		$this->data = [];

		$this->query = "select * from tab_produtosetor order by produtosetor";

		parent::Execute();		
	}

	public function getListWaiter() {

		$sector_list = "";

		$this->data = [];

		$this->query = "SELECT * from tab_produtosetor
						WHERE garcom = 1
						ORDER BY produtosetor";

		parent::Execute();	

		if ($row = parent::getResult()) {

			$tplSector = new View('templates/waiter_sector');

			do {

				$row['extra_block_product'] = "";
				// $row['expandable'] = 'hidden';
				$row['sector_bt_expand'] = $tplSector->getContent([], "EXTRA_BLOCK_SECTOR_BT_EXPAND");
				$sector_list.= $tplSector->getContent($row, "EXTRA_BLOCK_SECTOR");
	
			} while ($row = parent::getResult());
		}	

		return $sector_list;
	}

	public function getDigitalMenu() {

		$this->data = [];

		$this->query = "SELECT * from tab_produtosetor
						WHERE cardapio_setor = 1
						ORDER BY produtosetor";

		parent::Execute();
	}

	public function Create($sector) {

		$this->data = ['produtosetor' => $sector];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produtosetor ($fields) VALUES ($places)";

		parent::Execute();
		return parent::lastInsertId();
	}	

	public function Read($id_sector) {

		$this->data = ["id_produtosetor" => $id_sector];

		$this->query = "SELECT * FROM tab_produtosetor 
						WHERE id_produtosetor = :id_produtosetor";

		parent::Execute();
	}

	public function Update(array $data) {

		$field = $data['field'];
		
		$this->data = [
			"id_produtosetor" => $data['id_produtosetor'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_produtosetor set $field = :value where id_produtosetor = :id_produtosetor";

		parent::Execute();
	}

	public function Delete($id_sector) {

		$this->data = [
			"id_produtosetor" => $id_sector,
		];

		$this->query = "DELETE FROM tab_produtosetor 
			WHERE id_produtosetor = :id_produtosetor";

		parent::Execute();
		return parent::rowCount();
	}

	public function ToggleGarcom($id_produtosetor) {

		$this->data = [
			"id_produtosetor" => $id_produtosetor,
		];

		$this->query = "UPDATE tab_produtosetor 
						SET garcom = not garcom
						WHERE id_produtosetor = :id_produtosetor";

		parent::Execute();
	}

	public function ToggleMenuDigital($id_produtosetor) {

		$this->data = [
			"id_produtosetor" => $id_produtosetor,
		];

		$this->query = "UPDATE tab_produtosetor 
						SET cardapio_setor = not cardapio_setor
						WHERE id_produtosetor = :id_produtosetor";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$tplSector = new View('templates/product');

		if ($row['garcom'] == 1) {

			$row['garcom'] = "checked";

		} else {

			$row['garcom'] = "";
		}

		if ($row['cardapio_setor'] == 1) {

			$row['cardapio_setor'] = "checked";

		} else {

			$row['cardapio_setor'] = "";
		}

		return $row;
	}
}