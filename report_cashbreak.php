<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\SaleOrder;
use database\CashAdd;
use database\CashDrain;
use database\Entity;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":
	
		$tplCashbreak = new View("templates/report_cashbreak");

		$data = ['data' => date('Y-m-d')];

		Send($tplCashbreak->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_cashbreak_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
		
		$tplCashbreak = new View('templates/report_cashbreak');

		$sale = new SaleOrder();

		if ($intervalo) {

			$sale->SearchCashBreakByDateInterval($dataini, $datafim);

		} else {

			$sale->SearchCashBreakByDate($dataini);
		}

		if ($row = $sale->getResult()) {
	
			$dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
			$datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

			if ($intervalo) {

				$content['header'] = "$dataini_formatted a $datafim_formatted";

			} else {

				$content['header'] = "$dataini_formatted";
			}

			$cashbreak_entry = "";
			$cashbreak_collaborator = "";
			$colaborador='';
			$quebraP=0;
			$quebraN=0;
			$total = 0;
			
			$cashAdd = new CashAdd();
			$cashDrain = new CashDrain();
			
			do {

				if ($colaborador != $row['nome']) {

					if ($colaborador != '') {

						$data['extra_block_cashbreak_entry'] = $cashbreak_entry;

						$data['quebraP'] = number_format($quebraP,2,",",".");
						$data['quebraN'] = number_format($quebraN,2,",",".");

						$quebra = $quebraP - $quebraN;

						$total += $quebra;

						if ($quebra > 0) {
							
							$data['quebra_formatted'] = number_format($quebra,2,",",".");
							$data['extra_block_cashbreak_total'] = $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_TOTAL_P");

						} else {

							$data['quebra_formatted'] = number_format(-$quebra,2,",",".");
							$data['extra_block_cashbreak_total'] = $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_TOTAL_N");
						}

						$cashbreak_collaborator .= $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_COLLABORATOR");
						// $cashbreak_entry .= $tplCashbreak->getContent($data, "EXTRA_BLOCK_TR_QUEBRA");
						$cashbreak_entry = "";
					}

					$colaborador = $row['nome'];
					$quebraP = 0;
					$quebraN = 0;

					$row = Entity::FormatFields($row);

					$data['nome'] = $row['nome'];
					$data['extra_block_entity_button_status'] = $row['extra_block_entity_button_status'];
					
					
					// $cashbreak_entry .= $tplCashbreak->getContent($row, "EXTRA_BLOCK_TR_COLABORADOR");
				}
			
				$cashDrain->getTotalByCashier($row['id_caixa']);

				$quebra = $row['total'] - $row['trocofim'] + $row['trocoini'];

				if ($row2 = $cashDrain->getResult()) {
				
					$quebra -= $row2['total'];
				}

				$cashAdd->getTotalByCashier($row['id_caixa']);

				if ($rowCashAdd = $cashAdd->getResult()) {
				
					$quebra += $rowCashAdd['total'];
				}

				$row['data_formatted'] = date_format( date_create($row['dataini']), 'd/m/Y');
				$row['total_formatted'] = number_format($row['total'],2,",",".");

				if ($quebra > 0) {

					// Faltou
					$row['quebra_formatted'] = number_format($quebra,2,",",".");
					$row['extra_block_cashbreak_value'] = $tplCashbreak->getContent($row, "EXTRA_BLOCK_CASHBREAK_POSITIVE");
					$quebraP += $quebra;

				}else{
					// Sobrou
					$row['quebra_formatted'] = number_format(-$quebra,2,",",".");
					$row['extra_block_cashbreak_value'] = $tplCashbreak->getContent($row, "EXTRA_BLOCK_CASHBREAK_NEGATIVE");
					$quebraN += -$quebra;
				}

				$cashbreak_entry .= $tplCashbreak->getContent($row, "EXTRA_BLOCK_CASHBREAK_ENTRY");

			} while($row = $sale->getResult());

			$data['extra_block_cashbreak_entry'] = $cashbreak_entry;

			$data['quebraP'] = number_format($quebraP,2,",",".");
			$data['quebraN'] = number_format($quebraN,2,",",".");

			$quebra = $quebraP - $quebraN;
			$total += $quebra;

			if ($quebra > 0) {
							
				$data['quebra_formatted'] = number_format($quebra,2,",",".");
				$data['extra_block_cashbreak_total'] = $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_TOTAL_P");

			} else {

				$data['quebra_formatted'] = number_format(-$quebra,2,",",".");
				$data['extra_block_cashbreak_total'] = $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_TOTAL_N");
			}

			$cashbreak_collaborator .= $tplCashbreak->getContent($data, "EXTRA_BLOCK_CASHBREAK_COLLABORATOR");

			$content['extra_block_cashbreak_collaborator'] = $cashbreak_collaborator;

			Send([
				"data" => $tplCashbreak->getContent($content, "EXTRA_BLOCK_CONTENT"),
				"total" => number_format(-$total, 2, ",", ".")
			]);
			
		} else {

			Notifier::Add("Relatório não encontrado para a data informada.", Notifier::NOTIFIER_INFO);
			Send([
				"data" => $tplCashbreak->getContent([], "EXTRA_BLOCK_REPORT_CASHBREAK_NOT_FOUND"),
				"total" => "0,00"
			]);
		}

	break;			
}


