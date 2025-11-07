<?php

namespace App\Legacy;

use App\View\View;
class ProductExpDate extends Connection {

    public function Create($id_produto, $validade) {

		$this->data = [
			'id_produto' => $id_produto,
			'data' => $validade,
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_produtovalidade ($fields) VALUES ($places)";

		parent::Execute();

        return parent::lastInsertId();
	}

	public function Read($id_produtovalidade) {

        $this->data = [
            'id_produtovalidade' => $id_produtovalidade
		];

		$this->query = "SELECT *, DATEDIFF(data, now()) as dias
                        FROM tab_produtovalidade
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtovalidade.id_produto
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
			            WHERE id_produtovalidade = :id_produtovalidade";

		parent::Execute();
	}

	public function Delete($id_produtovalidade) {

		$this->data = ["id_produtovalidade" => $id_produtovalidade];

		$this->query = "DELETE FROM tab_produtovalidade
			WHERE id_produtovalidade = :id_produtovalidade";

		parent::Execute();
		return parent::rowCount();
	}

    public function getList($id_produto) {

		$this->data = ["id_produto" => $id_produto];

		$this->query = "SELECT *, DATEDIFF(data, now()) as dias
                        FROM tab_produtovalidade
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtovalidade.id_produto
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
                        WHERE tab_produtovalidade.id_produto = :id_produto
                        ORDER BY data";

		parent::Execute();
	}

	public function getListExpirateUntil($days) {

		$this->data = ["days" => $days];

		$this->query = "SELECT *, DATEDIFF(data, now()) as dias
                        FROM tab_produtovalidade
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtovalidade.id_produto
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
                        WHERE data between now() AND now() + interval :days day
                        ORDER BY data";

		parent::Execute();
	}

	public function getListExpirated() {

		$this->data = [];

		$this->query = "SELECT *, DATEDIFF(data, now()) as dias
                        FROM tab_produtovalidade
						INNER JOIN tab_produto on tab_produto.id_produto = tab_produtovalidade.id_produto
						INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
                        WHERE data < now()
                        ORDER BY data";

		parent::Execute();
	}

    public function Search($id_produto, $validade) {

        $this->data = [
            'id_produto' => $id_produto,
			'datestart' => $validade . " 00:00:00",
			'dateend' => $validade . " 23:59:59"
		];

		$this->query = "SELECT * FROM tab_produtovalidade
			WHERE id_produto = :id_produto and data between :datestart and :dateend";

		parent::Execute();
	}

	public static function DoPrint($days, $id_impressora) {

		$printing = new Printing($id_impressora);

		if (!$printing->initialize()) {

			return false;
		}

		$productExpDate = new self();

		$productExpDate->getListExpirateUntil($days);

		if ($row = $productExpDate->getResult()) {

			$printing->textCenter("Lista de Validade dos Produtos");

			$printing->line(1);

			$printing->text("Data: " . date("d/m/Y H:i"));

			$printing->line(1);

			do {

				$row = self::FormatFields($row);

				$printing->text($row['produto']);

				$printing->textSpaceBetween("Validade", $row['data_formatted']);

				$printing->line(1);

			} while ($row = $productExpDate->getResult());
		}

		$printing->linedashspaced();

		$printing->close();

		return true;
	}

	public static function getListHUD() {

		$product_list = "";
		$expirated = 0;
		$toexpirate = 0;

		$config = new Config();
		$productExpDate = new ProductExpDate();

		$config->Read();

		if ($row = $config->getResult()) {

			$tplCP = new View("home");

			$productExpDate->getListExpirated();

			$expirated = $productExpDate->rowCount();

			if ($rowExpirated = $productExpDate->getResult()) {

				do {

					$rowExpirated = ProductExpDate::FormatFields($rowExpirated);
					$rowExpirated = Product::FormatFields($rowExpirated);
					$rowExpirated["extra_block_productexpdate_days"] = $tplCP->getContent([], "EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED");

					$product_list .= $tplCP->getContent($rowExpirated, "EXTRA_BLOCK_CP_EXPDATE_TR");

				} while ($rowExpirated = $productExpDate->getResult());
			}

			$productExpDate->getListExpirateUntil($row['product_expirate_days']);

			$toexpirate = $productExpDate->rowCount();

			if ($rowProduct = $productExpDate->getResult()) {

				do {

					$rowProduct = ProductExpDate::FormatFields($rowProduct);
					$rowProduct = Product::FormatFields($rowProduct);
					$rowProduct["extra_block_productexpdate_days"] = $tplCP->getContent($rowProduct, "EXTRA_BLOCK_PRODUCTEXPDATE_DAYS");

					$product_list .= $tplCP->getContent($rowProduct, "EXTRA_BLOCK_CP_EXPDATE_TR");

				} while ($rowProduct = $productExpDate->getResult());
			}

			$extra_block_expiratedays = $tplCP->getContent($row, "EXTRA_BLOCK_EXPIRATEDAYS");

			return [$product_list, $expirated, $toexpirate, $extra_block_expiratedays];
		}

		return null;
	}

    public static function FormatFields($row) {

		$row['data_formatted'] = date_format(date_create($row['data']),'d/m/Y');

        return $row;
    }
}