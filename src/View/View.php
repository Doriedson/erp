<?php

namespace App\View;

final class View {

	private string $template;
	private array $blocks = [];

	public const ALL = "ALL";

	/**
	 * Regular expression to find var and block names.
	 * Only alfa-numeric chars and the underscore char are allowed.
	 *
	 * @var		string
	 */
	private string $REG_NAME = "([[:alnum:]]|_)+";

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
	public function __construct(string $template){

		$base = \dirname(__DIR__, 2); // sai de src/View -> src -> raiz do projeto
		$file = $base . '/templates/' . $template . '.tpl';

		if (!file_exists($file)) {
			echo "File $file does not exist.";
			return;
		}
		$this->template = preg_replace("/<!--[^ ].*?-->/smi", "", file_get_contents($file));

		if ($this->template === '') {

            echo "file $file is empty";
            return;
        }

		// Bloco ALL = arquivo inteiro sem comentários
        $this->blocks[self::ALL] = preg_replace("/<!--.*?-->/smi", "", $this->template);

		// $reg = "/<!--\s*BEGIN\s+(".$this->REG_NAME.")\s*-->/sm";

		// $match = preg_match_all($reg, $this->template, $m);

		// Descobre blocos <!-- BEGIN NAME --> ... <!-- END NAME -->
        $reg = "/<!--\s*BEGIN\s+(".$this->REG_NAME.")\s*-->/sm";
        preg_match_all($reg, $this->template, $m);

		foreach ($m[1] as $block) {

            $regBlock = "/<!--\s*BEGIN\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+END\s+$block\s*-->/sm";

			if (1 !== preg_match($regBlock, $this->template, $block_content)) {
                echo "Mal-formed block $block";
            }

            // Remove EXTRA_BLOCK_* e seu conteúdo de dentro do bloco
            $regExtra = "/<!--\s*BEGIN\s+(EXTRA_([[:alnum:]]|_)+)\s*-->/sm";

            if (preg_match_all($regExtra, $block_content[1], $blocks_extra)) {

                foreach ($blocks_extra[1] as $block_extra) {

                    $block_content[1] = preg_replace(
                        "/<!--\s*BEGIN\s" . $block_extra . "\s+-->\s*(\s*.*?\s*)<!--\s+END\s" . $block_extra . "\s*-->/sm",
                        "",
                        $block_content[1]
                    );
                }
            }

            // Limpa comentários do bloco final
            $this->blocks[$block] = preg_replace("/<!--.*?-->/smi", "", $block_content[1]);
        }
	}

	/** Substitui {chave} por valor e retorna conteúdo do bloco */
    public function getContent(array $data, string $block): string {

		$map = [];

        foreach ($data as $key => $value) {

            $map['{' . $key . '}'] = (string)$value;
        }

        return strtr($this->blocks[$block] ?? '', $map);
	}

	/** Faz echo do conteúdo do bloco */
    public function Show(array $data, string $block): void {

		echo $this->getContent($data, $block);
	}
}