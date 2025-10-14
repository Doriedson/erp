<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Company;
use database\Entity;
use database\EntityAddress;
use database\ValidaCPFCNPJ;
use database\FreightCEP;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST['action']) {

	case "load":

		$tplEntity = new View('templates/entity');

		Send($tplEntity->getContent([], "BLOCK_PAGE"));

	break;

	case "entity_smart_search":

		$value = $_POST['value'];

		// $value = Clean::HtmlChar(trim($_POST['value']));
		// $value = Clean::DuplicateSpace($value);

		$source = $_POST['source'];

		$entity = new Entity();

		$tplEntity = new View("templates/entity");

		if( is_numeric($value) ) {

			$entity->SearchByCode($value);

		} else {

			// if (mb_strlen($value) < 3) {

			// 	Send($tplEntity->getContent($row, "BLOCK_ITEM_SEARCH_NONE"));
			// 	exit();
			// }

			if (empty($value)) {

				Send([]);
			}

			$entity->Search($value);
		}

		$entity_list = "";

		if ($row = $entity->getResult()) {

			do {

				$row = Entity::FormatFields($row);

				switch($source) {

					case "popup":

						$entity_list.= $tplEntity->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH");
						break;

					case "entity":

						$row['block_entity_credit'] = $tplEntity->getContent($row, "BLOCK_ENTITY_CREDIT");
						$entity_list.= $tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY");
						break;
				}

			} while ($row = $entity->getResult());

		} else {

			$entity_list = []; // $tplEntity->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND");
		}

		Send($entity_list);

		break;

	case "entity_listall":

		$entity = new Entity();

		$entity->getList();

		$result = "";

		if ($row = $entity->getResult()) {

			$tplEntity = new View('templates/entity');

			do {

				$row = Entity::FormatFields($row);

				$row['block_entity_credit'] = $tplEntity->getContent($row, "BLOCK_ENTITY_CREDIT");

				$result.= $tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY");

			} while ($row = $entity->getResult());

			Send($result);

		} else {

			Notifier::Add("Erro na consulta de clientes!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$entity = new Entity();

		$id_entidade = $entity->Create([
			'id_entidade' => 0,
			'cpfcnpj' => null,
			'nome' => 'Novo Cliente',
			'telcelular' => '',
			'telresidencial' => '',
			'telcomercial' => '',
			'credito' => 0,
			'limite' => 0,
			'email' => '',
			'obs' => '',
			'ativo' => 1,
			]);

		$entity->Read($id_entidade);

		if($row = $entity->getResult()) {

			$tplEntity = new View("templates/entity");

			$row = Entity::FormatFields($row);

			$row['extra_block_address'] = "";

			$tplSale = new View("templates/sale_order");

			$row['block_history_order'] = "";// $tplSale->getContent(["id_entidade" => $id_entidade], "BLOCK_HISTORY_ORDER");

			Send([
				"data" => $tplEntity->getContent($row, "EXTRA_BLOCK_TR"),
				"id_entidade" => $id_entidade
			]);

		} else {

			Notifier::Add("Ocorreu um erro ao cadastrar novo cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_create_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$cpfcnpj = $_POST['cpfcnpj'];
		$nome = $_POST['nome'];
		$telcelular = $_POST['telcelular'];
		$telresidencial = $_POST['telresidencial'];
		$telcomercial = $_POST['telcomercial'];
		$email = $_POST['email'];
		$obs = $_POST['obs'];

		$entity = new Entity();

		if (trim($nome) == "") {

			Notifier::Add("Digite um nome para o cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		if ($cpfcnpj == "") {

			$cpfcnpj = null;

		} else {

			$valida_cpf_cnpj = new ValidaCPFCNPJ($cpfcnpj);

			if (!$valida_cpf_cnpj->valida()) {

				Notifier::Add("CPF ou CNPJ inválido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$entity->SearchByCode($cpfcnpj);

			if ($row = $entity->getResult()) {

				Notifier::Add("CPF/CNPJ já cadastro. Cliente: " . $row['nome'] . ".", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$id_entidade = $entity->Create([
			'cpfcnpj' => $cpjcnpj,
			'nome' => $nome,
			'telcelular' => $telcelular,
			'telresidencial' => $telresidencial,
			'telcomercial' => $telcomercial,
			'email' => $email,
			'obs' => $obs,
		]);

		if ($id_entidade) {

			Send($id_entidade);

		} else {

			Notifier::Add("Ocorreu um erro ao cadastrar novo cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_change_status":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity;

		$entity->ToggleActive($id_entidade);

		$entity->SearchByCode($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			Send($row['extra_block_entity_button_status']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_address_new":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$page = $_POST['page'];

		$address = new EntityAddress();

		$id_endereco = $address->Create($id_entidade);

		$address->Read($id_endereco);

		if ($row = $address->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			$row['entity_bt_new_saleorder'] = "";

			if ($page == "sale_order") {

				$row['entity_bt_new_saleorder'] = "hidden";
				$row['extra_block_button_sale_address'] = $tplEntity->getContent($row, "EXTRA_BLOCK_BUTTON_SALE_ADDRESS");
			}

			Send($tplEntity->getContent($row, 'EXTRA_BLOCK_ADDRESS'));

		} else {

			Notifier::Add("Erro ao criar endereço.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_address_delete":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$address = new EntityAddress;

		if ($address->Delete($_POST['id_address'])) {

			Send([]);

		} else {

			Notifier::Add("Não foi possível remover o endereço do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_nome_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$tplEntity = new View('templates/entity');

		$entity = new Entity();
		$entity->SearchByCode($_POST['id_entidade']);

		if ($row = $entity->getResult()) {

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_NOME_FORM"));

		} else {

			Notifier::Add("Não foi possível localizar o cadastro do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_nome_cancel":

		$tplEntity = new View('templates/entity');

		$entity = new Entity();
		$entity->SearchByCode($_POST['id_entidade']);

		if ($row = $entity->getResult()) {

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_NOME"));

		} else {

			Notifier::Add("Não foi possível localizar o cadastro do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_nome_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'nome',
			'value' => $_POST['nome'],
		];

		// 'limite' => (float) ('0' . $_POST['limite']),

		$entity->Update($data);

		$tplEntity = new View('templates/entity');

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			// $row = Entity::FormatFields($row);
			$resp = [
				"data" => $tplEntity->getContent($row, "BLOCK_ENTITY_NOME"),
				"nome" => $row['nome'],
				"nick" => strtok($row['nome'], " ")
			];

			Send($resp);

		} else {

			Notifier::Add("Não foi possível ler os dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	// case "entity_expand":

	// 	$id_entidade = $_POST['id_entidade'];

	// 	$tplEntity = new View('templates/entity');

	// 	$entity = new Entity();

	// 	$entity->Read($id_entidade);

	// 	if($row = $entity->getResult()) {

	// 		$row = Entity::FormatFields($row);

	// 		$entityAddress = new EntityAddress();

	// 		$entityAddress->getList($id_entidade);

	// 		$address = "";

	// 		while ($rowAddress = $entityAddress->getResult()) {

	// 			$rowAddress = EntityAddress::FormatFields($rowAddress);
	// 			$rowAddress['extra_block_button_sale_address']="";
	// 			$rowAddress['entity_bt_new_saleorder'] = "";

	// 			$address.= $tplEntity->getContent($rowAddress, "EXTRA_BLOCK_ADDRESS");
	// 		}

	// 		$row['extra_block_address'] = $address;

	// 		$tplSale = new View("templates/sale_order");

	// 		$row['block_history_order'] = $tplSale->getContent(["id_entidade" => $id_entidade], "BLOCK_HISTORY_ORDER");

	// 		Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_DATA"));

	// 	} else {

	// 		Send(null, "Erro ao carregar dados do cliente!", Notifier::NOTIFIER_ERROR);
	// 	}

	// break;

	case "entity_credit_edit":

		ControlAccess::Check(ControlAccess::CA_CLIENTE_CREDITO);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_CREDIT_EDIT"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_credit_save":

		// ControlAccess::Check(ControlAccess::CA_CLIENTE_CREDITO);
		$auth_id = $_POST['auth_id'];
		$auth_pass = $_POST['auth_pass'];

		ControlAccess::CheckAuth($auth_id, $auth_pass, ControlAccess::CA_CLIENTE_CREDITO);

		$id_entidade = $_POST['id_entidade'];
		$credito = $_POST['credito'];
		$tipo = $_POST['tipo'];
		$obs = $_POST['obs'];

		$entity = new Entity();

		if ($tipo == 'remove') {

			$entity->Read($id_entidade);

			if ($row = $entity->getResult()) {

				if ($row['credito'] < $credito) {

					Notifier::Add("Valor a ser removido não pode ser maior que crédito do cliente!", Notifier::NOTIFIER_INFO);
					Send(null);
				}
			}
		}

		$credito = ($tipo == 'add')? $credito: -$credito;

		$entity->setCredito($id_entidade, $credito, $obs, $auth_id);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_CREDIT"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	// case "entity_credit_edit_cancel":

	// 	$id_entidade = $_POST['id_entidade'];

	// 	$entity = new Entity();

	// 	$entity->Read($id_entidade);

	// 	if ($row = $entity->getResult()) {

	// 		$row = Entity::FormatFields($row);

	// 		$tplEntity = new View('templates/entity');

	// 		Send($tplEntity->getContent($row, "BLOCK_ENTITY_CREDIT"));

	// 	} else {

	// 		Send(null, "Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
	// 	}

	// break;

	case "entity_cpfcnpj_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_CPFCNPJ_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_cpfcnpj_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_CPFCNPJ"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_cpfcnpj_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$cpfcnpj = $_POST['cpfcnpj'];

		$entity = new Entity();

		if ($cpfcnpj == "") {

			$cpfcnpj = null;

		} else {

			$valida_cpf_cnpj = new ValidaCPFCNPJ($cpfcnpj);

			if (!$valida_cpf_cnpj->valida()) {

				Notifier::Add("CPF ou CNPJ inválido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			$entity->SearchByCode($cpfcnpj);

			if ($row = $entity->getResult()) {

				if ($row['id_entidade'] != $_POST['id_entidade']) {

					Notifier::Add("CPF/CNPJ já cadastro. Cliente: " . $row['nome'] . ".", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}
		}

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'cpfcnpj',
			'value' => $cpfcnpj,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_CPFCNPJ"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_limite_edit":

		ControlAccess::Check(ControlAccess::CA_CLIENTE_LIMITE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_LIMITE_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_limite_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_LIMITE"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_limite_save":

		ControlAccess::Check(ControlAccess::CA_CLIENTE_LIMITE);

		$id_entidade = $_POST['id_entidade'];
		$limite = (float) $_POST['limite'];

		$entity = new Entity();

		$entity->setLimite($id_entidade, $limite);

		// $data = [
		// 	'id_entidade' =>  $id_entidade,
		// 	'field' => 'limite',
		// 	'value' => $limite,
		// ];

		// $entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_LIMITE"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_email_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_EMAIL_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_email_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_EMAIL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_email_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$email = $_POST['email'];

		$entity = new Entity();

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'email',
			'value' => $email,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_EMAIL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcelular_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_TELCELULAR_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcelular_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELCELULAR"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcelular_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$telcelular = $_POST['telcelular'];

		$entity = new Entity();

		if (!empty($telcelular)) {

			$entity->SearchByPhone($telcelular);

			if ($row = $entity->getResult()) {

				Notifier::Add("Telefone já cadastrado:<br>[" . $row['id_entidade'] . "] " . $row['nome'], Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'telcelular',
			'value' => $telcelular,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELCELULAR"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telresidencial_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_TELRESIDENCIAL_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telresidencial_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELRESIDENCIAL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telresidencial_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$telresidencial = $_POST['telresidencial'];

		$entity = new Entity();

		if (!empty($telresidencial)) {

			$entity->SearchByPhone($telresidencial);

			if ($row = $entity->getResult()) {

				Notifier::Add("Telefone já cadastrado:<br>[" . $row['id_entidade'] . "] " . $row['nome'], Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'telresidencial',
			'value' => $telresidencial,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELRESIDENCIAL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcomercial_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_TELCOMERCIAL_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcomercial_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELCOMERCIAL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_telcomercial_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$telcomercial = $_POST['telcomercial'];

		$entity = new Entity();

		if (!empty($telcomercial)) {

			$entity->SearchByPhone($telcomercial);

			if ($row = $entity->getResult()) {

				Notifier::Add("Telefone já cadastrado:<br>[" . $row['id_entidade'] . "] " . $row['nome'], Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'telcomercial',
			'value' => $telcomercial,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_TELCOMERCIAL"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_obs_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITY_OBS_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_obs_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_OBS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_entidade = $_POST['id_entidade'];
		$obs = $_POST['obs'];

		$entity = new Entity();

		$data = [
			'id_entidade' =>  $id_entidade,
			'field' => 'obs',
			'value' => $obs,
		];

		$entity->Update($data);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$row = Entity::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITY_OBS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_nickname_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_NICKNAME_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_nickname_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_NICKNAME"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_nickname_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$nickname = $_POST['nickname'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'nickname',
			'value' => $nickname,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_NICKNAME"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cep_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_CEP_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cep_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_CEP"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cep_address_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$entityAddress = new EntityAddress();

		$id_endereco = $_POST['id_endereco'];
		$saleorder = ($_POST["saleorder"] == "true")? true: false;

		$logradouro = $_POST["logradouro"];

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'logradouro',
			'value' => $logradouro,
		];

		$entityAddress->Update($data);

		$bairro = $_POST["bairro"];

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'bairro',
			'value' => $bairro,
		];

		$entityAddress->Update($data);

		$cidade = $_POST["cidade"];

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'cidade',
			'value' => $cidade,
		];

		$entityAddress->Update($data);

		$uf = $_POST["uf"];

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'uf',
			'value' => $uf,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			if ($saleorder == true) {

				$row['extra_block_button_sale_address'] = $tplEntity->getContent($row, "EXTRA_BLOCK_BUTTON_SALE_ADDRESS");
				$row['entity_bt_new_saleorder'] = "hidden";
			}

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ADDRESS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cep_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$cep = $_POST['cep'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'cep',
			'value' => $cep,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_CEP"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_extra_block_cep_address_update":

		$data = [
			"logradouro" => $_POST["logradouro"],
			"bairro" => $_POST["bairro"],
			"cidade" => $_POST["cidade"],
			"uf" => $_POST["uf"],
		];

		$tplEntity = new View('templates/entity');

		Send($tplEntity->getContent($data, "EXTRA_BLOCK_CEP_ADDRESS_UPDATE"));

	break;

	case "entityaddress_logradouro_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_LOGRADOURO_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_logradouro_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_LOGRADOURO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_logradouro_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$logradouro = $_POST['logradouro'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'logradouro',
			'value' => $logradouro,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_LOGRADOURO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_numero_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_NUMERO_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_numero_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_NUMERO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_numero_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$numero = $_POST['numero'];

		if ($numero == "") {

			$numero = null;
		}

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'numero',
			'value' => $numero,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_NUMERO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_complemento_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_COMPLEMENTO_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_complemento_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_COMPLEMENTO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_complemento_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$complemento = $_POST['complemento'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'complemento',
			'value' => $complemento,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_COMPLEMENTO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_bairro_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_BAIRRO_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_bairro_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_BAIRRO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_bairro_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$bairro = $_POST['bairro'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'bairro',
			'value' => $bairro,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_BAIRRO"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cidade_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_CIDADE_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cidade_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_CIDADE"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_cidade_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$cidade = $_POST['cidade'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'cidade',
			'value' => $cidade,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_CIDADE"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_uf_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$uf_array = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');

			$option = "";

			foreach ($uf_array as $uf) {

				if ($row['uf'] == $uf) {

					$option.= "<option selected>$uf</option>";

				} else {

					$option.= "<option>$uf</option>";
				}
			}

			$row["uf"] = $option;

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_UF_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_uf_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_UF"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_uf_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$uf = $_POST['uf'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'uf',
			'value' => $uf,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_UF"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_obs_edit":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ENTITYADDRESS_OBS_FORM"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_obs_cancel":

		$id_endereco = $_POST['id_endereco'];

		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_OBS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entityaddress_obs_save":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR_CLIENTE);

		$id_endereco = $_POST['id_endereco'];
		$obs = $_POST['obs'];

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'obs',
			'value' => $obs,
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			Send($tplEntity->getContent($row, "BLOCK_ENTITYADDRESS_OBS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_datacad_edit":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$tplEntity = new View('templates/entity');

			$row = Entity::FormatFields($row);

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_FORM_DATA"));

		} else {

			Notifier::Add("Erro ao abrir data de cadastro para edição!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_datacad_save":

		$id_entidade = $_POST['id_entidade'];
		$datacad = $_POST['datacad'];

		$entity = new Entity();

		$entity->Update([
			'id_entidade' => $id_entidade,
			'field' => "datacad",
			'value' => $datacad . ' 00:00:00',
		]);

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$tplEntity = new View("templates/entity");

			$row = Entity::FormatFields($row);

			Send($tplEntity->getContent($row, "BLOCK_DATA"));

		} else {

			Notifier::Add("Erro ao salvar data de cadastro!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_datacad_cancel":

		$id_entidade = $_POST['id_entidade'];

		$entity = new Entity();

		$entity->Read($id_entidade);

		if ($row = $entity->getResult()) {

			$tplEntity = new View('templates/entity');

			$row = Entity::FormatFields($row);

			Send($tplEntity->getContent($row, "BLOCK_DATA"));

		} else {

			Notifier::Add("Não foi possível carregar data de cadastro!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "entity_show":

		$id_entidade = $_POST['id_entidade'];

		if ($id_entidade == 0) {

			Notifier::Add("Não há dados de cliente em venda varejo!", Notifier::NOTIFIER_INFO);
			Send(null);
		}

		$entity = new Entity();

		$entity->Read($id_entidade);

		if($row = $entity->getResult()) {

			$tplEntity = new View("templates/entity");

			$row = Entity::FormatFields($row);

			$entityAddress = new EntityAddress();

			$entityAddress->getList($id_entidade);

			$address = "";

			while ($rowAddress = $entityAddress->getResult()) {

				$rowAddress = EntityAddress::FormatFields($rowAddress);
				$rowAddress['extra_block_button_sale_address']="";
				$rowAddress['entity_bt_new_saleorder'] = "";

				$address.= $tplEntity->getContent($rowAddress, "EXTRA_BLOCK_ADDRESS");
			}

			$row['extra_block_address'] = $address;

			$tplSale = new View("templates/sale_order");

			$row['block_history_order'] = $tplSale->getContent(["id_entidade" => $id_entidade, "id_venda" => 0], "BLOCK_HISTORY_ORDER");

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_TR"));

		} else {

			Notifier::Add("Error ao carregar dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_cepsearch_show":

		$id_endereco = $_POST["id_endereco"];

		$tplEntity = new View("templates/entity");

		$uf_array = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');

		$company = new Company();

		$company->Read();

		if ($row = $company->getResult()) {

			$option = "";

			foreach ($uf_array as $uf) {

				if ($row['uf'] == $uf) {

					$option.= "<option selected>$uf</option>";

				} else {

					$option.= "<option>$uf</option>";
				}
			}

			$data = [
				"id_endereco" => $id_endereco,
				"uf" => $option,
				"cidade" => $row["cidade"]
			];

			Send($tplEntity->getContent($data, "EXTRA_BLOCK_CEP_SEARCH"));

		} else {

			Notifier::Add("Erro ao carregar dados da Empresa", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_cepsearch":

		$id_endereco = $_POST["id_endereco"];
		$datas = $_POST["data"];

		$tplEntity = new View("templates/entity");

		$response = "";

		foreach($datas as $data) {

			$data["id_endereco"] = $id_endereco;

			$response .= $tplEntity->getContent($data, "EXTRA_BLOCK_ENTITY_CEPSEARCH_ADDRESS");
		}

		Send($response);

	break;

	case "entity_cepsearch_select":

		$id_endereco = $_POST["id_endereco"];
		$cep = $_POST["cep"];
		$logradouro = $_POST["logradouro"];
		$bairro = $_POST["bairro"];
		$cidade = $_POST["cidade"];
		$uf = $_POST["uf"];
		$saleorder = ($_POST["saleorder"] == "true")? true: false;

		$entityAddress = new EntityAddress();

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'logradouro',
			'value' => $logradouro,
		];

		$entityAddress->Update($data);

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'bairro',
			'value' => $bairro,
		];

		$entityAddress->Update($data);

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'cidade',
			'value' => $cidade,
		];

		$entityAddress->Update($data);

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'uf',
			'value' => $uf,
		];

		$entityAddress->Update($data);

		$data = [
			'id_endereco' =>  $id_endereco,
			'field' => 'cep',
			'value' => str_replace("-", "", $cep)
		];

		$entityAddress->Update($data);

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$row = EntityAddress::FormatFields($row);
			$row['extra_block_button_sale_address']="";

			$tplEntity = new View('templates/entity');

			if ($saleorder == true) {

				$row['extra_block_button_sale_address'] = $tplEntity->getContent($row, "EXTRA_BLOCK_BUTTON_SALE_ADDRESS");
				$row['entity_bt_new_saleorder'] = "hidden";
			}

			Send($tplEntity->getContent($row, "EXTRA_BLOCK_ADDRESS"));

		} else {

			Notifier::Add("Erro ao ler dados do cliente!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}
		Send(null);

	break;

	case "entity_cepsearchfreight_show":

		$tplEntity = new View("templates/entity");

		$uf_array = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');

		$company = new Company();

		$company->Read();

		if ($row = $company->getResult()) {

			$option = "";

			foreach ($uf_array as $uf) {

				if ($row['uf'] == $uf) {

					$option.= "<option selected>$uf</option>";

				} else {

					$option.= "<option>$uf</option>";
				}
			}

			$data = [
				"uf" => $option,
				"cidade" => $row["cidade"]
			];

			Send($tplEntity->getContent($data, "EXTRA_BLOCK_CEP_SEARCHFREIGHT"));

		} else {

			Notifier::Add("Erro ao carregar dados da Empresa", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "entity_cepsearchfreight":

		$datas = $_POST["data"];

		$tplEntity = new View("templates/entity");

		$response = "";

		foreach($datas as $data) {

			$freight = new FreightCEP();

			$cep = str_replace("-", "", $data["cep"]);

			$freight->getCEPValue($cep);

			if ($row = $freight->getResult()) {

				if ($row["valor"] == 0) {

					$data["freight"] = "Grátis";

				} else {

					$data["freight"] = "R$ " . number_format($row["valor"], 2, ",", ".");
				}

			} else {

				$data["freight"] = "Valor de frete não encontrado";
			}

			$response .= $tplEntity->getContent($data, "EXTRA_BLOCK_ENTITY_CEPSEARCHFREIGHT_ADDRESS");
		}

		Send($response);

	break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}