<?php

use database\ControlAccess;
use App\View\View;
use database\Clean;
use database\Company;
use database\Collaborator;
use database\Session;
use database\Notifier;
use App\Support\Version;

require "./inc/config.inc.php";

if (!isset($_POST['action'])) {

	$tplBackendIndex = new View("templates/backend_index");

	$module = $tplBackendIndex->getContent(["module" => 'backend'], "BLOCK_PAGE");

	$tplIndex = new View("templates/index");

	$company = new Company();

	$company->Read();

	$empresa = "Nome da Empresa";

	if ($row = $company->getResult()) {

		$empresa = $row['empresa'];
	}

	// $tplEntity = new View('templates/entity');

    $content = [
			"version" => Version::get(),
			"date" => date('Y-m-d'),
		// "date_search" => date("Y-m"),
			"title" => 'Retaguarda',
			"module" => $module,
			'manifest' => 'backend_manifest.json',
			"empresa" => $empresa,
		// 'block_entity_autocomplete_search' => $tplEntity->getContent([], "BLOCK_ENTITY_AUTOCOMPLETE_SEARCH")
    ];

    $tplIndex->Show($content, "BLOCK_PAGE");

    exit();
}

// switch($_POST['action']) {

// 	case "load":
// 	case "login":

		//$GLOBALS['authorized_skip'] = true;
		// break;
// }

// require "./inc/authorization.php";

switch($_POST['action']) {

	case "load":

		$id_entidade = $_POST["id_entidade"];

		$tplLogin = new View("templates/backend_login");

		$collaborator = new Collaborator();

		$row_result = $collaborator->getListHavingAccess(ControlAccess::CA_SERVIDOR);

		$collaborators = "";

		foreach($row_result as $row) {

			$row["selecte"] = "";

			if ($row["id_entidade"] == $id_entidade) {

				$row["selected"] = "selected";
			}

            $collaborators.= $tplLogin->getContent($row, "EXTRA_BLOCK_COLLABORATOR");
		}

		$data = [
			'collaborators' => $collaborators
		];

		Send($tplLogin->getContent($data, "BLOCK_PAGE"));

		break;

	case "login":

		$id_entidade = Clean::HtmlChar($_POST['id_entidade']);
		$pass = Clean::HtmlChar($_POST['pass']);

		if (ControlAccess::Login($id_entidade, $pass, ControlAccess::CA_SERVIDOR)) {

			$page = Session::get('page');

			$tplMenu = new View("templates/menu");

            Session::set('page', null);

			$nome = strtok($GLOBALS['authorized_nome'], " ");

			$company = new Company();

			$company->Read();

			$empresa = "Nome da Empresa";

			if ($row = $company->getResult()) {

				$empresa = $row['empresa'];
			}

			$date = new DateTimeImmutable();

			$content = [
				"version" => Version::get(),
				"id_entidade" => $GLOBALS['authorized_id_entidade'],
				"nome" => $nome,
				"empresa" => $empresa,
				"timestamp" => $date->getTimestamp()
			];

			Send([
				"data" => $tplMenu->getContent($content, View::ALL),
				"logged" => true,
				"page" => $page
			]);

		} else {

			Notifier::Add("Erro ao fazer login!", Notifier::NOTIFIER_ERROR);

			Send(null);
		}

		break;

	case "auth":

		ControlAccess::Check(ControlAccess::CA_SERVIDOR);

		$tplMenu = new View("templates/menu");

		$nome = strtok($GLOBALS['authorized_nome'], " ");

		$company = new Company();

		$company->Read();

		$empresa = "Nome da Empresa";

		if ($row = $company->getResult()) {

			$empresa = $row['empresa'];
		}

		$date = new DateTimeImmutable();

		$content = [
			"version" => Version::get(),
			"id_entidade" => $GLOBALS['authorized_id_entidade'],
			"nome" => $nome,
			"empresa" => $empresa,
			"timestamp" => $date->getTimestamp()
		];

		Send($tplMenu->getContent($content, View::ALL));

		break;

	case "popup_authenticator":

		$tplIndex = new View("templates/index");

		$collaborator = new Collaborator();

		$rows = $collaborator->getListHavingAccess(ControlAccess::CA_SERVIDOR);

		$collaborators = "";

		foreach($rows as $row) {

			$row["selected"] = "";

			if ($row["id_entidade"] == $GLOBALS['authorized_id_entidade']) {

				$row["selected"] = "selected";
			}

			$collaborators.= $tplIndex->getContent($row, "EXTRA_BLOCK_COLLABORATOR");
		}

		$data = [
			'collaborators' => $collaborators
		];

		Send($tplIndex->getContent($data, "EXTRA_BLOCK_AUTHENTICATOR"));

		break;

	default:

		ControlAccess::Unauthorized();// login is not set
		break;
}