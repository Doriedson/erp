<?php

use database\Notifier;
use App\View\View;
use database\Sound;

require "inc/config.inc.php";
require "inc/authorization.php";

switch ($_POST['action']) {

	case "load":

		$tplSound = new View('templates/sound');

		$sound = new Sound();

		$sound->getList();

		if ($row = $sound->getResult()) {

			// $row = Sound::FormatFields($row);

			Send($tplSound->getContent($row, "BLOCK_PAGE"));

		} else {

			Notifier::Add("Erro ao carregar informações de som!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "sound_descricao_edit":

		$id_som = $_POST["id_som"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			Send($tplSound->getContent($row, "EXTRA_BLOCK_SOUND_DESCRICAO_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sound_descricao_cancel":

		$id_som = $_POST["id_som"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			Send($tplSound->getContent($row, "BLOCK_SOUND_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sound_descricao_save":

		$id_som = $_POST["id_som"];
		$descricao = $_POST["descricao"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		if ($sound->Update($id_som, "descricao", $descricao) == 0) {

			Notifier::Add("Erro ao salvar descrição do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			Send($tplSound->getContent($row, "BLOCK_SOUND_DESCRICAO"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sound_play":

		$id_som = $_POST["id_som"];

		$sound = new Sound();

		$sound->Play($id_som);

		Send([]);

		break;

	case "sound_volume_edit":

		$id_som = $_POST["id_som"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			$options = "";

			for ($index_volume = 0; $index_volume <= 100; $index_volume++) {

				$selected = ($row["volume"] == $index_volume)?"selected":"";

				$options .= "<option value='$index_volume' $selected>$index_volume%</option>";
			}

			$row["soundvolume_option"] = $options;

			Send($tplSound->getContent($row, "EXTRA_BLOCK_SOUND_VOLUME_FORM"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sound_volume_cancel":

		$id_som = $_POST["id_som"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			Send($tplSound->getContent($row, "BLOCK_SOUND_VOLUME"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "sound_volume_save":

		$id_som = $_POST["id_som"];
		$volume = $_POST["volume"];

		$tplSound = new View("templates/sound");

		$sound = new Sound();

		if ($sound->Update($id_som, "volume", $volume) == 0) {

			Notifier::Add("Erro ao salvar volume do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$sound->Read($id_som);

		if ($row = $sound->getResult()) {

			Send($tplSound->getContent($row, "BLOCK_SOUND_VOLUME"));

		} else {

			Notifier::Add("Erro ao carregar dados do som.", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;
}