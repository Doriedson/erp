<?php

namespace App\Legacy;

class WalletSector extends Connection {

	public function Create($id_wallet, $walletsector) {

		$this->data = [
			'id_wallet' => $id_wallet,
			'walletsector' => $walletsector
		];

        $this->query = "INSERT INTO tab_walletsector
						(id_wallet, walletsector) VALUES (:id_wallet, :walletsector)";

        parent::Execute();
        return parent::lastInsertId();
    }

	public function Read($id_walletsector) {

        $this->data = [
            "id_walletsector" => $id_walletsector
        ];

        $this->query = "SELECT * from tab_walletsector
                        WHERE id_walletsector = :id_walletsector";

         parent::Execute();
    }

	public function Update(array $data) {

        $field = $data['field'];

		$this->data = [
			"id_walletsector" => $data['id_walletsector'],
			"value" => $data['value'],
		];

        $this->query = "UPDATE tab_walletsector
                        set $field = :value
                        where id_walletsector = :id_walletsector";

        parent::Execute();
    }

	public function Delete($id_walletsector) {

		$this->data = [
			'id_walletsector' => $id_walletsector
		];

		$this->query = "DELETE from tab_walletsector
						WHERE id_walletsector = :id_walletsector";

		parent::Execute();

		return parent::rowCount();
	}

	public function isSectorInUse($id_walletsector) {

		$this->data = [
			"id_walletsector" => $id_walletsector
		];

		$this->query = "SELECT * from tab_walletdespesa
		WHERE id_walletsector = :id_walletsector";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public function getList($id_wallet) {

		$this->data = [
			"id_wallet" => $id_wallet
		];

		$this->query = "SELECT * from tab_walletsector
						WHERE id_wallet = :id_wallet
						ORDER BY walletsector";

		parent::Execute();
	}
}