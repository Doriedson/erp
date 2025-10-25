<?php

use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Company;
use App\Legacy\ValidaCPFCNPJ;

require "inc/config.inc.php";
require "inc/authorization.php";

function CompanyFormEdit($block, $message_error) {

	$tplCompany = new View('company');

	$company = new Company();
	$company->Read();

	if ($row = $company->getResult()) {

		$row = Company::FormatFields($row);

		if ($block == "EXTRA_BLOCK_COMPANY_UF_FORM") {

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
		}

		Send($tplCompany->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}

}

function CompanyFormCancel($block, $message_error) {

	$tplCompany = new View('company');

	$company = new Company();
	$company->Read();

	if ($row = $company->getResult()) {

		$row = Company::FormatFields($row);

		Send($tplCompany->getContent($row, $block));

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

function CompanyFormSave($field, $block, $message_error) {

	$value = $_POST['value'];

    if ($field == "cnpj") {

		if (!empty($value)) {
			$valida_cpf_cnpj = new ValidaCPFCNPJ($value);

			if (!$valida_cpf_cnpj->valida()) {

				Notifier::Add("CNPJ inválido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}
		}
    }

	$data = [
		'field' => $field,
		'value' => $value,
	];

	$company = new Company();

	$company->Update($data);

	$tplCompany = new View('company');

	$company->Read();

	if ($row = $company->getResult()) {

		$row = Company::FormatFields($row);

		if ($field == 'empresa') {

			$resp = [
				"data" => $tplCompany->getContent($row, $block),
				"empresa" => $row['empresa']
			];

			Send($resp);

		} else {

			Send($tplCompany->getContent($row, $block));
		}

	} else {

		Notifier::Add($message_error, Notifier::NOTIFIER_ERROR);
		Send(null);
	}
}

switch ($_POST['action']) {

	case "load":

		$company = new Company();
		$company->Read();

		$tplCompany = new View('company');

		$sector = "";

		if ($row = $company->getResult()) {

			$row = Company::FormatFields($row);

    		Send($tplCompany->getContent($row, "BLOCK_PAGE"));

        } else {

            Notifier::Add("Erro ao carregar dados da empresa!", Notifier::NOTIFIER_ERROR);
			Send(null);
        }

	    break;

	case "company_empresa_edit":

		CompanyFormEdit('EXTRA_BLOCK_COMPANY_EMPRESA_FORM', 'Erro ao carregar empresa!');
	break;

	case "company_empresa_cancel":

		CompanyFormCancel('BLOCK_COMPANY_EMPRESA', 'Erro ao carregar empresa!');
	break;

	case "company_empresa_save":

		CompanyFormSave('empresa', 'BLOCK_COMPANY_EMPRESA', 'Erro ao carregar empresa!');
	break;

    case "company_cnpj_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CNPJ_FORM", "Erro ao carregar CNPJ!");
	break;

	case "company_cnpj_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CNPJ", "Erro ao carregar CNPJ!");
	break;

	case "company_cnpj_save":

		CompanyFormSave('cnpj', "BLOCK_COMPANY_CNPJ", "Erro ao carregar CNPJ!");
	break;

    case "company_ie_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_IE_FORM", "Erro ao carregar IE!");
	break;

	case "company_ie_cancel":

		CompanyFormCancel("BLOCK_COMPANY_IE", "Erro ao carregar IE!");
	break;

	case "company_ie_save":

		CompanyFormSave('ie', "BLOCK_COMPANY_IE", "Erro ao carregar IE!");
	break;

	case "company_telefone_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_TELEFONE_FORM", "Erro ao carregar telefone!");
	break;

	case "company_telefone_cancel":

		CompanyFormCancel("BLOCK_COMPANY_TELEFONE", "Erro ao carregar telefone!");
	break;

	case "company_telefone_save":

		CompanyFormSave('telefone', "BLOCK_COMPANY_TELEFONE", "Erro ao carregar telefone!");
	break;

	case "company_celular_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CELULAR_FORM", "Erro ao carregar celular!");
	break;

	case "company_celular_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CELULAR", "Erro ao carregar celular!");
	break;

	case "company_celular_save":

		CompanyFormSave('celular', "BLOCK_COMPANY_CELULAR", "Erro ao carregar celular!");
	break;

	case "company_cep_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CEP_FORM", "Erro ao carregar CEP!");
	break;

	case "company_cep_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CEP", "Erro ao carregar CEP!");
	break;

	case "company_cep_save":

		CompanyFormSave('cep', "BLOCK_COMPANY_CEP", "Erro ao carregar CEP!");
	break;

	case "company_rua_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_RUA_FORM", "Erro ao carregar rua!");
	break;

	case "company_rua_cancel":

		CompanyFormCancel("BLOCK_COMPANY_RUA", "Erro ao carregar rua!");
	break;

	case "company_rua_save":

		CompanyFormSave('rua', "BLOCK_COMPANY_RUA", "Erro ao carregar rua!");
	break;

	case "company_bairro_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_BAIRRO_FORM", "Erro ao carregar bairro!");
	break;

	case "company_bairro_cancel":

		CompanyFormCancel("BLOCK_COMPANY_BAIRRO", "Erro ao carregar bairro!");
	break;

	case "company_bairro_save":

		CompanyFormSave('bairro', "BLOCK_COMPANY_BAIRRO", "Erro ao carregar bairro!");
	break;

	case "company_cidade_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CIDADE_FORM", "Erro ao carregar cidade!");
	break;

	case "company_cidade_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CIDADE", "Erro ao carregar cidade!");
	break;

	case "company_cidade_save":

		CompanyFormSave('cidade', "BLOCK_COMPANY_CIDADE", "Erro ao carregar cidade!");
	break;

	case "company_uf_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_UF_FORM", "Erro ao carregar uf!");
	break;

	case "company_uf_cancel":

		CompanyFormCancel("BLOCK_COMPANY_UF", "Erro ao carregar uf!");
	break;

	case "company_uf_save":

		CompanyFormSave('uf', "BLOCK_COMPANY_UF", "Erro ao carregar uf!");
	break;

	case "company_cupomlinha1_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CUPOMLINHA1_FORM", "Erro ao carregar linha 1 do rodapé do cupom!");
	break;

	case "company_cupomlinha1_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CUPOMLINHA1", "Erro ao carregar linha 1 do rodapé do cupom!");
	break;

	case "company_cupomlinha1_save":

		CompanyFormSave('cupomlinha1', "BLOCK_COMPANY_CUPOMLINHA1", "Erro ao carregar linha 1 do rodapé do cupom!");
	break;

	case "company_cupomlinha2_edit":

		CompanyFormEdit("EXTRA_BLOCK_COMPANY_CUPOMLINHA2_FORM", "Erro ao carregar linha 2 do rodapé do cupom!");
	break;

	case "company_cupomlinha2_cancel":

		CompanyFormCancel("BLOCK_COMPANY_CUPOMLINHA2", "Erro ao carregar linha 2 do rodapé do cupom!");
	break;

	case "company_cupomlinha2_save":

		CompanyFormSave('cupomlinha2', "BLOCK_COMPANY_CUPOMLINHA2", "Erro ao carregar linha 2 do rodapé do cupom!");
	break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}