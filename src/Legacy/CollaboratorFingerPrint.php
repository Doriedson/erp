<?php

namespace App\Legacy;

class CollaboratorFingerPrint extends Connection {

  private function Create($id_entidade, $fingerprint) {

		$this->data = [
      'id_entidade' => $id_entidade,
			'fingerprint' => json_encode($fingerprint),
		];

		$this->query = "INSERT INTO tab_colaboradorfingerprint
                    (id_entidade, fingerprint) VALUES (:id_entidade, :fingerprint)";

		parent::Execute();
	}

  private function Read($id_entidade) {

    $this->data = [
			"id_entidade" => $id_entidade
		];

		$this->query = "SELECT * FROM tab_colaboradorfingerprint
			              WHERE id_entidade = :id_entidade";

		parent::Execute();
  }

  private function Update($id_entidade, $fingerprint) {

		$this->data = [
      'id_entidade' => $id_entidade,
			'fingerprint' => json_encode($fingerprint),
		];

		$this->query = "UPDATE tab_colaboradorfingerprint
                    SET fingerprint = :fingerprint
                    WHERE id_entidade = :id_entidade";

		parent::Execute();
	}

	public function FingerPrint($id_entidade, $page) {

		$this->Read($id_entidade);

    if ($row = $this->getResult()) {

      $fingerprint = json_decode($row["fingerprint"], true);

      if (key_exists($page, $fingerprint)) {

        $fingerprint[$page]++;

      } else {

        $fingerprint[$page] = 1;
      }

      $this->Update($id_entidade, $fingerprint);

    } else {

      $fingerprint = [
        "$page" => 1
      ];

      $this->Create($id_entidade, $fingerprint);
    }

	}
}