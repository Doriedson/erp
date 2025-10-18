<?php

use database\ControlAccess;
use database\Notifier;
use App\View\View;
use database\SaleOrder;
use database\Product;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

switch ($_POST['action']) {

	case "load":

		$data = ['data' => date('Y-m-d')];

		$tplWaitertip = new View('templates/report_waitertip');

		Send($tplWaitertip->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_waitertip_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;

		$saleorder = new SaleOrder();

		if ($intervalo) {

			$saleorder->SearchWaitertipByDateInterval($dataini, $datafim);

		} else {

			$saleorder->SearchWaitertipByDate($dataini);
		}

		$tplWaitertip = new View('templates/report_waitertip');

        $dataini_formatted = date_format(date_create($dataini), 'd/m/Y');
        $datafim_formatted = date_format(date_create($datafim), 'd/m/Y');

        if ($intervalo) {

            $header = "$dataini_formatted a $datafim_formatted";

        } else {

            $header = "$dataini_formatted";
        }

        $data['header'] = $header;

        $subtotal = 0;
        $total = 0;
        $extra_block_waitertip_tip = "";

        $extra_block_waitertip_waiter = "";

		if ($row = $saleorder->getResult()) {

			$id_colaborador = "";
            $colaborador = "";
			// $tr = "";

			//




			do {



				$subtotal+= $row['valor_servico'];

				// $row['subtotal_formatted'] = number_format($row['valor_servico'], 2, ',', '.');

			// 	$row['custo_formatted'] = number_format(round($row['subtotal'] / $row['qtd'], 2), 2, ',', '.');

			// 	$row['qtd_formatted'] = number_format($row['qtd'], 3, ',', '.');

				$row = SaleOrder::FormatFields($row);

				$extra_block_waitertip_tip.= $tplWaitertip->getContent($row, "EXTRA_BLOCK_WAITERTIP_TIP");

                $id_colaborador = $row['id_colaborador'];
                $colaborador = $row["nome"];

                $row = $saleorder->getResult();

                if (!$row || $id_colaborador != $row['id_colaborador']) {

                    $data_row = [];

                    // 		if ($id_colaborador != "") {

                    			$data_row = ['subtotal_formatted' => number_format($subtotal, 2, ",", ".")];

                    			$data_row['colaborador'] = $colaborador;

                    			$data_row['extra_block_waitertip_tip'] = $extra_block_waitertip_tip;

                    			$extra_block_waitertip_waiter.= $tplWaitertip->getContent($data_row, "EXTRA_BLOCK_WAITERTIP_WAITER");

                    			$extra_block_waitertip_tip = "";

                    // 		}

                    // 		$id_colaborador = $row['produtosetor'];

                    		$total+= $subtotal;
                    		$subtotal = 0;
                        }

			} while ($row);


			// $data = ['subtotal_formatted' => number_format($subtotal, 2, ",", ".")];

			// $data['produtosetor'] = $id_colaborador;

			// $data['extra_block_waitertip_tip'] = $extra_block_waitertip_tip;

			// $extra_block_waitertip_waiter.= $tplWaitertip->getContent($data, "EXTRA_BLOCK_SETOR_GRUPO");

			// $total+= $subtotal;


			//

		} else {

			Notifier::Add("Nenhum relatório encontrado:<br>$header", Notifier::NOTIFIER_INFO);
            $extra_block_waitertip_waiter = $tplWaitertip->getContent([], "EXTRA_BLOCK_WAITERTIP_NOTFOUND");
		}

        $data['extra_block_waitertip_waiter'] = $extra_block_waitertip_waiter;
        $data['total_formatted'] = number_format($total, 2, ",", ".");

        $content = [
            'data' => $tplWaitertip->getContent($data, "EXTRA_BLOCK_WAITERTIP_CONTENT"),
            'total_formatted' => $data['total_formatted']
        ];

        Send($content);
	break;
}