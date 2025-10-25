<?php

use App\Legacy\Notifier;

require "./inc/config.inc.php";
// require "./inc/authorization.php";

// function Unauthorized() {

//     header('HTTP/1.0 401 Unauthorized');
// }

switch($_POST['action']) {

	case "subscription":

		$endpoint = $_POST['endpoint'];
        $keys = $_POST['keys'];

		Send([]);
		// Send($endpoint . " <=> " . $keys);

	break;

	default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}