<?php

namespace database;

class CashDrawer {

    private $table;
    private $rows;
    private $row_index;

    public function __construct() {

        $this->row_index = 0;

        $this->table = [
            [
                "id_gaveteiro" => 1,
                "descricao" => "Bematech",
                "comando" => chr(27) . chr(118) . chr(140)
            ],
            [
                "id_gaveteiro" => 2,
                "descricao" => "Oletech",
                "comando" => chr(27) . chr(112) . chr(0) . chr(140) . chr(140)
            ],
        ];
    }

    public function Read($id_gaveteiro) {

        $this->row_index = 0;
        $this->rows = [];

        foreach ($this->table as $row) {

            if ($row['id_gaveteiro'] == $id_gaveteiro) {

                $this->rows = [$row];
                break;
            }
        }
    }

    public function getResult() {

        if ($this->row_index == count($this->rows)) {

            return null;
        }

        return $this->rows[$this->row_index++];
    }

    public function getList() {

        $this->row_index = 0;
        $this->rows = $this->table;
    }

}