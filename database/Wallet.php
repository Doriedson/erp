<?php

namespace database;

class Wallet extends Connection {

    public function Create() {

        $this->data = [
            "id_entidade" => $GLOBALS['authorized_id_entidade']
        ];

        $this->query = "INSERT INTO tab_wallet (id_entidade, wallet) VALUES (:id_entidade, 'Nova Carteira')";

        parent::Execute();
        return parent::lastInsertId();
    }

    public function Read($id_wallet) {

        $this->data = [
            "id_wallet" => $id_wallet
        ];

        $this->query = "SELECT * FROM tab_wallet
                        WHERE id_wallet = :id_wallet and deleted = 0
                        ORDER BY wallet";

         parent::Execute();
    }

    public function Delete($id_wallet) {

        $this->data = [
            "id_wallet" => $id_wallet
        ];

		$this->query = "UPDATE tab_wallet
                        SET deleted = 1
                        WHERE id_wallet = :id_wallet";

		parent::Execute();
		return parent::rowCount();
    }

    public function AddDespesa(array $data) {

        $this->data = $data;
        $fields = implode(', ', array_keys($this->data));
        $places = ':' . implode(", :", array_keys($this->data));

        $this->query = "INSERT INTO tab_wallet ($fields) VALUES ($places)";

        parent::Execute();
        return parent::lastInsertId();
    }

	public function DespesaSearchByDate($id_wallet, $datestart, $dateend, $id_walletcashtype, $id_walletsector) {

		$this->data = [
            "id_wallet" => $id_wallet,
			'datestart' => $datestart,
			'dateend' => $dateend,
		];

        $this->query = "SELECT *, tab_walletdespesa.obs as obs_despesa FROM tab_walletdespesa
                            INNER JOIN tab_walletsector on tab_walletsector.id_walletsector = tab_walletdespesa.id_walletsector
                            INNER JOIN tab_walletcashtype on tab_walletcashtype.id_walletcashtype = tab_walletdespesa.id_walletcashtype
                            INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_walletdespesa.id_entidade
                            WHERE datapago BETWEEN DATE_FORMAT(:datestart, '%Y-%m-01') AND LAST_DAY(:dateend)
                            AND tab_walletdespesa.id_wallet = :id_wallet ";


		if (!is_null($id_walletcashtype)) {

            $this->data["id_walletcashtype"] = $id_walletcashtype;

            $this->query .= "AND tab_walletdespesa.id_walletcashtype = :id_walletcashtype ";
        }

        if (!is_null($id_walletsector)) {

            $this->data["id_walletsector"] = $id_walletsector;

            $this->query .= "AND tab_walletdespesa.id_walletsector = :id_walletsector ";
        }

        $this->query .= "ORDER BY datapago";

        parent::Execute();
	}

    public function DespesaFutura($id_wallet) {

		$this->data = [
            "id_wallet" => $id_wallet
        ];

        $this->query = "SELECT *, tab_walletdespesa.obs as obs_despesa FROM tab_walletdespesa
                            INNER JOIN tab_walletsector on tab_walletsector.id_walletsector = tab_walletdespesa.id_walletsector
                            INNER JOIN tab_walletcashtype on tab_walletcashtype.id_walletcashtype = tab_walletdespesa.id_walletcashtype
                            INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_walletdespesa.id_entidade
                            WHERE datapago is null
                            AND tab_walletdespesa.id_wallet = :id_wallet ";

        $this->query .= "ORDER BY data";

        parent::Execute();
	}

    public function ReceitaSearchByDate($id_wallet, $datestart, $dateend) {

		$this->data = [
            "id_wallet" => $id_wallet,
			'datestart' => $datestart,
			'dateend' => $dateend,
		];

        $this->query = "SELECT * FROM tab_walletreceita
                            INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_walletreceita.id_entidade
                            WHERE data BETWEEN DATE_FORMAT(:datestart, '%Y-%m-01') AND LAST_DAY(:dateend)
                            AND tab_walletreceita.id_wallet = :id_wallet ORDER BY data";

        parent::Execute();
	}

    public function Update(array $data) {

        $field = $data['field'];

		$this->data = [
			"id_wallet" => $data['id_wallet'],
			"value" => $data['value'],
		];

        $this->query = "UPDATE tab_wallet
                        set $field = :value
                        where id_wallet = :id_wallet";

        parent::Execute();
    }

    public function getWallets() {

        $this->data = [
            "id_entidade" => $GLOBALS['authorized_id_entidade']
        ];

        $this->query = "SELECT * FROM tab_wallet
                        WHERE id_entidade = :id_entidade and deleted = 0";

         parent::Execute();
    }

