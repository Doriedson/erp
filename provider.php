<?php

use database\ControlAccess;
use database\Notifier;
use database\View;
use database\Clean;
use database\Provider;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_FORNECEDOR);

function ProviderFormEdit($block, $message_error) {

	$id_fornecedor = $_POST['id_fornecedor'];

	$tplProvider = new View('templates/provider');

	$provider = new Provider();
	$provider->Read($id_fornecedor);

	if ($row = $provider->getResult()) {

		$row = Provider::FormatFields($row);

		Send($tplProvider->getContent($row, $block));
	
	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function ProviderFormCancel($block, $message_error) {

	$id_fornecedor = $_POST['id_fornecedor'];

	$tplProvider = new View('templates/provider');

	$provider = new Provider();
	$provider->Read($id_fornecedor);

	if ($row = $provider->getResult()) {

		$row = Provider::FormatFields($row);

		Send($tplProvider->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function ProviderFormSave($field, $block, $message_error) {

	$id_fornecedor = $_POST['id_fornecedor'];
	$value = $_POST['value'];

	$data = [
		'id_fornecedor' => (int) $id_fornecedor,
		'field' => $field,
		'value' => $value,
	];

	$provider = new Provider();

	$provider->Update($data);

	$tplProvider = new View('templates/provider');

	$provider->Read($id_fornecedor);

	if ($row = $provider->getResult()) {

		$row = Provider::FormatFields($row);

		Send($tplProvider->getContent($row, $block));
	
	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$tplEntity = new View('templates/provider');
		
		Send($tplEntity->getContent([], "BLOCK_PAGE"));

		break;

    case "provider_new":

        $provider = new Provider();

        $id_provider = $provider->Create();

        $provider->Read($id_provider);

        if ($row = $provider->getResult()) {

            $tplProvider = new View('templates/provider');

            $row = Provider::FormatFields($row);

            Send($tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER"));

        } else {

            Notifier::Add("Ocorreu um erro ao cadastrar novo fornecedor!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }
        
        break;        

	// case "provider_search":

	// 	$value = $_POST['value'];
		
	// 	$provider = new Provider();

	// 	if( is_numeric($value) ) {

	// 		$provider->Read($value);

	// 	} else {

	// 		$provider->Search($value);
	// 	}
		
	// 	$tplProvider = new View('templates/provider');

	// 	if ($row = $provider->getResult()) {

	// 		$result = "";

	// 		do {

	// 			$row = Provider::FormatFields($row);
	// 			$result.= $tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER");

	// 		} while ($row = $provider->getResult());

	// 		Send($result);

	// 	} else {

	// 		Send([], "Nenhum fornecedor encontrado!", Notifier::NOTIFIER_INFO);
	// 	}

	// break;	

	case "provider_smart_search":

		$value = Clean::HtmlChar(trim($_POST['value']));
		$value = Clean::DuplicateSpace($value);
		$source = $_POST['source'];

		$provider = new Provider();

		$tplProvider = new View("templates/provider");

        if( is_numeric($value) ) {

            $provider->Read($value);

        } else {

			if (empty($value)) {

				Send([]);
			}

            $provider->Search($value);
        }

		$provider_list = "";

		if ($row = $provider->getResult()) {

			do {

				$row = Provider::FormatFields($row);

				switch ($source) {

					case "popup":

						$provider_list.= $tplProvider->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH");

					break;
					

					case "provider":

						$provider_list.= $tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER");
					break;
				}

			} while ($row = $provider->getResult());

		} else {

			switch ($source) {

				case "popup":

					$provider_list = ""; //$tplProduct->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND");
				break;

				case "product":

					$provider_list = null; // $tplProduct->getContent($row, "EXTRA_BLOCK_PRODUCT_SECTOR_NOTFOUND");
				break;
			}
			// $provider_list = $tplProvider->getContent($row, "EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND");
		}

		Send($provider_list);

	break;	
				
    case "provider_list":

        $provider = new Provider();

        $provider->getList();

        $result = "";

        if ($row = $provider->getResult()) {

            $tplProvider = new View('templates/provider');

            do {

                $row = Provider::FormatFields($row);

                $result.= $tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER");

            } while ($row = $provider->getResult());

            Send($result);

        } else {

            // Send($tplProvider->getContent([], "EXTRA_BLOCK_PROVIDER_NOTFOUND"), "Nenhum fornecedor encontrado!", Notifier::NOTIFIER_INFO);
            Notifier::Add("Nenhum fornecedor encontrado!", Notifier::NOTIFIER_INFO);
			Send([]);
        }

        break;        

	case "provider_change_status":

		$id_fornecedor = $_POST['id_fornecedor'];

		$provider = new Provider();

		$provider->ToggleActive($id_fornecedor);

		$provider->Read($id_fornecedor);

		if ($row = $provider->getResult()) {

			$row = Provider::FormatFields($row);

			Send($row['extra_block_provider_button_status']);

		} else {

			Notifier::Add("Ocorreu um erro na ativação/desativação do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

    case "provider_expand":

		$id_fornecedor = $_POST['id_fornecedor'];

        $tplProvider = new View('templates/provider');

        $provider = new Provider();

        $provider->Read($id_fornecedor);

        if($row = $provider->getResult()) {

            $row = Provider::FormatFields($row);

            Send($tplProvider->getContent($row, "EXTRA_BLOCK_PROVIDER_DATA"));

        } else {

            Notifier::Add("Erro ao ler dados do fornecedor!", Notifier::NOTIFIER_ERROR);
			Send([]);
        }
		
        break;

    case "provider_razaosocial_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_RAZAOSOCIAL_FORM", "Não foi possível localizar o cadastro do fornecedor!");

	break; 
        
    case "provider_razaosocial_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_RAZAOSOCIAL", "Não foi possível localizar o cadastro do fornecedor!");

	break;        
	
	case "provider_razaosocial_save":

		ProviderFormSave('razaosocial', "BLOCK_PROVIDER_RAZAOSOCIAL", "Não foi possível ler os dados do fornecedor!");
	break;
	
	case "provider_nomefantasia_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_NOMEFANTASIA_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_nomefantasia_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_NOMEFANTASIA", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_nomefantasia_save":

		ProviderFormSave('nomefantasia', "BLOCK_PROVIDER_NOMEFANTASIA", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_cpfcnpj_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CPFCNPJ_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_cpfcnpj_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CPFCNPJ", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_cpfcnpj_save":

		ProviderFormSave('cpfcnpj', "BLOCK_PROVIDER_CPFCNPJ", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_ie_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_IE_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_ie_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_IE", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_ie_save":

		ProviderFormSave('ie', "BLOCK_PROVIDER_IE", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_email_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_EMAIL_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_email_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_EMAIL", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_email_save":

		ProviderFormSave('email', "BLOCK_PROVIDER_EMAIL", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_obs_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_OBS_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_obs_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_OBS", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_obs_save":

		ProviderFormSave('obs', "BLOCK_PROVIDER_OBS", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_cep_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CEP_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_cep_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CEP", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_cep_save":

		ProviderFormSave('cep', "BLOCK_PROVIDER_CEP", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_endereco_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_ENDERECO_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_endereco_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_ENDERECO", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_endereco_save":

		ProviderFormSave('endereco', "BLOCK_PROVIDER_ENDERECO", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_bairro_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_BAIRRO_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_bairro_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_BAIRRO", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_bairro_save":

		ProviderFormSave('bairro', "BLOCK_PROVIDER_BAIRRO", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_cidade_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CIDADE_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_cidade_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CIDADE", "Não foi possível localizar o cadastro do fornecedor!");
	break;        

	case "provider_cidade_save":

		ProviderFormSave('cidade', "BLOCK_PROVIDER_CIDADE", "Não foi possível ler os dados do fornecedor!");
	break;

	case "provider_uf_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_UF_FORM", "Não foi possível localizar o cadastro do fornecedor!");
	break;

	case "provider_uf_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_UF", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_uf_save":

		ProviderFormSave('uf', "BLOCK_PROVIDER_UF", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_telefone1_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_TELEFONE1_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_telefone1_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_TELEFONE1", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_telefone1_save":

		ProviderFormSave('telefone1', "BLOCK_PROVIDER_TELEFONE1", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_contato1_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CONTATO1_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_contato1_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CONTATO1", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_contato1_save":

		ProviderFormSave('contato1', "BLOCK_PROVIDER_CONTATO1", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_telefone2_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_TELEFONE2_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_telefone2_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_TELEFONE2", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_telefone2_save":

		ProviderFormSave('telefone2', "BLOCK_PROVIDER_TELEFONE2", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_contato2_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CONTATO2_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_contato2_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CONTATO2", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_contato2_save":

		ProviderFormSave('contato2', "BLOCK_PROVIDER_CONTATO2", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_telefone3_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_TELEFONE3_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_telefone3_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_TELEFONE3", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_telefone3_save":

		ProviderFormSave('telefone3', "BLOCK_PROVIDER_TELEFONE3", "Não foi possível ler os dados do fornecedor!");
		break;

	case "provider_contato3_edit":

		ProviderFormEdit("EXTRA_BLOCK_PROVIDER_CONTATO3_FORM", "Não foi possível localizar o cadastro do fornecedor!");
		break;

	case "provider_contato3_cancel":

		ProviderFormCancel("BLOCK_PROVIDER_CONTATO3", "Não foi possível localizar o cadastro do fornecedor!");
		break;        

	case "provider_contato3_save":

		ProviderFormSave('contato3', "BLOCK_PROVIDER_CONTATO3", "Não foi possível ler os dados do fornecedor!");
		break;
		
	default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
		break;
}