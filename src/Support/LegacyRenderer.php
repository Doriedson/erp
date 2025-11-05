<?php
namespace App\Support;


class LegacyRenderer
{
	private string $baseDir;

	/**
	* @param string $baseDir Diretório da raiz do legado (repo root)
	*/
	public function __construct(string $baseDir)
	{
		$this->baseDir = rtrim($baseDir, '/');
	}

	/**
	* Renderiza um arquivo legado (ex.: home.php) e retorna HTML
	* - Força chdir para o diretório base do legado
	* - Usa output buffering
	*/
	public function render(string $file): string
	{
		$target = $this->baseDir . '/' . ltrim($file, '/');

		if (!is_file($target)) {
			return 'Arquivo não encontrado: ' . htmlspecialchars($file);
		}

		$cwd = getcwd();
		$oldIncludePath = get_include_path();

		// 1) Ir para a raiz do projeto (onde está inc/, home.php, etc.)
		chdir($this->baseDir);

		// 2) Ampliar include_path para que 'inc/authorization.php' seja resolvido
		//    Mesmo que algum script altere o cwd, o include_path segura.
		$extra = $this->baseDir . PATH_SEPARATOR . $this->baseDir . '/inc';
		set_include_path($extra . PATH_SEPARATOR . $oldIncludePath);

		// 🔧 SEED de variáveis esperadas pelo legado
		if (!isset($publicPages) || !is_array($publicPages)) {
			// páginas públicas típicas; ajuste conforme seu projeto
			$publicPages = [
				'index.php',     // quando usado diretamente
				'home.php',      // nossa landing
				'login.php',     // se existir
				'auth.php',      // se existir
				'garcom.php',    // se precisar público
			];
		}

		// Opcional: alguns legados dependem disso
		$_SERVER['SCRIPT_NAME']    = $_SERVER['SCRIPT_NAME']    ?? '/index.php';
		$_SERVER['PHP_SELF']       = $_SERVER['PHP_SELF']       ?? '/index.php';
		$_SERVER['REQUEST_URI']    = $_SERVER['REQUEST_URI']    ?? '/';
		$_SERVER['QUERY_STRING']   = $_SERVER['QUERY_STRING']   ?? '';

		// 4) Opcional: muitas bases legadas usam isso
		if (!isset($_SERVER['DOCUMENT_ROOT'])) {
			$_SERVER['DOCUMENT_ROOT'] = $this->baseDir;
		}
		if (!defined('BASE_PATH')) {
			define('BASE_PATH', $this->baseDir);
		}

		// Renderiza a página legado
		ob_start();
		require $target;
		$html = ob_get_clean();

		chdir($cwd);
		return (string)$html;
	}
}