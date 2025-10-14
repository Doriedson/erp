<?php

namespace database;

class ProductUnit extends Connection {

	public function getList() {

		$this->data = [];
		
		$this->query = "select * from tab_produtounidade order by produtounidade";

		parent::Execute();		
	}
}