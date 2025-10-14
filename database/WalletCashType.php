<?php

namespace database;

class WalletCashType extends Connection {

	public function Create($id_wallet, $walletcashtype) {

        $this->data = [
			'id_wallet' => $id_wallet,
			'walletcashtype' => $walletcashtype
		];

        $this->query = "INSERT INTO tab_walletcashtype 
						(id_wallet, walletcashtype) VALUES (:id_wallet, :walletcashtype)";
        
        parent::Execute();
        return parent::lastInsertId();
    }

	public function Read($id_walletcashtype) {
         
        $this->data = [
            "id_walletcashtype" => $id_walletcashtype
        ];

        $this->query = "SELECT * from tab_walletcashtype 
                        WHERE id_walletcashtype = :id_walletcashtype";

         parent::Execute();
    }

	public function Update(array $data) {

        $field = $data['field'];
		
		$this->data = [
			"id_walletcashtype" => $data['id_walletcashtype'],
			"value" => $data['value'],
		];

        $this->query = "UPDATE tab_walletcashtype
                        set $field = :value 
                        where id_walletcashtype = :id_walletcashtype";

        parent::Execute();
    }

	public function Delete($id_walletcashtype) {

		$this->data = [
			'id_walletcashtype' => $id_walletcashtype
		];

		$this->query = "DELETE from tab_walletcashtype 
						WHERE id_walletcashtype = :id_walletcashtype";	
		
		parent::Execute();
		
		return parent::rowCount();
	}

	public function isCashtypeInUse($id_walletcashtype) {

		$this->data = [
			"id_walletcashtype" => $id_walletcashtype
		];

		$this->query = "SELECT * from tab_walletdespesa
						WHERE id_walletcashtype = :id_walletcashtype";

		parent::Execute();

		return (parent::rowCount() > 0);
	}

	public function getList($id_wallet) {

		$this->data = [

			"id_wallet" => $id_wallet
		];

		$this->query = "SELECT * from tab_walletcashtype 
						where id_wallet = :id_wallet
						order by walletcashtype";
		
		parent::Execute();
	}	
}