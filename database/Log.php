<?php

namespace database;

class Log extends Connection {

	private function Create($log) {

		$this->data = [
			'log' => json_encode($log),
		];

		$this->query = "INSERT INTO tab_log
						(log) values (:log)";

		parent::Execute();
	}

	public function Preco($id_entidade, $id_produto, $preco_anterior, $preco_novo, $promocao) {

		$log = [
			"log" => "preco",
			"id_entidade" => intval($id_entidade),
			"id_produto" => intval($id_produto),
			"preco_anterior" => floatval($preco_anterior),
			"preco_novo" => floatval($preco_novo),
			"promocao" => ($promocao == true)?true:false // true | false
		];

		$this->Create($log);
	}

	public function ProdutoEstoqueAdd($id_entidade, $id_produto, $qtd, $custoun, $obs) {

		$log = [
			"log" => "produto_estoque_add",
			"id_entidade" => intval($id_entidade),
			"id_produto" => intval($id_produto),
			"qtd" => floatval($qtd),
			"custoun" => floatval($custoun),
			"obs" => $obs
		];

		$this->Create($log);
	}

	public function ProdutoEstoqueDel($id_entidade, $id_produto, $qtd, $custoun, $obs) {

		$log = [
			"log" => "produto_estoque_del",
			"id_entidade" => intval($id_entidade),
			"id_produto" => intval($id_produto),
			"qtd" => floatval($qtd),
			"custoun" => floatval($custoun),
			"obs" => $obs
		];

		$this->Create($log);
	}

	public function ProdutoEstoqueSecundarioAdd($id_entidade, $id_produto, $qtd, $obs) {

		$log = [
			"log" => "produto_estoque_secundario_add",
			"id_entidade" => intval($id_entidade),
			"id_produto" => intval($id_produto),
			"qtd" => floatval($qtd),
			"obs" => $obs
		];

		$this->Create($log);
	}

	public function ProdutoEstoqueSecundarioDel($id_entidade, $id_produto, $qtd, $obs) {

		$log = [
			"log" => "produto_estoque_secundario_del",
			"id_entidade" => intval($id_entidade),
			"id_produto" => intval($id_produto),
			"qtd" => floatval($qtd),
			"obs" => $obs
		];

		$this->Create($log);
	}

	public function Desconto($id_entidade, $id_venda, $id_vendaitem, $valor) {

		$log = [
			"log" => "desconto",
			"id_entidade" => intval($id_entidade),
			"id_venda" => intval($id_venda),
			"id_vendaitem" => intval($id_vendaitem),
			"valor"=> floatval($valor),
		];

		$this->Create($log);
	}

	public function EntidadeLimite($id_entidade, $id_cliente, $valor) {

		$log = [
			"log" => "limite",
			"id_entidade" => intval($id_entidade),
			"id_cliente" => intval($id_cliente),
			"valor" => floatval($valor),
		];

		$this->Create($log);
	}

	public function EstornoItem($id_entidade, $id_venda, $id_vendaitem) {

		$log = [
			"log" => "estorno_item",
			"id_entidade" => intval($id_entidade),
			"id_venda" => intval($id_venda),
			"id_vendaitem" => intval($id_vendaitem)
		];

		$this->Create($log);
	}

	public function MesaTransferencia($id_entidade, $id_vendafrom, $id_vendato) {

		$log = [
			"log" => "mesa_transferencia",
			"id_entidade" => intval($id_entidade),
			"id_vendafrom" => intval($id_vendafrom),
			"id_vendato" => intval($id_vendato)
		];

		$this->Create($log);
	}

	public function EstornoVenda($id_entidade, $id_venda, $obs) {

		$log = [
			"log" => "estorno_venda",
			"id_entidade" => intval($id_entidade),
			"id_venda" => intval($id_venda),
			"obs" => $obs
		];

		$this->Create($log);
	}

	public function VendaPrazo($id_entidade, $id_venda) {

		$log = [
			"log" => "venda_prazo",
			"id_entidade" => intval($id_entidade),
			"id_venda" => intval($id_venda)
		];

		$this->Create($log);
	}

