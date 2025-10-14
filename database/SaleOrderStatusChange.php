<?php

namespace database;

class SaleOrderStatusChange extends Connection {

    public function Read($id_venda) {

        $this->data = [
            'id_venda' => $id_venda,
        ];

		$this->query = "SELECT tab_vendastatuschange.*, tab_vendastatus.*, tab_entidade.nome as colaborador FROM tab_vendastatuschange
                        INNER JOIN tab_vendastatus on tab_vendastatus.id_vendastatus = tab_vendastatuschange.id_vendastatus
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_vendastatuschange.id_entidade
                        WHERE id_venda = :id_venda";

		parent::Execute();
    }

    public function countPrints($id_venda) {

        $this->data = [
            'id_venda' => $id_venda,
            'id_vendastatus' => SaleOrder::STATUS_PEDIDO_IMPRESSO
        ];

		$this->query = "SELECT count(id_venda) as total FROM tab_vendastatuschange
                        WHERE id_venda = :id_venda and id_vendastatus = :id_vendastatus";

		parent::Execute();

        $row = parent::getResult();

        return $row['total'];
    }

    public function countStatus($id_venda, $id_vendastatus) {

        $this->data = [
            'id_venda' => $id_venda,
            'id_vendastatus' => $id_vendastatus
        ];

		$this->query = "SELECT count(id_venda) as total FROM tab_vendastatuschange
                        WHERE id_venda = :id_venda and id_vendastatus = :id_vendastatus";

		parent::Execute();

        $row = parent::getResult();

        return $row['total'];
    }

    public function getStatus($id_venda, $id_vendastatus) {

        $this->data = [
            'id_venda' => $id_venda,
            'id_vendastatus' => $id_vendastatus
        ];

		$this->query = "SELECT * FROM tab_vendastatuschange
                        INNER JOIN tab_entidade on tab_entidade.id_entidade = tab_vendastatuschange.id_entidade
                        WHERE tab_vendastatuschange.id_venda = :id_venda
                        AND tab_vendastatuschange.id_vendastatus = :id_vendastatus";

		parent::Execute();
    }
}