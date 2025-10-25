<?php

use App\Legacy\Log;
use App\Legacy\Notifier;
// use App\Legacy\Company;
//
use App\Legacy\PrinterConfig;
use App\Legacy\ProductExpDate;
use App\View\View;
use App\Legacy\Config;

require "inc/config.inc.php";
require "inc/authorization.php";

function CPFormEdit($block, $message_error) {

	$tplCP = new View('home');

	$config = new Config();
	$config->Read();

	if ($row = $config->getResult()) {

		// $row = Config::FormatFields($row);

		Send($tplCP->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
        Send(null);
	}

}

function CPFormCancel($block, $message_error) {

	$tplCP = new View('home');

	$config = new Config();
	$config->Read();

	if ($row = $config->getResult()) {

		// $row = Config::CPFormatFields($row);

		Send($tplCP->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
        Send(null);
	}
}

function CPFormSave($field, $block, $message_error) {

	$value = $_POST['value'];

	$data = [
		'field' => $field,
		'value' => $value,
	];

	$config = new Config();

	$config->Update($data);

	$tplCP = new View('home');

	$config->Read();

	if ($row = $config->getResult()) {

		// $row = Config::FormatFields($row);

		Send($tplCP->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
        Send(null);
	}
}

switch($_POST['action']) {

	case "load":

              $tplHome = new View("home");

              //$company = new Company();

              //$company->Read();

              //$empresa = "Nome da Empresa";

              //if ($row = $company->getResult()) {

                    // $empresa = $row['empresa'];
              //}

              $date = new DateTimeImmutable();

              [$product_list, $expirated, $toexpirate, $extra_block_expiratedays] = ProductExpDate::getListHUD();

              if (is_null($product_list)) {

                     Notifier::Add("Erro ao carregar dados!", Notifier::NOTIFIER_ERROR);
                     Send(null);
              }

              $tplCP = new View("home");

              $data = [
                     //"empresa" => $empresa,
                     "timestamp" => $date->getTimestamp(),
                     "expirated" => $expirated,
                     "toexpirate" => $toexpirate,
                     "extra_block_expiratedays" => $extra_block_expiratedays
              ];

              Send($tplHome->getContent($data, "BLOCK_PAGE"));

              break;

       case "cp_expiratedays_edit":

              CPFormEdit('EXTRA_BLOCK_EXPIRATEDAYS_FORM', 'Erro ao carregar dados!');
              break;

       case "cp_expiratedays_cancel":

              CPFormCancel('EXTRA_BLOCK_EXPIRATEDAYS', 'Erro ao carregar dados!');
              break;

       case "cp_expiratedays_save":

              CPFormSave('product_expirate_days', 'EXTRA_BLOCK_EXPIRATEDAYS', 'Erro ao carregar dados!');
              break;

       case "cp_expiratedays_update":

              [$product_list, $expirated, $toexpirate, $extra_block_expiratedays] = ProductExpDate::getListHUD();

              if (!is_null($product_list)) {

                     $tplCP = new View("home");

                     $data = [
                            "data" => $product_list,
                            "expirated" => $expirated,
                            "toexpirate" => $toexpirate,
                            "extra_block_expiratedays" => $extra_block_expiratedays
                     ];

                     Send($data);

              } else {

                     Notifier::Add("Erro ao carregar lista de validade de produtos!", Notifier::NOTIFIER_ERROR);
                     Send(null);
              }

              break;

       case "cp_expiratedays_print":

              $config = new Config();
              $config->Read();

              if ($row = $config->getResult()) {

              $printer = new PrinterConfig();

              $printer->getPrinting(PrinterConfig::PRINTING_PRODUCTEXPIRATE);

              if ($rowPrinter = $printer->getResult()) {

                     if (is_null($rowPrinter['id_impressora'])) {

                            Notifier::Add("Defina uma impressora em Configuração / Impressão", Notifier::NOTIFIER_INFO);
                            Send(null);
                     }

                     if (ProductExpDate::DoPrint($row['product_expirate_days'], $rowPrinter['id_impressora'])) {

                            Notifier::Add("Lista impressa.", Notifier::NOTIFIER_DONE);
                            Send([]);

                     } else {

                            // Notifier::Add("Erro ao imprimir pedido!");
                            Notifier::Add("Erro ao imprimir lista de validade dos produtos!", Notifier::NOTIFIER_ERROR);
                            Send(null);
                     }

              } else {

                     // Notifier::Add("Erro ao imprimir pedido!");
                     Notifier::Add("Erro ao imprimir lista de validade dos produtos!", Notifier::NOTIFIER_ERROR);
                     Send(null);
              }

              } else {

                     Notifier::Add("Erro ao imprimir lista de validade dos produtos!", Notifier::NOTIFIER_ERROR);
                     Send(null);
              }

              break;

       case "productexpdate_popup_list":

              [$product_list, $expirated, $toexpirate, $extra_block_expiratedays] = ProductExpDate::getListHUD();

              if (!is_null($product_list)) {

                     $tplCP = new View("home");

                     $cp_expdate_notfound = "hidden";
                     $productexpdate_bt_print = "";

                     if ($expirated == 0 && $toexpirate == 0) {

                            $cp_expdate_notfound = "";
                            $productexpdate_bt_print = "hidden";
                     }

                     $data = [
                            "extra_block_cp_expdate_tr" => $product_list,
                            "extra_block_expiratedays" => $extra_block_expiratedays,
                            "cp_expdate_notfound" => $cp_expdate_notfound,
                            "productexpdate_bt_print" => $productexpdate_bt_print
                     ];

                     Send($tplCP->getContent($data, "EXTRA_BLOCK_POPUP_CP_EXPDATE"));

              } else {

                     Notifier::Add("Erro ao carregar lista de validade de produtos!", Notifier::NOTIFIER_ERROR);
                     Send(null);
              }

              break;

       case "updatelog":

              $log = new Log();

              $log->UpdateLog();

              Send([1]);

//Verificar as linhas com erro
              // $ret = $log->UpdateLog();
              // Send($ret);

 	default:

              Notifier::Add("Requisição inválida", Notifier::NOTIFIER_ERROR);
              Send(null);
              break;
}