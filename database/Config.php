<?php

namespace database;

class Config extends Connection {

	const SCALESBARCODE_WEIGHT = 0;
	const SCALESBARCODE_PRICE = 1;

	public function Read() {

		$this->data = [];

		$this->query = "SELECT * from tab_config";

		parent::Execute();

	}

    public function Update(array $data) {

		$field = $data['field'];

		$this->data = [
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_config set $field = :value";

		parent::Execute();
	}

	public function ToggleActiveSecondaryStock() {

		$this->data = [];

		$this->query = "UPDATE tab_config set estoque_secundario = not estoque_secundario";

		parent::Execute();
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
		$row["mesas"] = ($row["fc_table"] == 1)?"checked":"";

		if ($row["fc_productssoldoption_print"] == 1) {

			$row["frm_cashierclosing_product_option"] = "Abertura do caixa até fechamento";
			$row["frm_cashierclosing_product_option_0"] = "";
			$row["frm_cashierclosing_product_option_1"] = "selected";

		} else {

			$row["frm_cashierclosing_product_option"] = "0h da abertura do caixa até fechamento";
			$row["frm_cashierclosing_product_option_0"] = "selected";
			$row["frm_cashierclosing_product_option_1"] = "";
		}

        return $row;
    }
}