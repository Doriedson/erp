<?php

namespace database;

class Collaborator extends Connection {

	public function Create($id_entidade, $pass) {

		$this->data = [
			'id_entidade' => $id_entidade,
			'hash' => password_hash($pass, PASSWORD_BCRYPT),
			'acesso' => json_encode(array_fill(0, ControlAccess::CA_MAX + 1, 0))
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_colaborador ($fields) VALUES ($places)";

		parent::Execute();
	}

	public function Read($id_entidade) {

		$this->data = [
			'id_entidade' => $id_entidade
		];

		$this->query = "SELECT * FROM tab_colaborador
			INNER JOIN tab_entidade ON tab_entidade.id_entidade = tab_colaborador.id_entidade
			WHERE tab_colaborador.id_entidade = :id_entidade";

		parent::Execute();

		return (parent::rowCount() == 1);
	}

	public function getListHavingAccess($access) {

		$this->data = [];

		$this->query = "SELECT * FROM tab_colaborador
						INNER JOIN tab_entidade ON tab_entidade.id_entidade = tab_colaborador.id_entidade
						ORDER BY nome";

		parent::Execute();

		$result = [];

		while($row = parent::getResult()) {

			$entity_access = json_decode($row['acesso']);

			if ($entity_access[$access] == 1) {

				$result[] = $row;
			}
		}

		return $result;
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_colaborador
						INNER JOIN tab_entidade
						ON tab_entidade.id_entidade = tab_colaborador.id_entidade
						ORDER BY nome";

		parent::Execute();
	}

	public function RegistrySession($data) {

		$this->data = $data;

		$this->query = "UPDATE tab_colaborador
			SET sessao = ? WHERE id_entidade = ?";

		parent::Execute();
	}

	public function Delete($id_entidade) {

		$this->data = ["id_entidade" => $id_entidade];

		$this->query = "DELETE from tab_colaborador WHERE id_entidade = :id_entidade";

		parent::Execute();
		return parent::rowCount();
	}

	public function setAccess($id_entidade, $access) {

		$this->data = [
			"id_entidade" => $id_entidade,
			"acesso" => json_encode($access)
		];

		$this->query = "UPDATE tab_colaborador
						SET acesso = :acesso
						WHERE id_entidade = :id_entidade";

		parent::Execute();
	}

	public function setPass($id_entidade, $pass) {

		$this->data = [
			'id_entidade' => $id_entidade,
			'hash' => password_hash($pass, PASSWORD_BCRYPT),
		];

		$this->query = "UPDATE tab_colaborador
						SET hash = :hash
						WHERE id_entidade = :id_entidade";

		parent::Execute();
	}

	public static function FormatFields($row) {

		$access = json_decode($row['acesso']);

		$row['CA_SERVIDOR'] = ($access[ControlAccess::CA_SERVIDOR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_PRODUTO'] = ($access[ControlAccess::CA_SERVIDOR_PRODUTO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_PRODUTO_SETOR'] = ($access[ControlAccess::CA_SERVIDOR_PRODUTO_SETOR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_CLIENTE'] = ($access[ControlAccess::CA_SERVIDOR_CLIENTE] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_COLABORADOR'] = ($access[ControlAccess::CA_SERVIDOR_COLABORADOR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_EMISSAO_RECIBO'] = ($access[ControlAccess::CA_SERVIDOR_EMISSAO_RECIBO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_FORNECEDOR'] = ($access[ControlAccess::CA_SERVIDOR_FORNECEDOR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_COMPRA'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_COMPRA] == 1) ? 'checked': '';
		$row['CA_PDV'] = ($access[ControlAccess::CA_PDV] == 1) ? 'checked': '';
		$row['CA_PDV_SANGRIA'] = ($access[ControlAccess::CA_PDV_SANGRIA] == 1) ? 'checked': '';
		$row['CA_PDV_REFORCO'] = ($access[ControlAccess::CA_PDV_REFORCO] == 1) ? 'checked': '';
		$row['CA_PDV_CANCELA_ITEM'] = ($access[ControlAccess::CA_PDV_CANCELA_ITEM] == 1) ? 'checked': '';
		$row['CA_PDV_CANCELA_VENDA'] = ($access[ControlAccess::CA_PDV_CANCELA_VENDA] == 1) ? 'checked': '';
		$row['CA_PDV_DESCONTO'] = ($access[ControlAccess::CA_PDV_DESCONTO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_VENDA_FRETE'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_VENDA_FRETE] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_PRODUTO_PRECO'] = ($access[ControlAccess::CA_SERVIDOR_PRODUTO_PRECO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_COMPRA_LISTA'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_COMPRA_LISTA] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_CONTAS_A_PAGAR'] = ($access[ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_CONTAS_A_RECEBER'] = ($access[ControlAccess::CA_SERVIDOR_CONTAS_A_RECEBER] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_VENDA'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_VENDA] == 1) ? 'checked': '';
		$row['CA_VENDA_PRAZO_SEM_LIMITE'] = ($access[ControlAccess::CA_VENDA_PRAZO_SEM_LIMITE] == 1) ? 'checked': '';
		$row['CA_ORDEM_VENDA_EDITAR'] = ($access[ControlAccess::CA_ORDEM_VENDA_EDITAR] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO'] = ($access[ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_RELATORIO'] = ($access[ControlAccess::CA_SERVIDOR_RELATORIO] == 1) ? 'checked': '';
		$row['CA_CLIENTE_CREDITO'] = ($access[ControlAccess::CA_CLIENTE_CREDITO] == 1) ? 'checked': '';
		$row['CA_CLIENTE_LIMITE'] = ($access[ControlAccess::CA_CLIENTE_LIMITE] == 1) ? 'checked': '';
		$row['CA_SERVIDOR_CONFIG'] = ($access[ControlAccess::CA_SERVIDOR_CONFIG] == 1) ? 'checked': '';
		$row['CA_WAITER'] = ($access[ControlAccess::CA_WAITER] == 1) ? 'checked': '';
		$row['CA_PRODUTO_ESTOQUE_ADD'] = ($access[ControlAccess::CA_PRODUTO_ESTOQUE_ADD] == 1) ? 'checked': '';
		$row['CA_PRODUTO_ESTOQUE_DEL'] = ($access[ControlAccess::CA_PRODUTO_ESTOQUE_DEL] == 1) ? 'checked': '';
		$row['CA_TRANSFERENCIA_MESA'] = ($access[ControlAccess::CA_TRANSFERENCIA_MESA] == 1) ? 'checked': '';
		$row['CA_MESA_ITEM_ESTORNO'] = ($access[ControlAccess::CA_MESA_ITEM_ESTORNO] == 1) ? 'checked': '';
		$row['CA_VENDA_PRAZO_EDITAR'] = ($access[ControlAccess::CA_VENDA_PRAZO_EDITAR] == 1) ? 'checked': '';
		$row['CA_ESTOQUE_SECUNDARIO_ADD'] = ($access[ControlAccess::CA_ESTOQUE_SECUNDARIO_ADD] == 1) ? 'checked': '';
		$row['CA_ESTOQUE_SECUNDARIO_DEL'] = ($access[ControlAccess::CA_ESTOQUE_SECUNDARIO_DEL] == 1) ? 'checked': '';

		return $row;
	}
}