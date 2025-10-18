<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\SaleOrder;
use database\Entity;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$data = ['data' => date('Y-m-d')];

		$tplEntityCredit = new View('templates/report_entitycredit');

		Send($tplEntityCredit->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_entitycredit_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;

		$entity = new Entity();

		if ($intervalo == false) {

	  		$datafim = $dataini;
		}

		$entity->getEntityCreditByDateInterval($dataini, $datafim);

		$tplEntityCredit = new View('templates/report_entitycredit');

		$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
		$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

		if ($intervalo) {

			$header = "$dataini_formatted a $datafim_formatted";

		} else {

			$header = "$dataini_formatted";
		}

		$data['header'] = $header;

		// $subtotal = 0;
		// $total = 0;
		// $extra_block_entitycredit_tip = "";

		// $extra_block_entitycredit_waiter = "";

		if ($row = $entity->getResult()) {

			$id_colaborador = "";
			$colaborador = "";

			$collaborator = [];

			do {

				if (!array_key_exists($row["id_entidade"], $collaborator)) {

					$collaborator[$row["id_entidade"]]["nome"] = $row["nome"];
					$collaborator[$row["id_entidade"]]["id_entidade"] = $row["id_entidade"];
					$collaborator[$row["id_entidade"]]["totalc"] = 0;
					$collaborator[$row["id_entidade"]]["totald"] = 0;
					$collaborator[$row["id_entidade"]]["rows"] = [];
				}

				if ($row["valor"] > 0) {

					$collaborator[$row["id_entidade"]]["totalc"] += $row["valor"];

				} else {

					$collaborator[$row["id_entidade"]]["totald"] += -$row["valor"];
				}

				$row["data_formatted"] = date_format( date_create($row["data"]), "d/m/Y");


				$collaborator[$row["id_entidade"]]["rows"][] = $row;

				// $subtotal+= $row["valor_servico"];

				// $row = SaleOrder::FormatFields($row);

				// $extra_block_entitycredit_tip.= $tplEntityCredit->getContent($row, "EXTRA_BLOCK_WAITERTIP_TIP");

				// $id_colaborador = $row["id_colaborador"];
				// $colaborador = $row["nome"];

				// $row = $entity->getResult();

				// if (!$row || $id_colaborador != $row['id_colaborador']) {

				// $data_row = [];

				// $data_row = ['subtotal_formatted' => number_format($subtotal, 2, ",", ".")];

				// $data_row['colaborador'] = $colaborador;

				// $data_row['extra_block_entitycredit_tip'] = $extra_block_entitycredit_tip;

				// $extra_block_entitycredit_waiter.= $tplEntityCredit->getContent($data_row, "EXTRA_BLOCK_WAITERTIP_WAITER");

				// $extra_block_entitycredit_tip = "";

				// $total+= $subtotal;
				// $subtotal = 0;
				// }

			} while ($row = $entity->getResult());

			$entitycredit = "";

			foreach ($collaborator as $collab) {

				$content = "";

				foreach ($collab["rows"] as $row) {

					if ($row['valor'] > 0) {

						$row["valor_formatted"] = number_format($row["valor"], 2, ",", ".");
						$content .= $tplEntityCredit->getContent($row, "EXTRA_BLOCK_ENTITYCREDIT_CREDIT");

					} else {

						$row["valor_formatted"] = number_format(-$row["valor"], 2, ",", ".");
						$content .= $tplEntityCredit->getContent($row, "EXTRA_BLOCK_ENTITYCREDIT_DEBIT");
					}
				}

				$row = [
					"nome" => $collab["nome"],
					"id_entidade" => $collab["id_entidade"],
					"totalc_formatted" => number_format($collab["totalc"], 2, ",", "."),
					"totald_formatted" => number_format($collab["totald"], 2, ",", "."),
					"extra_block_entitycredit_data" => $content
				];

				$entitycredit .= $tplEntityCredit->getContent($row, "EXTRA_BLOCK_ENTITYCREDIT");
			}

		} else {

			Notifier::Add("Nenhum relatório encontrado:<br>$header", Notifier::NOTIFIER_INFO);
	  		Send(null);
		}

		$data['extra_block_entitycredit'] = $entitycredit;
		// $data['total_formatted'] = number_format($total, 2, ",", ".");

		$content = [
			'data' => $tplEntityCredit->getContent($data, "EXTRA_BLOCK_ENTITYCREDIT_CONTENT"),
		];

		Send($content);

		break;
}