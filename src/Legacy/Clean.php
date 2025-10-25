<?php

namespace App\Legacy;

class Clean {

	public static function HtmlChar($str){
		return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	}

	public static function DuplicateSpace($data) {
		$data = (string) $data;
		$space = 0;
		$length = mb_strlen($data);
		$dataClean = "";

		//remover espaços excedentes no meio da palavra
		for($x = 0; $x < $length; $x++){

			$letter = substr($data, $x, 1);

			if($letter == " "){
				if($space==0){
					$space=1;
						$dataClean .= $letter;
				}
			} else {
				$space=0;
					$dataClean .= $letter;
			}
		}

		return $dataClean;
	}

	public static function converter_data_sql($strData) {
		// Recebemos a data no formato: dd/mm/aaaa
		// Convertemos a data para o formato: aaaa-mm-dd
		if ( preg_match("#/#",$strData) == 1 ) {
			$strDataFinal = implode('-', array_reverse(explode('/',$strData)));
		}
		return $strDataFinal;
	}

	public static function converter_data_br($strData) {
		// Recebemos a data no formato: aaaa-mm-dd
		// Convertemos a data para o formato: dd/mm/aaaa
		if ( preg_match("#-#",$strData) == 1 ) {
			$strDataFinal = implode('/', array_reverse(explode('-',$strData)));
		}
		return $strDataFinal;
	}
}
?>
