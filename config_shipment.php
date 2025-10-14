<?php

use database\ControlAccess;
use database\EntityAddress;
use database\FreightCEP;
use database\FreightValue;
use database\Notifier;
use database\View;
use database\Freight;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

function ShipmentFormEdit($block, $message_error) {

	$tplShipment = new View('templates/config_shipment');

	$shipment = new Freight();
	$shipment->Read();

	if ($row = $shipment->getResult()) {

		$row = Freight::FormatFields($row);

		Send($tplShipment->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ShipmentFormCancel($block, $message_error) {

	$tplShipment = new View('templates/config_shipment');

	$shipment = new Freight();
	$shipment->Read();

	if ($row = $shipment->getResult()) {

		$row = Freight::FormatFields($row);

		Send($tplShipment->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ShipmentFormSave($field, $block, $message_error) {

	$value = $_POST['value'];

	$data = [
		'field' => $field,
		'value' => $value,
	];

	$billstopay = new Freight();

	$billstopay->Update($data);

	$tplShipment = new View('templates/config_shipment');

	$billstopay->Read();

	if ($row = $billstopay->getResult()) {

		$row = Freight::FormatFields($row);

		Send($tplShipment->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function getFreightCEPList($id_fretevalor = 0) {

	$tplShipment = new View('templates/config_shipment');

	$freightValue = new FreightValue();

	$freightValue->getList();

	$extra_block_fretevalor_option = "";

	if ($row = $freightValue->getResult()) {

		do {

			$selected = "";

			if ($row["id_fretevalor"] == $id_fretevalor) {

				$selected = "selected";
			}

			$row = FreightValue::FormatFields($row);

			$row["selected"] = $selected;

			$extra_block_fretevalor_option .= $tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTVALUE_OPTION");

		} while($row = $freightValue->getResult());
	}

	return $extra_block_fretevalor_option;
}

switch ($_POST['action']) {

	case "load":

		$tplShipment = new View('templates/config_shipment');

		$cep_list = "";
		$freightcep_none = "hidden";

		$freightCep = new FreightCEP();

		$freightCep->getList();

		if ($row = $freightCep->getResult()) {

			do {

				$row = FreightCEP::FormatFields($row);

				$cep_list .= $tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTCEP");

			} while ($row = $freightCep->getResult());

		} else {

			// $cep_list = $tplShipment->getContent([], "EXTRA_BLOCK_FREIGHTCEP_NONE");
			$freightcep_none = "";
		}

		$freight = new Freight();

		$freight->Read();

		if ($row = $freight->getResult()) {

			// $row['data'] = date('Y-m-d');

			$row = Freight::FormatFields($row);

			$row["extra_block_freightcep"] = $cep_list;
			$row["freightcep_none"] = $freightcep_none;

			Send($tplShipment->getContent($row, "BLOCK_PAGE"));

		} else {

			Notifier::Add("Erro ao carregar informações do frete!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "shipment_deliveryminimo_valor_edit":

		ShipmentFormEdit("EXTRA_BLOCK_DELIVERYMINIMO_VALOR_FORM", "Erro ao carregar formulário para valor minímo de delivery!");
	break;

	case "shipment_deliveryminimo_valor_cancel":

		ShipmentFormCancel("BLOCK_DELIVERYMINIMO_VALOR", "Erro ao carregar dados de valor minímo de delivery!");
	break;

	case "shipment_deliveryminimo_valor_save":

		ShipmentFormSave("deliveryminimo_valor", "BLOCK_DELIVERYMINIMO_VALOR", "Erro ao carregar dados de valor minímo de delivery!");
	break;

	case "shipment_fretegratis_valor_edit":

		ShipmentFormEdit("EXTRA_BLOCK_FRETEGRATIS_VALOR_FORM", "Erro ao carregar formulário de frete grátis!");
	break;

	case "shipment_fretegratis_valor_cancel":

		ShipmentFormCancel("BLOCK_FRETEGRATIS_VALOR", "Erro ao carregar dados de frete grátis!");
	break;

	case "shipment_fretegratis_valor_save":

		ShipmentFormSave("fretegratis_valor", "BLOCK_FRETEGRATIS_VALOR", "Erro ao carregar dados de frete grátis!");
	break;

	case "freightcep_show_new":

		$tplShipment = new View("templates/config_shipment");

		$cep_de = "";
		$cep_ate = "";

		if (key_exists("cep", $_POST)) {

			$cep_de = $_POST["cep"];
			$cep_ate = $_POST["cep"];
		}

		$data = [
			"extra_block_freightvalue_option" => getFreightCEPList(),
			"cep_de" => $cep_de,
			"cep_ate" => $cep_ate
		];

		Send($tplShipment->getContent($data, "EXTRA_BLOCK_FREIGHTCEP_NEW"));

	break;

	case "freightvalue_manager":

		$freightValue = new FreightValue();

		$freightValue->getList();

		$tplShipment = new View('templates/config_shipment');

		$freight_value_list = "";

		$notfound = "";

		if ($row = $freightValue->getResult()) {

			$notfound = "hidden";

			do {

				$row = FreightValue::FormatFields($row);

				$freight_value_list.= $tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTVALUE");

			} while ($row = $freightValue->getResult());
		}

		$data = [
			"extra_block_freightvalue" => $freight_value_list,
			"notfound" => $notfound
		];

		Send($tplShipment->getContent($data, "EXTRA_BLOCK_FREIGHTVALUE_POPUP"));

	break;

	case "freightvalue_new":

		$descricao = $_POST["descricao"];
		$valor = $_POST["valor"];

		$freightValue = new FreightValue();

		$id_fretevalor = $freightValue->Create($descricao, $valor);

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightValue::FormatFields($row);

			$data = [
				"extra_block_freightvalue" => $tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTVALUE"),
				"freight_cep_list" => $tplShipment->getContent(["extra_block_freightvalue_option" => getFreightCEPList($id_fretevalor)], "BLOCK_FREIGHTVALUE_SELECT")
			];

			Send($data);

		} else {

			Notifier::Add("Erro ao cadastrar valor de frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_del":

		$id_fretevalor = $_POST["id_fretevalor"];

		$freightValue = new FreightValue();

		if ($freightValue->Delete($id_fretevalor) > 0) {

			$tplShipment = new View('templates/config_shipment');

			Send($tplShipment->getContent(["extra_block_freightvalue_option" => getFreightCEPList()], "BLOCK_FREIGHTVALUE_SELECT"));

		} else {

			Send(null);
		}

	break;

	case "freightvalue_descricao_edit":

		$id_fretevalor = $_POST["id_fretevalor"];

		$freightValue = new FreightValue();

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			Send($tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTVALUE_DESCRICAO_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_descricao_cancel":

		$id_fretevalor = $_POST["id_fretevalor"];

		$freightValue = new FreightValue();

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_descricao_save":

		$id_fretevalor = $_POST["id_fretevalor"];
		$descricao = $_POST["descricao"];

		$freightValue = new FreightValue();

		$freightValue->Update([
			"id_fretevalor" => $id_fretevalor,
			"field" => "descricao",
			"value" => $descricao
		]);

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			// Send($tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_DESCRICAO"));

			$data = [
				"data" => $tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_DESCRICAO"),
				"freight_cep_list" => $tplShipment->getContent(["extra_block_freightvalue_option" => getFreightCEPList($id_fretevalor)], "BLOCK_FREIGHTVALUE_SELECT")
			];

			Send($data);


		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_valor_edit":

		$id_fretevalor = $_POST["id_fretevalor"];

		$freightValue = new FreightValue();

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightValue::FormatFields($row);

			Send($tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTVALUE_VALOR_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_valor_cancel":

		$id_fretevalor = $_POST["id_fretevalor"];

		$freightValue = new FreightValue();

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightValue::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_VALOR"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightvalue_valor_save":

		$id_fretevalor = $_POST["id_fretevalor"];
		$valor = $_POST["valor"];

		$freightValue = new FreightValue();

		$freightValue->Update([
			"id_fretevalor" => $id_fretevalor,
			"field" => "valor",
			"value" => $valor
		]);

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightValue::FormatFields($row);

			// Send($tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_VALOR"));
			$data = [
				"data" => $tplShipment->getContent($row, "BLOCK_FREIGHTVALUE_VALOR"),
				"valor_formatted" => $row["valor_formatted"],
				"freight_cep_list" => $tplShipment->getContent(["extra_block_freightvalue_option" => getFreightCEPList($id_fretevalor)], "BLOCK_FREIGHTVALUE_SELECT")
			];

			Send($data);


		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_new":

		$id_fretevalor = $_POST["id_fretevalor"];
		$descricao = $_POST["descricao"];
		$cep_de = $_POST["cep_de"];
		$cep_ate = $_POST["cep_ate"];

		if (intval($cep_ate) < intval($cep_de)) {

			Notifier::Add("Segundo CEP não pode ser menor que primeiro CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$freightCep = new FreightCEP();

		$id_fretecep = $freightCep->Create($descricao, $cep_de, $cep_ate, $id_fretevalor);

		if ($id_fretecep == null) {

			Send(null);

		} else {

			$freightCep = new FreightCEP();

			$freightCep->Read($id_fretecep);

			if ($row = $freightCep->getResult()) {

				$row = FreightCEP::FormatFields($row);

				$tplFreightCep = new View("templates/config_shipment");

				Notifier::Add("Nova faixa de CEP cadastrada.", Notifier::NOTIFIER_INFO);

				Send($tplFreightCep->getContent($row, "EXTRA_BLOCK_FREIGHTCEP"));

			} else {

				Notifier::Add("Erro ao ler dados do CEP.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

	break;

	case "freightcep_del":

		$id_fretecep = $_POST["id_fretecep"];

		$freightCep = new FreightCEP();

		if ($freightCep->Delete($id_fretecep) > 0) {

			Notifier::Add("Faixa de CEP removida.", Notifier::NOTIFIER_INFO);
			Send([]);

		} else {

			Send(null);
		}

	break;

	case "freightcep_ativo":

		$id_fretecep = $_POST["id_fretecep"];

		$freightCep = new FreightCEP();

		$freightCep->ToggleAtivo($id_fretecep);

        $freightCep->Read($id_fretecep);

        if ($row = $freightCep->getResult()) {

            Send($row['ativo']);

        } else {

            Notifier::Add("Erro ao carregar dados do X-DeliveryDireto-ID!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

	break;

	case "fretecep_descricao_edit":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			Send($tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTCEP_DESCRICAO_FORM"));

		} else {

			Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_descricao_cancel":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_DESCRICAO"));

		} else {

			Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_descricao_save":

		$id_fretecep = $_POST["id_fretecep"];
		$descricao = $_POST["descricao"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Update([
			"id_fretecep" => $id_fretecep,
			"field" => "descricao",
			"value" => $descricao
		]);

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_DESCRICAO"));

		} else {

			Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "fretecep_cep_de_edit":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTCEP_CEP_DE_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_cep_de_cancel":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_CEP_DE"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_cep_de_save":

		$id_fretecep = $_POST["id_fretecep"];
		$cep_de = $_POST["cep_de"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			if (intval($cep_de) > $row["cep_ate"]) {

				Notifier::Add("Primeiro CEP deve ser menor ou igual ao segundo.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			if ($freightCep->hasCEPConflict($cep_de, $row["cep_ate"], $id_fretecep) == true) {

				Notifier::Add("Faixa de CEP em conflito com CEP cadastrado.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados do CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$freightCep->Update([
			"id_fretecep" => $id_fretecep,
			"field" => "cep_de",
			"value" => $cep_de
		]);

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_CEP_DE"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "fretecep_cep_ate_edit":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTCEP_CEP_ATE_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_cep_ate_cancel":

		$id_fretecep = $_POST["id_fretecep"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_CEP_ATE"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_cep_ate_save":

		$id_fretecep = $_POST["id_fretecep"];
		$cep_ate = $_POST["cep_ate"];

		$tplShipment = new View('templates/config_shipment');

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			if (intval($cep_ate) < $row["cep_de"]) {

				Notifier::Add("Segundo CEP deve ser maior ou igual ao primeiro.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			if ($freightCep->hasCEPConflict($row["cep_de"], $cep_ate, $id_fretecep) == true) {

				Notifier::Add("Faixa de CEP em conflito com CEP cadastrado.", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao carregar dados do CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$freightCep->Update([
			"id_fretecep" => $id_fretecep,
			"field" => "cep_ate",
			"value" => $cep_ate
		]);

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_CEP_ATE"));

		} else {

			Notifier::Add("Erro ao carregar dados de CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "fretecep_valor_edit":

		$id_fretecep = $_POST["id_fretecep"];

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$id_fretevalor = $row["id_fretevalor"];

		} else {

			Notifier::Add("Erro ao carregar dados do CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$freightValue = new FreightValue();

		$freightValue->Read($id_fretevalor);

		if ($row = $freightValue->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightValue::FormatFields($row);

			$data = [
				"id_fretecep" => $id_fretecep,
				"extra_block_freightvalue_option" => getFreightCEPList($id_fretevalor)
			];

			Send($tplShipment->getContent($data, "EXTRA_BLOCK_FREIGHTCEP_VALOR_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados de valores do CEP.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_valor_cancel":

		$id_fretecep = $_POST["id_fretecep"];

		$freightCep = new FreightCEP();

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_VALOR"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freightcep_valor_save":

		$id_fretecep = $_POST["id_fretecep"];
		$id_fretevalor = $_POST["id_fretevalor"];

		$freightCep = new FreightCEP();

		$freightCep->Update([
			"id_fretecep" => $id_fretecep,
			"field" => "id_fretevalor",
			"value" => $id_fretevalor
		]);

		$freightCep->Read($id_fretecep);

		if ($row = $freightCep->getResult()) {

			$tplShipment = new View('templates/config_shipment');

			$row = FreightCEP::FormatFields($row);

			Send($tplShipment->getContent($row, "BLOCK_FREIGHTCEP_VALOR"));

		} else {

			Notifier::Add("Erro ao carregar dados de valor do frete.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "freight_fretegratis":

		$freight = new Freight();

		$freight->ToggleFretegratis();

        $freight->Read();

        if ($row = $freight->getResult()) {

			$row = Freight::FormatFields($row);

            Send($row['fretegratis']);

        } else {

            Notifier::Add("Erro ao carregar dados de frete!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

	break;

	case "freight_deliveryminimo":

		$freight = new Freight();

		$freight->ToggleDeliveryminimo();

        $freight->Read();

        if ($row = $freight->getResult()) {

			$row = Freight::FormatFields($row);

            Send($row['deliveryminimo']);

        } else {

            Notifier::Add("Erro ao carregar dados de frete!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

	break;

	case "freightcep_list_norules":

		$entityAddress = new EntityAddress();

		$entityAddress->getListDistinctCEP();

		if ($row = $entityAddress->getResult()) {

			$list = "";

			$freightCep = new FreightCEP();

			$tplShipment = new View("templates/config_shipment");

			do {

				if (!$freightCep->hasCEPConflict($row["cep"], $row["cep"])) {

					$row['cep_formatted'] = FormatCEP($row['cep']);

					$list.= $tplShipment->getContent($row, "EXTRA_BLOCK_FREIGHTCEP_NORULES_TR");
				}

			} while ($row = $entityAddress->getResult());

			$data = [
				"extra_block_freightcep_norules_tr" => $list
			];

			Send($tplShipment->getContent($data, "EXTRA_BLOCK_FREIGHTCEP_NORULES"));

		} else {

			Notifier::Add("Não há CEPs sem regra.", Notifier::NOTIFIER_INFO);
			Send(null);
		}

	break;

	case "freightcep_show_address":

		$cep = $_POST["cep"];

		$tplEntity = new View("templates/entity");
		$tplShipment = new View("templates/config_shipment");

		$entityAddress = new EntityAddress();

		$entityAddress->getListCEP($cep);

		$address = "";

		while ($rowAddress = $entityAddress->getResult()) {

			$rowAddress = EntityAddress::FormatFields($rowAddress);
			$rowAddress['extra_block_button_sale_address']="";
			$rowAddress['entity_bt_new_saleorder'] = "";

			$address.= $tplEntity->getContent($rowAddress, "EXTRA_BLOCK_ADDRESS");
		}

		$data = [
			"extra_block_address" => $address
		];

		Send($tplShipment->getContent($data, "EXTRA_BLOCK_FREIGHTCEP_ADDRESS_SHEET"));

	break;

	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}