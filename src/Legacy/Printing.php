<?php

namespace App\Legacy;

use App\Legacy\OS;
use Escpos\PrintConnectors\DummyPrintConnector;
use Escpos\PrintConnectors\NetworkPrintConnector;
use Escpos\Printer;
use Escpos\PrintConnectors\WindowsPrintConnector;
use Escpos\PrintConnectors\CupsPrintConnector;

use Exception;

class Printing extends Connection {

	private $impressora;
	private $printer;
	private $cutter;
	private $linefeed;
	private $columns;
	private $bigfont;
	private $connector;
	private $copies;

	public function __construct($id_impressora) {

		$this->connector = null;

		if ($id_impressora == null) {

			Notifier::Add("Impressora não definida!", Notifier::NOTIFIER_ERROR);
			return;
		}

		if ($id_impressora == -1) {

			$this->impressora = "Not Printing";
			$this->cutter = false;
			$this->linefeed = 0;
			$this->columns = 44;
			$this->bigfont = false;
			$this->copies = 0;

			$this->connector = new DummyPrintConnector();

		} else {

			$printer = new PrinterConfig();

			$printer->Read($id_impressora);

			if ($row = $printer->getResult()) {

				if ($row['id_impressora'] != null) {

					$this->impressora = $row['impressora'];
					$this->cutter = ($row['guilhotina'] == 0)? false: true;
					$this->linefeed = $row['linefeed'];
					$this->columns = $row['colunas'];
					$this->bigfont = ($row['bigfont'] == 0)? false: true;
					$this->copies = $row['copies'];

					if (preg_match(WindowsPrintConnector::REGEX_SMB, $this->impressora) == 1) {
						// Connect to samba share, eg smb://host/printer
					// if (OS::isWindows()) {

						// $this->impressora = "smb://hortifruti:8511965@192.168.1.69/generic";
						$this->connector = new WindowsPrintConnector($this->impressora);

					} else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?::(\d{1,5}))?\z/', $this->impressora) == 1) {

						// preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $this->impressora, $IP);

						$IP = explode(":", $this->impressora);

						if (filter_var($IP[0], FILTER_VALIDATE_IP)) {

							if (count($IP) > 1) {

								$this->connector = new NetworkPrintConnector($IP[0], $IP[1]);

							} else {

								$this->connector = new NetworkPrintConnector($IP[0]);
							}

						} else {

							Notifier::Add("IP inválido [" . $IP[0] . "] para impressora!", Notifier::NOTIFIER_ERROR);
						}

					} else {

						$this->connector = new CupsPrintConnector($this->impressora);
					}

				} else {

					Notifier::Add("Impressora não definida!", Notifier::NOTIFIER_ERROR);
					return;
				}

			}
		}

		if ($this->connector) {

			$this->printer = new Printer($this->connector);

		} else {

			Notifier::Add("Impressora não inicializada!", Notifier::NOTIFIER_ERROR);
		}
	}

	public function initialize() {

		$ret = false;

		if ($this->connector) {

			$this->printer->initialize();
			$ret = true;
		}

		return $ret;
	}

	public function finalize() {

		$this->printer->close($this->copies);
	}

	public function close() {

		if ($this->linefeed > 0) {

			$this->printer->feed($this->linefeed);
		}

		if ($this->cutter) {

			$this->printer->cut();
		}

		try {

			$this->printer->close($this->copies);

		} catch (Exception $e) {

			Notifier::Add("Não foi possível imprimir! <br>" . $e->getMessage(), Notifier::NOTIFIER_ERROR);
		}
	}

	public function getData() {

		$data = $this->printer->getPrintBuffer();

		$this->printer->close($this->copies);

		return $data;
	}

	public function Command($cmd) {

		$this->connector->write($cmd);
	}

	public function line($lines) {

		$this->printer->feed($lines);
	}

	public function textCenter($text) {

		$text_len = mb_strlen(($text));

		if ($text_len >= $this->columns) {

			$this->text($text);

		} else {

			$this->text(str_repeat(" ", intval(($this->columns - $text_len) / 2)) . $text);
		}
	}

	public function textRight($text) {

		$text_len = mb_strlen(($text));

		if ($text_len >= $this->columns) {

			$this->text($text);

		} else {

			$this->text(str_repeat(" ", $this->columns - mb_strlen($text)) . $text);
		}
	}

	public function textRepeat($text) {

		$this->text(str_repeat($text, $this->columns));
	}

	public function textSpaceBetween($text1, $text2) {

		$text1_len = mb_strlen(($text1));
		$text2_len = mb_strlen(($text2));

		//Prints one line if text is empty
		if ($text1_len + $text2_len > $this->columns - 1) {

			$this->text($text1 . " " . $text2);

		} else {

			$this->text($text1 . str_repeat(" ", $this->columns - ($text1_len + $text2_len)) . $text2);
		}
	}

	public function textTruncate($text) {

		$this->text(mb_substr($text, 0, $this->columns));
	}

	public function text($text) {

		$textlen = mb_strlen(($text));

		//Prints one line if text is empty
		if ($textlen == 0) {

			$this->line(1);

		} elseif ($textlen <= $this->columns) {

			$this->print($text . "\n");

		} else {

			$arr = explode(" ", $text);

			$text_tmp = "";

			for ($index = 0; $index < count($arr); $index++) {

				if (mb_strlen($text_tmp . " " . $arr[$index]) > $this->columns) {

					if ($text_tmp == "") {

						$text_tmp2 = $arr[$index];

						while (mb_strlen($text_tmp2) > 0) {

							if (mb_strlen($text_tmp2) > $this->columns) {

								$this->print(mb_substr($text_tmp2, 0, $this->columns) . "\n");

								$text_tmp2 = mb_substr($text_tmp2, $this->columns, mb_strlen($text_tmp2) - $this->columns);

							} else {

								$text_tmp = $text_tmp2;

								$text_tmp2 = "";
							}
						}

					} else {

						$this->print($text_tmp . "\n");

						$text_tmp = "";

						$index--;
					}

				} else {

					if ($text_tmp == "") {

						$text_tmp .= $arr[$index];

					} else {

						$text_tmp .= " " . $arr[$index];
					}
				}
			}

			$this->print($text_tmp . "\n");
		}

		// 	for ($index = 0; $index <= $textlen - 1; $index += $this->columns) {

		// 		$this->print(mb_substr($text, $index, $this->columns)."\n");
		// 	}
		// }
	}

	private function print($text) {

		if ($this->bigfont) {

			$this->Command(chr(27) . "!" . chr(16)); //Double height
		}

		$this->printer->text($text);
	}

	public function linedash() {

		$this->text(str_repeat("-", $this->columns));
	}

	public function linedashspaced() {

		$this->text(str_repeat("- ", intval($this->columns / 2)));
	}
}