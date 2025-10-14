<?php

namespace database;

class SaleOrderAddress extends Connection {

	public function Read($id_venda) {

        $this->data = [
			"id_venda" => $id_venda,
		];

		$this->query = "SELECT * FROM tab_vendaendereco
                        WHERE id_venda = :id_venda";

		parent::Execute();
    }

	public function Create($data) {

		$this->data = [
			"id_venda" => $data['id_venda'],
			"nickname" => $data['nickname'],
			"logradouro" => $data['logradouro'],
			"numero" => $data['numero'],
			"complemento" => $data['complemento'],
			"bairro" => $data['bairro'],
			"cidade" => $data['cidade'],
			"uf" => $data['uf'],
			"cep" => $data['cep'],
			"obs" => $data['obs'],
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_vendaendereco
						($fields) VALUES ($places)";

		parent::Execute();
	}

	public function Delete($id_venda) {

		$this->data = [
			'id_venda' => $id_venda
		];

		$this->query = "DELETE from tab_vendaendereco
						WHERE id_venda = :id_venda";

		parent::Execute();
	}

	public static function getSaleAddress($id_venda) {

		$saleAddress = new SaleOrderAddress();

		$saleAddress->Read($id_venda);

		$tplSale = new View('templates/sale_order');

		if ($rowAddress = $saleAddress->getResult()) {

			$rowAddress = EntityAddress::FormatFields($rowAddress);

			return $tplSale->getContent($rowAddress, "EXTRA_BLOCK_SALEADDRESS");

		} else {

			return $tplSale->getContent([], "EXTRA_BLOCK_NO_SALEADDRESS");
		}
	}
}