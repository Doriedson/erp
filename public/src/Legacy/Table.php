<?php

namespace App\Legacy;

class Table extends Connection {

    public function Create($num_of_tables, $id_start) {

		$this->data = [];

        // $this->query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES
        //                 WHERE TABLE_SCHEMA = '" . parent::$database . "' AND TABLE_NAME = 'tab_mesa'";

		// $this->query = "SELECT id_mesa FROM tab_mesa
        //                 ORDER BY id_mesa desc limit 1";

        // parent::Execute();

		// $id_mesa = 1;

		// if ($row = parent::getResult()) {

		// 	$id_mesa = $row['id_mesa'] + 1;
		// }
		// $id = parent::getResult()['AUTO_INCREMENT'] + 1;

        $entry = "";

        for ($k = 0; $k < $num_of_tables; $k++) {

            $id_table = $id_start + $k;

            if ($entry != "") $entry .= ", ";

            $entry .= "('Mesa " . str_pad($id_table, 2, "0", STR_PAD_LEFT) . "')";
        }

		$this->query = "INSERT INTO tab_mesa (mesa) VALUES $entry";

		parent::Execute();
        return parent::rowCount();
	}

    public function Delete($id_mesa) {

		$this->data = [
			'id_mesa' => $id_mesa
		];

		$this->query = "DELETE from tab_mesa
						WHERE id_mesa = :id_mesa and id_venda is null";

		parent::Execute();

		return parent::rowCount();
	}

	public function Read($id_mesa) {

		$this->data = [
			"id_mesa" => $id_mesa
		];

		$this->query = "SELECT * FROM tab_mesa
			WHERE id_mesa = :id_mesa";

		parent::Execute();
	}