    public function getWalletsShared() {

        $this->data = [
            "id_entidade" => $GLOBALS['authorized_id_entidade']
        ];

        $this->query = "SELECT tab_wallet.*, tab_entidade.nome FROM tab_wallet
                        INNER JOIN tab_walletshare on tab_walletshare.id_wallet = tab_wallet.id_wallet
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_wallet.id_entidade
                        WHERE tab_walletshare.id_entidade = :id_entidade and deleted = 0";

         parent::Execute();
    }

    public function getWallet($id_wallet) {

        $this->data = [
            "id_wallet" => $id_wallet
        ];

        $this->query = "SELECT * FROM tab_wallet
                        WHERE id_wallet = :id_wallet";

         parent::Execute();
    }

    public function isMyWallet($id_wallet) {

        $ret = false;

        $this->data = [
            "id_entidade" => $GLOBALS['authorized_id_entidade'],
            "id_wallet" => $id_wallet
        ];

        $this->query = "SELECT * FROM tab_wallet
                        WHERE id_entidade = :id_entidade and id_wallet = :id_wallet";

        parent::Execute();

        $ret = (parent::rowCount() > 0);

        if ($ret == false) {

            $this->query = "SELECT * FROM tab_walletshare
                        WHERE id_entidade = :id_entidade and id_wallet = :id_wallet";

            parent::Execute();

            $ret = (parent::rowCount() > 0);
        }

        return $ret;
    }

    public function ReadSharing($id_entidade, $id_wallet) {

        $this->data = [
            "id_entidade" => $id_entidade,
            "id_wallet" => $id_wallet
        ];

       $this->query = "SELECT * FROM tab_walletshare
                       WHERE id_entidade = :id_entidade and id_wallet = :id_wallet";

        parent::Execute();
   }

    public function CreateSharing($id_wallet, $id_entidade) {

        $this->data = [
            "id_entidade" => $id_entidade,
            "id_wallet" => $id_wallet
        ];

        $this->query = "INSERT INTO tab_walletshare
                        (id_entidade, id_wallet) values (:id_entidade, :id_wallet)";

        parent::Execute();
    }

    public function DeleteSharing($id_wallet, $id_entidade) {

        $this->data = [
            "id_entidade" => $id_entidade,
            "id_wallet" => $id_wallet
        ];

        $this->query = "DELETE from tab_walletshare
                        WHERE id_entidade = :id_entidade and id_wallet = :id_wallet";

        parent::Execute();
        return parent::rowCount();
    }

    public function UpdateSaldo($id_wallet, $valor) {

		$this->data = [
			"id_wallet" => $id_wallet,
			"valor" => $valor,
		];

		$this->query = "UPDATE tab_wallet
						SET saldo = saldo + :valor
						WHERE id_wallet = :id_wallet";

		parent::Execute();
	}

    public function CreateDespesa($id_wallet, $id_entidade, $data, $id_walletsector, $walletdespesa, $id_walletcashtype, $valor, $datapago, $valorpago, $obs="") {

        $this->data = [
            "id_wallet" => $id_wallet ,
            "id_entidade" => $id_entidade ,
            "data" => $data ,
            "id_walletsector" => $id_walletsector ,
            "walletdespesa" => $walletdespesa ,
            "id_walletcashtype" => $id_walletcashtype ,
            "valor" => $valor,
            "datapago" => $datapago,
            "valorpago" => $valorpago,
            "obs" => $obs
        ];

        $this->query = "INSERT INTO tab_walletdespesa
                        (id_wallet, id_entidade, data, id_walletsector, walletdespesa, id_walletcashtype, valor, datapago, valorpago, obs)
                        VALUES (:id_wallet, :id_entidade, :data, :id_walletsector, :walletdespesa, :id_walletcashtype, :valor, :datapago, :valorpago, :obs)";

        parent::Execute();

        if ($lastInsertId = parent::lastInsertId()) {

            if ($datapago != null) {

                $this->UpdateSaldo($id_wallet, -$valorpago);
            }
        }

        return $lastInsertId;
    }

    public function ReadDespesa($id_walletdespesa) {

        $this->data = [
            "id_walletdespesa" => $id_walletdespesa
        ];

        $this->query = "SELECT *, tab_walletdespesa.obs as obs_despesa FROM tab_walletdespesa
                        INNER JOIN tab_walletsector on tab_walletsector.id_walletsector = tab_walletdespesa.id_walletsector
                        INNER JOIN tab_walletcashtype  on tab_walletcashtype.id_walletcashtype = tab_walletdespesa.id_walletcashtype
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_walletdespesa.id_entidade
                        WHERE id_walletdespesa = :id_walletdespesa";

        parent::Execute();
    }