	// public function getEstornoVendaDateInterval($datestart, $dateend, $id_entidade) {

	// 	$this->data = [
	// 		'datestart' => $datestart,
	// 		'dateend' => $dateend,
	// 		'id_entidade' => '%"id_entidade":"' . $id_entidade . '"%'
	// 	];

	// 	$this->query = "SELECT * FROM tab_log
	// 					WHERE data BETWEEN :datestart AND :dateend AND log like '%estorno_venda%' AND log like :id_entidade
	// 					ORDER BY data";

	// 	parent::Execute();
	// }

	public function getEstornoVenda($id_venda) {

		$this->data = [
			'id_venda' => $id_venda
		];

		$this->query = "SELECT * FROM tab_log
						WHERE JSON_VALUE(log, '$.log') = 'estorno_venda'
						AND JSON_VALUE(log, '$.id_venda') = :id_venda";
						// log like '%estorno_venda%' AND log like :id_venda";

		parent::Execute();
	}

	public function getEstornoItem($id_venda, $id_vendaitem) {

		$this->data = [
			'id_venda' => $id_venda,
			'id_vendaitem' => $id_vendaitem
		];

		$this->query = "SELECT * FROM tab_log
						WHERE JSON_VALUE(log, '$.id_venda') = :id_venda
						AND JSON_VALUE(log, '$.id_vendaitem') = :id_vendaitem";
						// log like '%estorno_item%' AND log like :id_venda AND log like :id_vendaitem";

		parent::Execute();
	}

	// public function getEstornoItemDateInterval($datestart, $dateend) {

	// 	$this->data = [
	// 		'datestart' => $datestart,
	// 		'dateend' => $dateend
	// 	];

	// 	$this->query = "SELECT * FROM tab_log
	// 					WHERE data BETWEEN :datestart AND :dateend AND log like '%estorno_item%'
	// 					ORDER BY data";

	// 	parent::Execute();
	// }

	public function getProductStockDateInterval($id_produto, $datestart, $dateend, $stock_primary) {

		$this->data = [
			'datestart' => $datestart . " 00:00:00",
			'dateend' => $dateend . " 23:59:59",
		];

		$this->query = "SELECT * FROM tab_log
							WHERE data BETWEEN :datestart AND :dateend";

		if ($stock_primary == true) {

			$this->query .= " AND log->'$.log' in ('produto_estoque_add', 'produto_estoque_del')";

		} else {

			$this->query .= " AND log->'$.log' in ('produto_estoque_secundario_add', 'produto_estoque_secundario_del')";
		}

		if ($id_produto != null) {

			$this->data['id_produto'] = $id_produto;

			// $this->query .= " AND JSON_CONTAINS(log, :id_produto, '$.id_produto')";
			$this->query .= " AND JSON_VALUE(log, '$.id_produto') = :id_produto";
		}

		$this->query .= " ORDER BY data";

		parent::Execute();
	}

	/** To Correct tab_log com formato JSON inválido */
	public function Up($text) {

		$this->data = [
			"text" => $text
		];

// Uncomment after adapt code
		// $this->query = "Update tab_log set log = REPLACE(log, '\"id_vendaitem\"', ', \"id_vendaitem\"') where log = :text";

		parent::Execute();
	}

	public function UpdateLog() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_log";

		parent::Execute();

		$count = 0;
		$count_error = 0;
		$rows = [];

		$log = new Self();

		while($row = $this->getResult()) {
			// var_dump($row);
			$decoded = json_decode($row["log"]);
// var_dump($decoded);
// return;

			if ($decoded) {

				// switch($decoded->log) {

				// 	case "produto_estoque_add":

				// 		$count++;
				// 		break;
				// }
			} else {

				$count_error++;

// Uncomment after adapt code
				// $log->Up($row['log']);

				// Notifier::Add($row['log'], Notifier::NOTIFIER_ERROR);
//Verificar as linhas com erro
				// $rows[] = $row['log'];
			}
		}

		Notifier::Add("produto_estoque_add " . $count . " errors: " . $count_error, Notifier::NOTIFIER_INFO);
//Verificar as linhas com erro
		// return $rows;
	}
}