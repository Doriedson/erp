<?php

namespace database;

use ZipArchive;

class Backup extends Connection {

	public function Read() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_backup";

		parent::Execute();
	}

	public function Do() {

		$ret = false;

		$this->Read();

		if ($row = $this->getResult()) {

			$path_db_backupfile = __DIR__ . "/../assets/";
			$db_file = $row["db_filename"];
			$ftp_server = $row["ftp_server"];
			$ftp_user_name = $row["ftp_username"];
			$ftp_user_pass = $row["ftp_pass"];

			Connection::Backup($path_db_backupfile . $db_file);

			$zip = new ZipArchive();
			$zipfilename = $db_file .".zip";

			if ($zip->open($path_db_backupfile . $zipfilename, ZipArchive::CREATE)!==TRUE) {
    			exit("cannot open <$zipfilename>\n");
			}

			// $zip->addFromString("testfilephp.txt" . time(), "#1 This is a test string added as testfilephp.txt.\n");
			// $zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
			$zip->addFile($path_db_backupfile . $db_file);

			echo "numfiles: " . $zip->numFiles . "\n";
			echo "status:" . $zip->status . "\n";

			$zip->close();

			$remote_file = $zipfilename;

			// set up basic connection
			$ftp = ftp_connect($ftp_server);

			// login with username and password
			// $login_result =

			if (ftp_login($ftp, $ftp_user_name, $ftp_user_pass)) {

				// liga o modo passivo (necessário se estiver atrás de firewall se não dá erro de timeout)
				ftp_pasv($ftp, true);

				// upload a file
				if (ftp_put($ftp, $remote_file, $path_db_backupfile . $zipfilename)) {

					$ret = true;
					echo "successfully uploaded $zipfilename\n";

				} else {

					echo "There was a problem while uploading $zipfilename\n";
				}
			}

			// close the connection
			ftp_close($ftp);
		}

		return $ret;
	}
}