    public function setPaymentDespesa($id_wallet, $id_walletdespesa, $datapago, $valorpago) {

        $this->data = [
            "id_walletdespesa" => $id_walletdespesa,
            "datapago" => $datapago,
            "valorpago" => $valorpago
        ];

        $this->query = "UPDATE tab_walletdespesa
						SET valorpago = :valorpago, datapago = :datapago
						WHERE id_walletdespesa = :id_walletdespesa";

        parent::Execute();

        if (parent::rowCount() > 0) {

            $this->UpdateSaldo($id_wallet, -$valorpago);
        }
    }

    public function UpdateDespesa(array $data) {

        $field = $data['field'];

        if ($field == 'valorpago') {

            $this->ReadDespesa($data['id_walletdespesa']);

            if ($row = parent::getResult()) {

                $this->UpdateSaldo($row['id_wallet'], $row['valorpago'] - $data['value']);
            }
        }

		$this->data = [
			"id_walletdespesa" => $data['id_walletdespesa'],
			"value" => $data['value'],
		];

        $this->query = "UPDATE tab_walletdespesa
                        set $field = :value
                        where id_walletdespesa = :id_walletdespesa";

        parent::Execute();
    }

    public function DeleteDespesa($id_walletdespesa) {

        $this->ReadDespesa($id_walletdespesa);

        if ($row = parent::getResult()) {

            if ($row["datapago"] != null) {

                $this->UpdateSaldo($id_walletdespesa, $row['valorpago']);
            }
        }

        $this->data = [
            "id_walletdespesa" => $id_walletdespesa
        ];

        $this->query = "DELETE FROM tab_walletdespesa
                        WHERE id_walletdespesa = :id_walletdespesa";

         parent::Execute();

         return parent::rowCount();
    }

    public function CreateReceita($id_wallet, $id_entidade, $data, $walletreceita, $valor) {

        $this->data = [
            "id_wallet" => $id_wallet ,
            "id_entidade" => $id_entidade ,
            "data" => $data ,
            "walletreceita" => $walletreceita ,
            "valor" => $valor
        ];

        $this->query = "INSERT INTO tab_walletreceita
                        (id_wallet, id_entidade, data, walletreceita, valor)
                        VALUES (:id_wallet, :id_entidade, :data, :walletreceita, :valor)";

        parent::Execute();

        if ($lastInsertId = parent::lastInsertId()) {

            $this->UpdateSaldo($id_wallet, $valor);
        }

        return $lastInsertId;
    }

    public function ReadReceita($id_walletreceita) {

        $this->data = [
            "id_walletreceita" => $id_walletreceita
        ];

        $this->query = "SELECT * FROM tab_walletreceita
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_walletreceita.id_entidade
                        WHERE id_walletreceita = :id_walletreceita";

        parent::Execute();
    }

    public function UpdateReceita(array $data) {

        $field = $data['field'];

        if ($field == 'valor') {

            $this->ReadReceita($data['id_walletreceita']);

            if ($row = parent::getResult()) {

                $this->UpdateSaldo($row['id_wallet'], $data['value'] - $row['valor']);
            }
        }

		$this->data = [
			"id_walletreceita" => $data['id_walletreceita'],
			"value" => $data['value'],
		];

        $this->query = "UPDATE tab_walletreceita
                        set $field = :value
                        where id_walletreceita = :id_walletreceita";

        parent::Execute();
    }

    public function DeleteReceita($id_walletreceita) {

        $this->ReadReceita($id_walletreceita);

        if ($row = parent::getResult()) {

            $this->UpdateSaldo($id_walletreceita, $row['valor']);
        }

        $this->data = [
            "id_walletreceita" => $id_walletreceita
        ];

        $this->query = "DELETE FROM tab_walletreceita
                        WHERE id_walletreceita = :id_walletreceita";

         parent::Execute();

         return parent::rowCount();
    }

    public static function FormatFieldsDespesa($row) {

        $row['data_formatted'] = date_format( date_create($row['data']), 'd/m/Y');

        if ($row["datapago"] != null) {

            $row['datapago_formatted'] = date_format( date_create($row['datapago']), 'd/m/Y');
            $row['datapago'] = date_format( date_create($row['datapago']), 'Y-m-d');
        }

        $row['data'] = date_format( date_create($row['data']), 'Y-m-d');
        $row['valor_formatted'] = number_format($row['valor'],2,",",".");
        $row['valorpago_formatted'] = number_format($row['valorpago'],2,",",".");

        return $row;
    }

    public static function FormatFieldsReceita($row) {

        $row['data_formatted'] = date_format( date_create($row['data']), 'd/m/Y');
        $row['data'] = date_format( date_create($row['data']), 'Y-m-d');
        $row['valor_formatted'] = number_format($row['valor'],2,",",".");

        return $row;
    }
}