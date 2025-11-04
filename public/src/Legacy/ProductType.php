<?php

namespace App\Legacy;

class ProductType extends Connection {

    const PRODUTO = 1;
    const COMPOSICAO = 2;
    const KIT = 3;

    public function Read($id_produtotipo) {

        $this->data = [
			"id_produtotipo" => $id_produtotipo,
		];

		$this->query = "SELECT * FROM tab_produtotipo
			WHERE id_produtotipo = :id_produtotipo";

		parent::Execute();
    }
}