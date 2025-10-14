<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Receipt;
use database\Entity;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_EMISSAO_RECIBO);

switch ($_POST['action']) {

    case "load":

        $tplReceipt = new View('templates/receipt');

        $receipt = new Receipt();

        $receipt->getList();
    
        $extra_block_receipt = "";

        if ($row = $receipt->getResult()) {

            do {
            
                $row = Receipt::FormatFields($row);

                $row = Entity::FormatFields($row);

                $extra_block_receipt.= $tplReceipt->getContent($row, "EXTRA_BLOCK_RECEIPT");

            } while ($row = $receipt->getResult());

            $data = [
                "receipt_bt_clear_visibility" => "",
                "receipt_bt_print_visibility" => ""
            ];
        
        } else {

            $extra_block_receipt = $tplReceipt->getContent([], "EXTRA_BLOCK_RECEIPT_NONE");


            $data = [
                "receipt_bt_clear_visibility" => "hidden",
                "receipt_bt_print_visibility" => "hidden"
            ];
        }
    
        // $data['data'] = date('Y-m-d');
        $data['extra_block_receipt'] = $extra_block_receipt;
        
        // $tplEntity = new View('templates/entity');

	    // $data['block_entity_autocomplete_search'] = $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH");

        Send($tplReceipt->getContent($data, "BLOCK_PAGE"));
    
    break;

    case "receipt_add":

        $data = $_POST['data'];
        $collaborator = $_POST['nome'];
        $valor = $_POST['valor'];
        $motivo = $_POST['motivo'];

        $entity = new Entity();

        if( is_numeric($collaborator) ) {

			$entity->Read($collaborator);

		} else {

			$entity->ReadName($collaborator);
		}

        if ($row = $entity->getResult()) {
            
            $receipt = new Receipt();

            $data = [
                "data" => $data,
                "id_entidade" => $row['id_entidade'],
                "valor" => $valor,
                "motivo" => $motivo,
            ];

            $id_receipt = $receipt->Create($data);
    
            $receipt->Read($id_receipt);
    
            if ($row = $receipt->getResult()) {

                $tplReceipt = new View('templates/receipt');

                $row = Receipt::FormatFields($row);

                $row = Entity::FormatFields($row);

                Notifier::Add("Novo recibo emitido para:<br>" . $row['nome'], Notifier::NOTIFIER_DONE);
                Send($tplReceipt->getContent($row, "EXTRA_BLOCK_RECEIPT"));

            } else {

                Notifier::Add("Erro ao cadastrar novo recibo!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

        } else {

            Notifier::Add("Nome não encontrado no cadastro de clientes!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    break;

    case "receipt_delete":

        $id_recibo = $_POST['id_recibo'];
    
        $receipt = new Receipt();

        if ($receipt->Delete($id_recibo)) {

            $receipt->getList();

            if ($receipt->getResult()) {

                Notifier::Add("Recibo removido com sucesso.", Notifier::NOTIFIER_DONE);
                Send([]);

            } else {

                $tplReceipt = new View('templates/receipt');

                Notifier::Add("Recibo removido com sucesso.", Notifier::NOTIFIER_DONE);
                Send($tplReceipt->getContent([], "EXTRA_BLOCK_RECEIPT_NONE"));
            }
            
        } else {
        
            Notifier::Add("Erro ao remover recibo!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    break;        

    case "receipt_delete_all":

        $receipt = new Receipt();

        if($receipt->DeleteAll()) {
        
            $tplReceipt = new View('templates/receipt');

            Notifier::Add("Todos os rebibos foram removidos com sucesso.", Notifier::NOTIFIER_DONE);
            Send($tplReceipt->getContent([], "EXTRA_BLOCK_RECEIPT_NONE"));
        
        } else {

            Notifier::Add("Não há recibos para excluir!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    break;
        
    case "receipt_print":

        $receipt = new Receipt();
        
        $receipt->getList();

        if ($row = $receipt->getResult()) {

            $tplReceipt = new View("templates/receipt_print");

            $response = "";

            do {
                $row = Receipt::FormatFields($row);

                $response.= $tplReceipt->getContent($row, "EXTRA_BLOCK_RECEIPT");

            } while ($row = $receipt->getResult());

            Send($tplReceipt->getContent(["extra_block_receipt" => $response], "BLOCK_PAGE"));

        } else {

            Notifier::Add("Não há recibos para impressão!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    break;

    case "receipt_popup":

        $id_entidade = $_POST['id_entidade'];
        $nome = $_POST['nome'];

        $tplReceipt = new View("templates/receipt");
        $tplEntity = new View('templates/entity');
        
        $data = [
            "id_entidade" => $id_entidade,
            "nome" => $nome,
            "date" => date('Y-m-d'),
            'block_entity_autocomplete_search' => $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH")
        ];

        Send($tplReceipt->getContent($data, "EXTRA_BLOCK_POPUP_NEWRECEIPT"));

        break;

    default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}