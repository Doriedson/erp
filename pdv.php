<?php


use App\Legacy\Notifier;
use App\View\View;
use App\Legacy\Clean;
use App\Legacy\Collaborator;
use App\Legacy\ControlAccess;
use App\Support\Version;

require "./inc/config.inc.php";

if (!isset($_COOKIE['id_pdv'])) {

    // setcookie("id_pdv", password_hash(rand(), PASSWORD_BCRYPT), time() + 3600 * 24 * 7);
} else {

    $id_pdv = $_COOKIE['id_pdv'];
}

if (!isset($_POST['action'])) {

    $tplPDVIndex = new View("pdv_index");

    // $module = $tplPDVIndex->getContent(["module" => 'pdv'], "BLOCK_PAGE");

    $tplIndex = new View("index");

    $content = [
        "version" => Version::get(),
        "title" => 'PDV',
        'manifest' => 'pdv_manifest.json'
    ];

    $tplIndex->Show($content, "BLOCK_PAGE");

    exit();
}

if ($_POST['action'] == "load" || $_POST['action'] == "login") {

	$GLOBALS['authorized_skip'] = true;
}

require "./inc/authorization.php";

// switch($_POST['action']) {

// 	case "load":

//         ControlAccess::Check(ControlAccess::CA_PDV);

//         $tplPdv = new View("pdv_closed");

//         Send($tplPdv->getContent([], VIEW::ALL));

// 		break;

// 	default:

//         Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        // Send(null);
//     break;
// }

switch($_POST['action']) {

	case "load":

		$id_entidade = $_POST["id_entidade"];

		$tplLogin = new View("pdv_login");

		$collaborator = new Collaborator();

		$row_result = $collaborator->getListHavingAccess(ControlAccess::CA_PDV);

		$entitys = "";

		foreach($row_result as $row) {

			$row["selecte"] = "";

			if ($row["id_entidade"] == $id_entidade) {

				$row["selected"] = "selected";
			}

            $entitys.= $tplLogin->getContent($row, "EXTRA_BLOCK_ENTITY");
		}

		if (count($row_result) == 0) {

			$entitys = $tplLogin->getContent([], "EXTRA_BLOCK_ENTITY_NONE");
		}

		$data = [
			'entitys' => $entitys
		];

		Send($tplLogin->getContent($data, "BLOCK_PAGE"));

		break;

	case "login":

		$user = Clean::HtmlChar($_POST['id_entidade']);
		$pass = Clean::HtmlChar($_POST['pass']);

		if ( trim($user)=='' || trim($pass=='') ) {

			ControlAccess::Unauthorized();

		} else if ($user == 0) {

			Notifier::Add("Nenhum(a) operador(a) cadastrado(a) para acesso!", Notifier::NOTIFIER_ERROR);
			Send(null);

		} else {

            // if (ControlAccess::Login($user, $pass, ControlAccess::CA_PDV)) {

                $tplMenu = new View("pdv_menu");

                Send([
                    "data" => "",
                    "menu" => $tplMenu->getContent([], View::ALL),
                    "logged" => true
                ]);

			// } else {

			// 	ControlAccess::Unauthorized(); //user not found
			// }
		}

		break;

	case "auth":

		ControlAccess::Check(ControlAccess::CA_PDV);

		$tplMenu = new View("pdv_menu");
		$tplPDV = new View("pdv_index");

		Send([
			"data" => $tplPDV->getContent([], "EXTRA_BLOCK_SALE_CONTAINER"),
			"menu" => $tplMenu->getContent([], View::ALL)
		]);

		break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}