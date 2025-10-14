<?php

namespace database;

class Receipt extends Connection {

	public function getList() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_recibo 
						INNER JOIN tab_entidade
						ON tab_entidade.id_entidade = tab_recibo.id_entidade";

		parent::Execute();
	}

	public function Create($data) {

		$this->data = [
			"data" => $data['data'],
			"id_entidade" => $data['id_entidade'],
			"valor" => $data['valor'],
			"motivo" => $data['motivo']
		];

		$fields = implode(', ', array_keys($this->data));
		$places = ':' . implode(", :", array_keys($this->data));

		$this->query = "INSERT INTO tab_recibo 
						($fields) VALUES ($places)";

		parent::Execute();

		return parent::lastInsertId();
	}

	public function Read($id_recibo) {

		$this->data = [
			'id_recibo' => $id_recibo
		];

		$this->query = "SELECT * FROM tab_recibo 
						INNER JOIN tab_entidade
						ON tab_entidade.id_entidade = tab_recibo.id_entidade
						WHERE id_recibo = :id_recibo";

		parent::Execute();
	}

	public function Delete($id_recibo) {

		$this->data = [
			"id_recibo" => $id_recibo
		];

		$this->query = "DELETE from tab_recibo 
						WHERE id_recibo = :id_recibo";	
		
		parent::Execute();

		return parent::rowCount();
	}

	public function DeleteAll() {

		$this->data = [];

		$this->query = "DELETE FROM tab_recibo";		

		parent::Execute();

		return parent::rowCount();
	}

	public static function number_in_full($number) {

		if (!is_numeric($number)) {
			return false;
		}
	
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}
	
		if ($number < 2 && $number > -2) {
			$coin = " real";
		} else {
			$coin = " reais";
		}
	
		$string = $fraction = null;
	
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
	
		$string = self::convert_number_to_words($number);
		$string .= $coin;
		if ($fraction > 0) {
			$string .= self::convert_decimal_to_words($fraction);
		}
	
		return $string;
	
	}
	
	public static function convert_number_to_words($number) {
	
		$hyphen      = '-';
		$conjunction = ' e ';
		$separator   = ', ';
		$negative    = 'menos ';
		$decimal     = ' e ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'um',
			2                   => 'dois',
			3                   => 'três',
			4                   => 'quatro',
			5                   => 'cinco',
			6                   => 'seis',
			7                   => 'sete',
			8                   => 'oito',
			9                   => 'nove',
			10                  => 'dez',
			11                  => 'onze',
			12                  => 'doze',
			13                  => 'treze',
			14                  => 'quatorze',
			15                  => 'quinze',
			16                  => 'dezesseis',
			17                  => 'dezessete',
			18                  => 'dezoito',
			19                  => 'dezenove',
			20                  => 'vinte',
			30                  => 'trinta',
			40                  => 'quarenta',
			50                  => 'cinquenta',
			60                  => 'sessenta',
			70                  => 'setenta',
			80                  => 'oitenta',
			90                  => 'noventa',
			100                 => 'cento',
			200                 => 'duzentos',
			300                 => 'trezentos',
			400                 => 'quatrocentos',
			500                 => 'quinhentos',
			600                 => 'seiscentos',
			700                 => 'setecentos',
			800                 => 'oitocentos',
			900                 => 'novecentos',
			1000                => 'mil',
			1000000             => array('milhão', 'milhões'),
			1000000000          => array('bilhão', 'bilhões'),
			1000000000000       => array('trilhão', 'trilhões'),
			1000000000000000    => array('quatrilhão', 'quatrilhões'),
			1000000000000000000 => array('quinquilhão', 'quinquilhões')
		);
	
		if ($number < 0) {
			return $negative . self::convert_number_to_words(abs($number));
		}
	
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				// if (!$recursive) {
					// $string .= " reais";  
				// }
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $conjunction . $dictionary[$units];
				}
				// if (!$recursive) {
					// $string .= " reais";
				// }
				break;
			case $number < 1000:
				$hundreds  = floor($number / 100)*100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds];
				if ($remainder) {
					$string .= $conjunction . self::convert_number_to_words($remainder);
				// } else {
					// $string .= " reais";
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				if ($baseUnit == 1000) {
					$string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[1000];
				} elseif ($numBaseUnits == 1) {
					$string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
				} else {
					$string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
				}
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= self::convert_number_to_words($remainder, $remainder);
				// } else {
				//     $string .= " reais";
				}
				break;
		}
	
		return $string;
	}
	
	public static function convert_decimal_to_words($number) {
	
		$hyphen      = '-';
		$conjunction = ' e ';
		$separator   = ', ';
		$negative    = 'menos ';
		$decimal     = ' e ';
		$string      = '';
	
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'um',
			2                   => 'dois',
			3                   => 'três',
			4                   => 'quatro',
			5                   => 'cinco',
			6                   => 'seis',
			7                   => 'sete',
			8                   => 'oito',
			9                   => 'nove',
			10                  => 'dez',
			11                  => 'onze',
			12                  => 'doze',
			13                  => 'treze',
			14                  => 'quatorze',
			15                  => 'quinze',
			16                  => 'dezesseis',
			17                  => 'dezessete',
			18                  => 'dezoito',
			19                  => 'dezenove',
			20                  => 'vinte',
			30                  => 'trinta',
			40                  => 'quarenta',
			50                  => 'cinquenta',
			60                  => 'sessenta',
			70                  => 'setenta',
			80                  => 'oitenta',
			90                  => 'noventa',
			100                 => 'cento',
			200                 => 'duzentos',
			300                 => 'trezentos',
			400                 => 'quatrocentos',
			500                 => 'quinhentos',
			600                 => 'seiscentos',
			700                 => 'setecentos',
			800                 => 'oitocentos',
			900                 => 'novecentos',
			1000                => 'mil',
			1000000             => array('milhão', 'milhões'),
			1000000000          => array('bilhão', 'bilhões'),
			1000000000000       => array('trilhão', 'trilhões'),
			1000000000000000    => array('quatrilhão', 'quatrilhões'),
			1000000000000000000 => array('quinquilhão', 'quinquilhões')
		);
	
		if (null !== $number && is_numeric($number)) {
			$number = $number * 1;
			$string .= $decimal;
	
			// $number = $fraction;
	
			switch (true) {
				case $number < 2:
					$string .= $dictionary[$number];
					$string .= " centavo";
					break;
				case $number < 21:
					$string .= $dictionary[$number];
					$string .= " centavos";
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string .= $dictionary[$tens];
					if ($units) {
						$string .= $conjunction . $dictionary[$units];
					}
					$string .= " centavos";
					break;
			}
			// $words = array();
			// foreach (str_split((string) $fraction) as $number) {
			//     $words[] = $dictionary[$number];
			// }
			// $string .= implode(' ', $words);
		} else {
			$string = "";
		}
	
		return $string;
	}

	public static function FormatFields($row) {

		$row['valor_extenso'] = self::number_in_full($row['valor']);
		$row['valor_formatted'] = number_format($row['valor'],2,",",".");
		$row['data_formatted'] = date_format( date_create($row['data']), 'd/m/Y');

		return $row;
	}
}