	public function Transfer($id_mesa_from, $versao_from, $id_mesa_to, $versao_to, $id_colaborador = null) {

		if ($id_mesa_from == $id_mesa_to) {

			Notifier::Add("Mesa não pode ser transferida para ela mesma!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($id_colaborador == null) {

			$id_colaborador = $GLOBALS['authorized_id_entidade'];
		}

		$this->Read($id_mesa_from);

		if ($row = $this->getResult()) {

			if ($row['id_venda'] == null) {

				Notifier::Add("Mesa não está em atendimento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$id_venda_from = $row['id_venda'];

			$this->Read($id_mesa_to);

			if ($row = $this->getResult()) {

				$sale = new SaleOrder();

				if ($row["id_venda"]) {

					$sale->ReadOnly($row["id_venda"]);

					if ($rowVendaTo = $sale->getResult()) {

						if ($rowVendaTo["id_vendastatus"] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

							Notifier::Add("Não é possível transferir para mesa fechada para pagamento!", Notifier::NOTIFIER_ERROR);
							Send(null);
						}
					}
				}

				if(!$versao_from = $sale->CheckVersion($id_venda_from, $versao_from)) {

					Send(null);
				}

				if ($row['id_venda'] == null) {

					$this->Book($id_mesa_to, $id_venda_from, $id_colaborador);

					$this->Free($id_mesa_from);

					$sale->Update($id_venda_from, "mesa", $row["mesa"]);

					$log = new Log();
					$log->MesaTransferencia($id_colaborador, $id_venda_from, $id_venda_from);

					Notifier::Add("Mesa tranferida com sucesso!", Notifier::NOTIFIER_INFO);
					Send([]);

				} else {

					$id_venda_to = $row['id_venda'];

					if(!$versao_to = $sale->CheckVersion($id_venda_to, $versao_to)) {

						Send(null);
					}

					$sale->Update($id_venda_to, "mesa", $row["mesa"]);

					$this->Update([
						"field" => "id_entidade",
						"id_mesa" => $id_mesa_to,
						"value" => $id_colaborador
					]);
				}

				$saleItemFrom = new SaleOrderItem();

				$saleItemFrom->getListActiveItems($id_venda_from);

				if ($rowItem = $saleItemFrom->getResult()) {

					$saleItemTo = new SaleOrderItem();

					do {

						$saleItemTo->Create($id_venda_to, $rowItem['id_produto'], $rowItem['id_produtotipo'], $rowItem['qtd'], $rowItem['preco'], $rowItem['obs']);

					} while ($rowItem = $saleItemFrom->getResult());

					$sale->Delete($id_venda_from, "", $id_colaborador, true);

					$log = new Log();

					$log->MesaTransferencia($id_colaborador, $id_venda_from, $id_venda_to);

					Notifier::Add("Mesa tranferida com sucesso!", Notifier::NOTIFIER_INFO);
					Send([]);

				} else {

					Notifier::Add("Mesa sem produto para transferir!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			} else {

				Notifier::Add("Error ao carregar dados da mesa de destino!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados da mesa de origem!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
	}

	public function ReadFromSale($id_venda) {

		$this->data = [
			"id_venda" => $id_venda
		];

		$this->query = "SELECT * FROM tab_mesa
			WHERE id_venda = :id_venda";

		parent::Execute();
	}

	public function getList($busy_order = false) {

		$this->data = [];

		$this->query = "SELECT tab_mesa.*, tab_entidade.nome FROM tab_mesa
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_mesa.id_entidade";

		if ($busy_order == true) {

			$this->query .= " order by id_venda IS null, id_mesa";

		} else {

			$this->query .= " order by id_mesa";
		}

		parent::Execute();
	}

    public static function LoadWaiterTable($screen) {

		$table = new self();

		$table->getList();

		$tplTable = new View("waiter_table");

		$tables = "";

		if ($row = $table->getResult()) {

			do {

				$row['screen'] = $screen;
				$row = self::FormatFields($row);

				if ($row['id_venda']) {

					if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

						$tables .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_BUSY");

					} else if ($row['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_PAGAMENTO) {

						$tables .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_PAYMENT");
					}

				} else {

					$tables .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE_FREE");
				}

			} while ($row = $table->getResult());

		} else {

			$tables = $tplTable->getContent([], "EXTRA_BLOCK_TABLE_NOTFOUND");
		}

        $data = [
            "extra_block_table" => $tables
        ];

		return $tplTable->getContent($data, "BLOCK_PAGE");
    }

    public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"id_mesa" => $data['id_mesa'],
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_mesa
						SET $field = :value
						WHERE id_mesa = :id_mesa";

		parent::Execute();
	}

	public function Book($id_mesa, $id_venda, $id_entidade) {

		$this->data = [
			"id_mesa" => $id_mesa,
			"id_venda" => $id_venda,
			"id_entidade" => $id_entidade,
		];

		$this->query = "UPDATE tab_mesa
						SET id_venda = :id_venda, id_entidade = :id_entidade
						WHERE id_mesa = :id_mesa";

		parent::Execute();
	}

	public function Free($id_mesa) {

		$this->data = [
			"id_mesa" => $id_mesa
		];

		$this->query = "UPDATE tab_mesa
						SET id_venda = null, id_entidade = null
						WHERE id_mesa = :id_mesa";

		parent::Execute();
	}

    public function hasDuplicated($id_mesa, $mesa) {

        $this->data = [
			"id_mesa" => $id_mesa,
            "mesa" => $mesa
		];

		$this->query = "SELECT * FROM tab_mesa
			WHERE id_mesa <> :id_mesa and mesa = :mesa";

		parent::Execute();

        return parent::rowCount() > 0;
    }

	public function Search($data, $busy_order = false) {

		$data = (string) $data;
		$data = '%' . str_replace('+', '%+%', $data) . '%';
		$this->data = explode('+', $data);

		$this->query = "SELECT tab_mesa.*, tab_entidade.nome from tab_mesa
						LEFT JOIN tab_entidade on tab_entidade.id_entidade = tab_mesa.id_entidade
						WHERE tab_mesa.mesa LIKE ?";

		for ($indexCount = count($this->data); $indexCount > 1; $indexCount--) {

			$this->query .= " AND tab_mesa.mesa LIKE ?";
		}

		if ($busy_order == true) {

			$this->query .= " order by id_venda IS null, id_mesa";

		} else {

			$this->query .= " order by id_mesa";
		}

		parent::Execute();
	}

	public static function FormatFields($row) {

		$row['id_colaborador'] = $row['id_entidade'];

		if (key_exists("nome", $row)) {

			$row['garcom'] = $row['nome'];
		}

		if ($row['id_venda']) {

			$sale = new SaleOrder();

			$sale->Read($row['id_venda']);

			if ($rowSale = $sale->getResult()) {

				$row['id_entidade'] = $rowSale['id_entidade'];
				$row['vendastatus'] = $rowSale['vendastatus'];
				$row["versao"] = $rowSale["versao"];
				$row['id_vendastatus'] = $rowSale['id_vendastatus'];
				$row['data_formatted'] = date_format( date_create($rowSale['data']), 'd/m/Y H:i');

				$entity = new Entity();

				// $entity->Read($rowSale['id_colaborador']);

				// if ($rowEntity = $entity->getResult()) {

				// 	$row['garcom'] = $rowEntity['nome'];
				// }

				if (is_null($rowSale['id_entidade'])) {

					$row['cliente'] = "Varejo";

				} else {

					$entity->Read($rowSale['id_entidade']);

					if ($rowEntity = $entity->getResult()) {

						$row['cliente'] = $rowEntity['nome'];
					}
				}

				if ($rowSale['id_vendastatus'] == SaleOrder::STATUS_MESA_EM_ANDAMENTO) {

					$row['status'] = 'border-orange';

				} else {

					$row['status'] = 'border-red';
				}
			}

		} else {

			$row['id_entidade'] = null;
			$row['status'] = 'border-green';
			$row['id_vendastatus'] = null;
		}

		$tplTable = new View("waiter_table");

		if (array_key_exists("screen", $row)) {

			switch ($row['screen']) {

				case "waiter_table":

					$row['button_select'] = $tplTable->getContent([], "EXTRA_BLOCK_BUTTON_SELECT");
					$row['button_view'] = $tplTable->getContent($row, "EXTRA_BLOCK_BUTTON_VIEW");
					break;

				case "selfservice":

					$row['button_select'] = $tplTable->getContent([], "EXTRA_BLOCK_BUTTON_ADD");
					$row['button_view'] = $tplTable->getContent($row, "EXTRA_BLOCK_BUTTON_VIEW");
					break;

				case "waiter_tabletransf":

					$row['button_select'] = $tplTable->getContent($row, "EXTRA_BLOCK_BUTTON_TRANSFER");
					$row['button_view'] = ""; //$tplTable->getContent([], "");
					break;
			}
		}

		return $row;
	}
}