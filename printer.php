<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\PrinterConfig;
use App\Legacy\Printing;
use App\Legacy\Company;
use App\Legacy\Product;
use Escpos\PrintConnectors\WindowsPrintConnector;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

function PrinterFormEdit($block, $message_error) {

	$id_impressora = $_POST['id_impressora'];

	$tplPrinter = new View('printer');

	$printer = new PrinterConfig();
	$printer->Read($id_impressora);

	if ($row = $printer->getResult()) {

		$row = PrinterConfig::FormatFields($row);

		if ($block == "EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_EDIT") {

			$row["block_printer_options"] = $tplPrinter->getContent($row, "BLOCK_PRINTER_OPTIONS");
		}

		Send($tplPrinter->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function PrinterFormCancel($block, $message_error) {

	$id_impressora = $_POST['id_impressora'];

	$tplPrinter = new View('printer');

	$printer = new PrinterConfig();
	$printer->Read($id_impressora);

	if ($row = $printer->getResult()) {

		Send($tplPrinter->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function PrinterFormSave($field, $block, $message_error) {

	$id_impressora = $_POST['id_impressora'];
	$value = $_POST['value'];

	$data = [
		'id_impressora' => (int) $id_impressora,
		'field' => $field,
		'value' => $value,
	];

	$printer = new PrinterConfig();

	$printer->Update($data);

	$tplPrinter = new View('printer');

	$printer->Read($id_impressora);

	if ($row = $printer->getResult()) {

		$row = PrinterConfig::FormatFields($row);

		Send($tplPrinter->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$printer = new PrinterConfig();

		$tplPrinter = new View('printer');

		$printers = PrinterConfig::getPrinters();

        $printer_select = "";

        foreach($printers as $print) {

			$printer_option = [
				"impressora" => $print,
				"selected" => ""
			];

			$printer_select.= $tplPrinter->getContent($printer_option, "EXTRA_BLOCK_PRINTER");
		}

		$data['extra_block_printers'] = $printer_select;
        $data['hidden'] = "";

        $printer->getList();

        $extra_block_printer = "";

        if ($row = $printer->getResult()) {

            $data['hidden'] = "hidden";

            do {

				$row = PrinterConfig::FormatFields($row);

                $extra_block_printer .= $tplPrinter->getContent($row, "EXTRA_BLOCK_PRINTER");

            } while ($row = $printer->getResult());
        }

        $data['extra_block_printer'] = $extra_block_printer;

		Send($tplPrinter->getContent($data, "BLOCK_PAGE"));

		break;

	case "printer_add":

		$printer = new PrinterConfig();

		$descricao = $_POST['descricao'];
		$printer_option = $_POST["printer_option"];

		switch($printer_option) {

			case "printer_local":

				$impressora = $_POST['printer_local_desc'];

			break;

			case "printer_share":

				$impressora = $_POST['printer_share_desc'];

				if (preg_match(WindowsPrintConnector::REGEX_SMB, $impressora) == 0) {

					Notifier::Add("Formato de endereço inválido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			break;

			case "printer_ip":

				$impressora = $_POST['printer_ip_desc'];

				if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?::(\d{1,5}))?\z/', $impressora) == 0) {

					Notifier::Add("Formato de endereço IP inválido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			break;

		}

		if ($id_impressora = $printer->Create($descricao, $impressora)) {

            $printer->Read($id_impressora);

            $tplPrinter = new View('printer');

            if ($row = $printer->getResult()) {

				$row = PrinterConfig::FormatFields($row);

                Send($tplPrinter->getContent($row, "EXTRA_BLOCK_PRINTER"));

            } else {

                Notifier::Add("Erro ao localizar impressora cadatsrada!", Notifier::NOTIFIER_ERROR);
				Send(null);
            }
        } else {

            Notifier::Add("Erro ao adicionar impressora!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }

	break;

	case "printer_descricao_edit":

		PrinterFormEdit('EXTRA_BLOCK_DESCRICAO_FORM', 'Erro ao carregar impressora!');
	break;

	case "printer_descricao_cancel":

		PrinterFormCancel('BLOCK_DESCRICAO', 'Erro ao carregar impressora!');
	break;

	case "printer_descricao_save":

		PrinterFormSave('descricao', 'BLOCK_DESCRICAO', 'Erro ao carregar impressora!');
	break;

	case "printer_impressora_edit":

		PrinterFormEdit('EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_EDIT', 'Erro ao carregar impressora!');
	break;

	// case "printer_impressora_cancel":

	// 	PrinterFormCancel('BLOCK_IMPRESSORA', 'Erro ao carregar impressora!');
	// break;

	case "printer_impressora_cancel":

		$id_impressora = $_POST['value'];

		$printer = new PrinterConfig();

		$printer->Read($id_impressora);

		if ($row = $printer->getResult()) {

			$row = PrinterConfig::FormatFields($row);

			$tplPrinter = new View("printer");

			Send($tplPrinter->getContent($row, "BLOCK_IMPRESSORA"));

		} else {

			Notifier::Add("Registro de impressora não encontrado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "printer_impressora_save":

		$printer = new PrinterConfig();

		$printer_option = $_POST["printer_option"];

		switch($printer_option) {

			case "printer_local":

				$impressora = $_POST['printer_local_desc'];

			break;

			case "printer_share":

				$impressora = $_POST['printer_share_desc'];

				if (preg_match(WindowsPrintConnector::REGEX_SMB, $impressora) == 0) {

					Notifier::Add("Formato de endereço inválido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			break;

			case "printer_ip":

				$impressora = $_POST['printer_ip_desc'];

				if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?::(\d{1,5}))?\z/', $impressora) == 0) {

					Notifier::Add("Formato de endereço IP inválido!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}

			break;

		}

		$_POST['value'] = $impressora;

		PrinterFormSave('impressora', 'EXTRA_BLOCK_PRINTER', 'Erro ao carregar impressora!');

	break;

	case "printer_change_status":

		$id_impressora = $_POST['id_impressora'];

		$printer = new PrinterConfig();
		$printer->ToggleActive($id_impressora);

		$printer->Read($id_impressora);

		if ($row = $printer->getResult()) {

			$row = PrinterConfig::FormatFields($row);

			Send($row['guilhotina']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação da guilhotina!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "printer_delete":

		$id_impressora = $_POST['id_impressora'];

		$printer = new PrinterConfig();
		$product = new Product();

		if ($product->hasPrinter($id_impressora)) {

			Notifier::Add("Existem produtos configurados para impressora. Não foi possível remover!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

			if ($printer->Delete($id_impressora)) {

				Notifier::Add("Impressora excluída com sucesso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				Notifier::Add("Erro ao excluir impressora!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		break;

	case "printer_print":

		$id_impressora = $_POST['id_impressora'];

		$printing = new Printing($id_impressora);

		if (!$printing->initialize()) {

			Notifier::Add("Erro ao inicializar impressora!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$company = new Company();

		$company->Read();

		$row = $company->getResult();

		$row = Company::FormatFields($row);

		$empresa = strtoupper($row['empresa']);

		$cnpj = "";

		if (!empty($row['cnpj'])) {

			$cnpj = "CNPJ " . $row['cnpj_formatted'];
		}

		$telefone = "";

		if (!empty($row['telefone']) || !empty($row['celular'])) {

			if (!empty($row['telefone'])) {

				$telefone = $row['telefone_formatted'];

				if (!empty($row['celular'])) {

					$telefone .= " / ";
				}
			}

			if (!empty($row['celular'])) {

				$telefone .= $row['celular_formatted'];
			}
		}

		$endereco = strtoupper($row['rua']);
		$bairro = strtoupper($row['bairro']);
		$rodape_1 = strtoupper($row['cupomlinha1']);
		$rodape_2 = strtoupper($row['cupomlinha2']);

		if (!empty($empresa)) {

			$printing->textCenter($empresa);
		}

		if (!empty($cnpj)) {

			$printing->textCenter($cnpj);
		}

		if (!empty($endereco)) {

			$printing->textCenter($endereco);
		}

		if (!empty($bairro)) {

			$printing->textCenter($bairro);
		}

		if (!empty($empresa)) {

			$printing->textCenter($telefone);
		}

		$printing->linedashspaced();
		$printing->textCenter("TESTE DE IMPRESSAO");
		$printing->linedashspaced();

		$printing->close();

		Notifier::Add("Impressão teste enviado para impressora!", Notifier::NOTIFIER_INFO);
		Send([]);

		break;

	case "printer_linefeed_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormEdit("EXTRA_BLOCK_LINEFEED_FORM", "Erro ao carregar linefeed!");

		break;

	case "printer_linefeed_cancel":

		PrinterFormCancel("BLOCK_LINEFEED", "Erro ao carregar linefeed!");

		break;

	case "printer_linefeed_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormSave('linefeed', "BLOCK_LINEFEED", "Erro ao salvar linefeed!");

		break;

	case "printer_colunas_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormEdit("EXTRA_BLOCK_COLUNAS_FORM", "Erro ao carregar colunas!");

		break;

	case "printer_colunas_cancel":

		PrinterFormCancel("BLOCK_COLUNAS", "Erro ao carregar colunas!");

		break;

	case "printer_colunas_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormSave('colunas', "BLOCK_COLUNAS", "Erro ao salvar colunas!");

		break;

	case "printer_bigfont_toggle":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

		$id_impressora = $_POST['id_impressora'];

		$printer = new PrinterConfig();

		$printer->ToggleBigfont($id_impressora);

		$printer->Read($id_impressora);

		if ($row = $printer->getResult()) {

			$row = PrinterConfig::FormatFields($row);

			Send($row['bigfont']);

		} else {

			Notifier::Add("Erro ao carregar tamanho da fonte!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "printer_popup_impressora_new":

		$tplPrinter = new View('printer');

		$printers = PrinterConfig::getPrinters();

        $printer_select = "";

        foreach($printers as $print) {

			$printer_option = [
				"impressora" => $print,
				"selected" => ""
			];

			$printer_select.= $tplPrinter->getContent($printer_option, "EXTRA_BLOCK_PRINTERS");
		}

		$data['extra_block_printers'] = $printer_select;

		$data["printer_local_checked"] = "checked";
		$data["printer_local_disabled"] = "";
		$data["printer_share_desc"] = "";
		$data["printer_share_checked"] = "";
		$data["printer_share_disabled"] = "disabled";
		$data["printer_ip_desc"] = "";
		$data["printer_ip_checked"] = "";
		$data["printer_ip_disabled"] = "disabled";

		Send($tplPrinter->getContent($data, "EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_NEW"));

		break;

	case "printer_copies_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormEdit("EXTRA_BLOCK_COPIES_FORM", "Erro ao carregar cópias!");

		break;

	case "printer_copies_cancel":

		PrinterFormCancel("BLOCK_COPIES", "Erro ao carregar cópias!");

		break;

	case "printer_copies_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);
		PrinterFormSave('copies', "BLOCK_COPIES", "Erro ao salvar cópias!");

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    	break;
}