<?php

namespace App\Legacy;

class SaleOrderItemKit extends Connection {

	public function Create($id_venda, $id_vendaitem, $id_produto, $qtd, $preco) {

		$this->data = [
            'id_venda' => $id_venda,
            'id_vendaitem' => $id_vendaitem,
            'id_produto' => $id_produto,
            'qtd' => $qtd,
            'preco' => $preco,
        ];


		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_vendaitemkit ($fields) VALUES ($places)";

		parent::Execute();
	}
}