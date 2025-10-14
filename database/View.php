<?php

namespace database;

class View {
	
	private $template;
	private $blocks = array();

	const ALL = "ALL";

	/**
	 * Regular expression to find var and block names.
	 * Only alfa-numeric chars and the underscore char are allowed.
	 *
	 * @var		string
	 */
	private $REG_NAME = "([[:alnum:]]|_)+";

	/**
	 * Creates a new template, using $filename as main file.
	 *
	 * When the parameter $accurate is true, blocks will be replaced perfectly
	 * (in the parse time), e.g., removing all \t (tab) characters, making the
	 * final document an accurate version. This will impact (a lot) the
	 * performance. Usefull for files using the &lt;pre&gt; or &lt;code&gt; tags.
	 *
	 * @param     string $filename		file path of the file to be loaded
	 */
	public function __construct($template){

		$template = (string) __DIR__ . "/../" . $template . ".tpl";

		if (!file_exists($template)) {
		
			echo "File $template does not exist.";

			return;
		}

		$this->template = preg_replace("/<!--[^ ].*?-->/smi", "", file_get_contents($template));
		
		if (empty($this->template)) {
		
			echo "file $template is empty";
			return;
		}

		$this->blocks["ALL"] = preg_replace("/<!--.*?-->/smi", "", $this->template);	

		$reg = "/<!--\s*BEGIN\s+(".$this->REG_NAME.")\s*-->/sm";

		$match = preg_match_all($reg, $this->template, $m);

		foreach ($m[1] as $block) {

			// echo "match $block<br>";
			$reg = "/<!--\s*BEGIN\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+END\s+$block\s*-->/sm";

			if (1!==preg_match($reg, $this->template, $block_content)) {
				
				echo "Mal-formed block $block";
			} 
				
			// Limpar os EXTRA_BLOCK removendo seu conteúdo junto.
			$reg = "/<!--\s*BEGIN\s+(EXTRA_([[:alnum:]]|_)+)\s*-->/sm";

			$match = preg_match_all($reg, $block_content[1], $blocks_extra);
			
			foreach ($blocks_extra[1] as $block_extra) {
				
				$block_content[1] = preg_replace("/<!--\s*BEGIN\s" . $block_extra . "\s+-->\s*(\s*.*?\s*)<!--\s+END\s" . $block_extra . "\s*-->/sm", "", $block_content[1]);
			}
			
			// Limpar todos os comentários.
			$this->blocks[$block] = preg_replace("/<!--.*?-->/smi", "", $block_content[1]);
		}
	}

	public function getContent($data, $block) {

		$array = [];
	
		foreach ($data as $key => $value) {

			$array["{" . $key . "}"] = $value;
		}
		
		return str_replace(array_keys($array), $array, $this->blocks[$block]);
	}

	public function Show($data, $block) {

		echo $this->getContent($data, $block);
	}
}