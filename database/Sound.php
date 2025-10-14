<?php

namespace database;

class Sound extends Connection {

	public function Read($id_som) {

		$this->data = [
			"id_som" => $id_som
		];

		$this->query = "SELECT * from tab_som where id_som = :id_som";

		parent::Execute();

	}

    public function Update($id, $field, $value) {

		$this->data = [
			"id_som" => $id,
			"value" => $value
		];

		$this->query = "UPDATE tab_som
						set $field = :value
						WHERE id_som = :id_som";

		parent::Execute();

		return parent::rowCount();
	}

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * from tab_som";

		parent::Execute();
	}

	public function Play($id_som) {

		$this->data = [
			"id_som" => $id_som
		];

		$this->query = "SELECT * from tab_som
						WHERE id_som = :id_som";

		parent::Execute();

		if ($row = parent::getResult()) {

			if (!OS::isWindows()) {

				$bell_file = realpath("./assets/sound") . "/" . $row["som"];

				$amp = $row["volume"] / 100.0;

				shell_exec("mplayer -nogui -af volnorm=2:" . $amp . " $bell_file >/dev/null 2>&1 ");
				// $ret = shell_exec("mplayer -volume " . $row["volume"] . " $bell_file");
				// Notifier::Add($amp, Notifier::NOTIFIER_INFO);
				//para o mplayer conseguir executar o audio precisa: sudo adduser www-data audio
			}

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
		}
	}

    public static function FormatFields($row) {

		$row["scalesbarcode"] = ($row["scalesbarcode"] == 1)?"checked":"";

	    $row['taxa_servico_formatted'] = number_format($row['taxa_servico'],2,",",".");

		if ($row['scalesbarcode_weightorprice'] == 0) {

			$row['scalesbarcode_weightorprice_desc'] = "Peso";

		} else {

			$row['scalesbarcode_weightorprice_desc'] = "Valor";
		}

		$row["taxagarcom"] = ($row["fc_waitertip_print"] == 1)?"checked":"";
		$row["produtos"] = ($row["fc_reverseitem_print"] == 1)?"checked":"";
		$row["vendas"] = ($row["fc_reversesale_print"] == 1)?"checked":"";
		$row["produtosvendidos"] = ($row["fc_productssold_print"] == 1)?"checked":"";
		$row["vendaprazo"] = ($row["fc_forwardsale_print"] == 1)?"checked":"";
		$row["vendaprazopaga"] = ($row["fc_forwardsalepaid_print"] == 1)?"checked":"";
		$row["pedidopago"] = ($row["fc_orderpaid_print"] == 1)?"checked":"";
		$row["reprint"] = ($row["fc_reprint_print"] == 1)?"checked":"";

        return $row;
    }
}