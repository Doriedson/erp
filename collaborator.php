<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Collaborator;
use database\Entity;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_COLABORADOR);

switch ($_POST['action']) {

	case "load":

		$tplCollaborator = new View('templates/collaborator');
		
		$extra_block_collaborator = "";

		$collaborator = new Collaborator;
		$collaborator->getList();

		while ($row = $collaborator->getResult()) {

			$row = Collaborator::FormatFields($row);

            $row = Entity::FormatFields($row);

			$extra_block_collaborator.= $tplCollaborator->getContent($row, "EXTRA_BLOCK_COLLABORATOR");
		}

		$data['extra_block_collaborator'] = $extra_block_collaborator;

        $tplEntity = new View('templates/entity');

	    $data['block_entity_autocomplete_search'] = $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH");
	
        Send($tplCollaborator->getContent($data, 'BLOCK_PAGE'));

		break;

    case "collaborator_add":

        $collaborator = $_POST['value'];

        $entity = new Entity();

        if( is_numeric($collaborator) ) {

			$entity->Read($collaborator);

		} else {

			$entity->ReadName($collaborator);
		}

        if ($row = $entity->getResult()) {

            $id_entidade = $row['id_entidade'];

            $collaborator = new Collaborator();

            $collaborator->Read($id_entidade);

            if ($row = $collaborator->getResult()) {

                Notifier::Add("Colaborador já está cadastrado!", Notifier::NOTIFIER_INFO);
                Send(null);

            } else {

                $collaborator->Create($id_entidade, '1234');
                                    
                $collaborator->Read($id_entidade);

                if ($row = $collaborator->getResult()) {
                
                    $tplCollaborator = new View('templates/collaborator');

                    $row = Collaborator::FormatFields($row);

                    $row = Entity::FormatFields($row);
                    
                    Send($tplCollaborator->getContent($row, "EXTRA_BLOCK_COLLABORATOR"));

                } else {

                    Notifier::Add("Erro ao cadastrar colaborador!", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }
            }

        } else {

            Notifier::Add("Não foi possível localizar o colaborador: $collaborator", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    
    break;

    case "collaborator_del":

        $id_entidade = $_POST['value'];

        $collaborator = new Collaborator();

        if ($GLOBALS['authorized_id_entidade'] == $id_entidade) {

            Notifier::Add("Não é possível excluir colaborador logado!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        if ($collaborator->Delete($id_entidade) == 1) {

            Notifier::Add("Colaborador removido com sucesso!", Notifier::NOTIFIER_DONE);
            Send([]);
            
        } else {

            Notifier::Add("Error ao remover colaborador!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;

    case "collaborator_access":

        $id_entidade = $_POST['id_entidade'];
        $value = ($_POST['value'] == "true")? 1: 0;
        $key = $_POST['key'];

        $level_access = constant("\database\ControlAccess::" . $key);

        $collaborator = new Collaborator();

        $collaborator->Read($id_entidade);

        if ($row = $collaborator->getResult()) {

            $access = json_decode($row['acesso']);

            if ($level_access == ControlAccess::CA_SERVIDOR && $GLOBALS['authorized_id_entidade'] == $id_entidade) {

                Notifier::Add("Não é possível remover acesso ao servidor do usuário logado! " . $GLOBALS['authorized_id_entidade'], Notifier::NOTIFIER_ERROR);
                Send(null);
            }

            if ($level_access == ControlAccess::CA_SERVIDOR_COLABORADOR && $GLOBALS['authorized_id_entidade'] == $id_entidade) {

                Notifier::Add("Não é possível remover acesso ao cadastro de colaborador do usuário logado!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

            // $access = ($access & ~constant("\database\ControlAccess::" . $key));

            // if (constant("\database\ControlAccess::" . $key) == ControlAccess::CA_SERVIDOR) {

            //     $access = $access & ~(ControlAccess::CA_SERVIDOR_PRODUTO|ControlAccess::CA_SERVIDOR_PRODUTO_SETOR|ControlAccess::CA_SERVIDOR_CLIENTE|ControlAccess::CA_SERVIDOR_COLABORADOR|ControlAccess::CA_SERVIDOR_FORNECEDOR|ControlAccess::CA_SERVIDOR_ORDEM_COMPRA|ControlAccess::CA_SERVIDOR_PRODUTO_PRECO|ControlAccess::CA_SERVIDOR_ORDEM_COMPRA_LISTA|ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR|ControlAccess::CA_SERVIDOR_CONTAS_A_RECEBER|ControlAccess::CA_SERVIDOR_EMISSAO_RECIBO|ControlAccess::CA_SERVIDOR_ORDEM_VENDA|ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO|ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO|ControlAccess::CA_SERVIDOR_CONFIG|ControlAccess::CA_SERVIDOR_RELATORIO|ControlAccess::CA_SERVIDOR_CONFIG|ControlAccess::CA_SERVIDOR_CONFIG|ControlAccess::CA_SERVIDOR_ORDEM_VENDA_FRETE|ControlAccess::CA_SERVIDOR_CONFIG|ControlAccess::CA_PRODUTO_ESTOQUE_ADD|ControlAccess::CA_PRODUTO_ESTOQUE_DEL|ControlAccess::CA_VENDA_PRAZO_SEM_LIMITE|ControlAccess::CA_TRANSFERENCIA_MESA);

            // } else if (constant("\database\ControlAccess::" . $key) == ControlAccess::CA_PDV) {

            //     $access = $access & ~(ControlAccess::CA_PDV_SANGRIA|ControlAccess::CA_PDV_REFORCO|ControlAccess::CA_PDV_CANCELA_ITEM|ControlAccess::CA_PDV_CANCELA_VENDA|ControlAccess::CA_PDV_DESCONTO);
            // }

            $access[$level_access] = $value;

            $collaborator->setAccess($id_entidade, $access);

            $collaborator->Read($id_entidade);

            if ($row = $collaborator->getResult()) {

                $row = Collaborator::FormatFields($row);

                $tplCollaborator = new View("templates/collaborator");

                Send($tplCollaborator->getContent($row, "BLOCK_ACCESS"));

            } else {

                Notifier::Add("Ocorreu um erro na alteração de acesso!", Notifier::NOTIFIER_ERROR);
                Send(null);
            }

        } else {

            Notifier::Add("Erro na alteração de acesso do colaborador!", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

    break;	        
}