<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Table;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

function TableConfigGetTables() {

    $table = new Table();

    $table->getList();

    $tplTable = new View("templates/table_config");

    $tables = "";

    if ($row = $table->getResult()) {

        do {

            $row = Table::FormatFields($row);

            $tables .= $tplTable->getContent($row, "EXTRA_BLOCK_TABLE");

        } while ($row = $table->getResult());
    }

    return $tables;
}

switch($_POST['action']) {
	
	case "load":

        $tplTable = new View("templates/table_config");

        $data = [
            "extra_block_table" => TableConfigGetTables()
        ];

        $data['tableconfig_notfound'] = "hidden";

        if (empty($data['extra_block_table'])) {

            $data['tableconfig_notfound'] = "";
        }

		Send($tplTable->getContent($data, "BLOCK_PAGE"));

    break;

    case "add":

        $number_of_tables = (int) $_POST['number_of_tables'];
        $id_start = (int) $_POST['id_start'];

        if ($number_of_tables == 0) {

            Notifier::Add("Digite um número válido para adicionar mesas!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $table = new Table();

        if($table->Create($number_of_tables, $id_start)) {

            if ($number_of_tables == 1) {

                Notifier::Add("Foi adicionado $number_of_tables mesa!", Notifier::NOTIFIER_DONE);
                Send(TableConfigGetTables());

            } else {

                Notifier::Add("Foram adicionadas $number_of_tables mesas!", Notifier::NOTIFIER_DONE);
                Send(TableConfigGetTables());
            }

        } else {

            Notifier::Add("Erro ao adicionar mesas!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "table_del":

        $id_mesa = $_POST['id_mesa'];

        $table = new Table();

        $table->Read($id_mesa);

        if($row = $table->getResult()) {

            if ($row['id_venda']) {

                Notifier::Add("Mesa em uso!", Notifier::NOTIFIER_ERROR);
                Send(null);

            } else {

                if($table->Delete($id_mesa)) {

                    Notifier::Add("Mesa removida com sucesso!", Notifier::NOTIFIER_DONE);
                    Send([]);
    
                } else {
    
                    Notifier::Add("Erro ao remover mesa!", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }
    
            }

        } else {

            Notifier::Add("Erro ao remover mesa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "table_mesa_edit":

        $id_mesa = $_POST['id_mesa'];

        $table = new Table();

        $table->Read($id_mesa);

        if($row = $table->getResult()) {

            $tplTable = new View('templates/table_config');

            Send($tplTable->getContent($row, "EXTRA_BLOCK_TABLE_FORM_MESA"));

        } else {

            Notifier::Add("Erro ao editar mesa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "table_mesa_cancel":

        $id_mesa = $_POST['id_mesa'];

        $table = new Table();

        $table->Read($id_mesa);

        if($row = $table->getResult()) {

            $tplTable = new View('templates/table_config');

            $row = Table::FormatFields($row);

            Send($tplTable->getContent($row, "BLOCK_TABLE_MESA"));

        } else {

            Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "table_mesa_save":

        $id_mesa = $_POST['id_mesa'];
        $mesa = trim($_POST['value']);

        if ($mesa == "") {

            Notifier::Add("Disgite uma descrição para mesa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $table = new Table();

        if ($table->hasDuplicated($id_mesa, $mesa)) {

            Notifier::Add("Já existe outra mesa com essa descrição!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $table->Update([
            "id_mesa" => $id_mesa,
            "field" => "mesa",
            "value" => $mesa
        ]);

        $table->Read($id_mesa);

        if($row = $table->getResult()) {

            $tplTable = new View('templates/table_config');

            $row = Table::FormatFields($row);
            
            Send($tplTable->getContent($row, "BLOCK_TABLE_MESA"));

        } else {

            Notifier::Add("Erro ao carregar dados da mesa!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        break;

    case "tableconfig_popup_new":

        $tplTable = new View('templates/table_config');

        Send($tplTable->getContent([], "EXTRA_BLOCK_POPUP_TABLECONFIG_NEW"));

        break;

	default:

        Notifier::Add("Requisição inválida", Notifier::NOTIFIER_ERROR);
        Send(null);
	break;
}