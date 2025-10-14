<?php
// export_to_csv.php
// Script para exportar todas as tabelas do MySQL em CSVs com prefixo de tabela nos nomes de colunas.

// 1) Carrega o .env manual
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, "\"' ");
        putenv("$name=$value");
        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
    }
}
loadEnv(__DIR__.'/.env');

// 2) Configurações de conexão
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: 3306;

// 3) Conecta via PDO
$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$pdo = new PDO($dsn, $user, $pass, $options);

// 4) Prepara diretório de exportação
$outputDir = __DIR__ . '/storage/exports';
if (!is_dir($outputDir)) {
    if (!mkdir($outputDir, 0755, true)) {
        fwrite(STDERR, "Erro: não foi possível criar $outputDir\n");
        exit(1);
    }
}

// 5) Obtém todas as tabelas
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (empty($tables)) {
    fwrite(STDOUT, "Nenhuma tabela encontrada em '$db'.\n");
    exit;
}

// 6) Exporta cada tabela
foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT * FROM `{$table}`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filePath = "{$outputDir}/{$table}.csv";
    $fp = fopen($filePath, 'w');
    if (!$fp) {
        fwrite(STDERR, "Falha ao abrir arquivo para escrita: $filePath\n");
        continue;
    }

    // Escreve BOM para UTF-8
    fwrite($fp, "\xEF\xBB\xBF");

    // Cabeçalho com prefixo de tabela
    $columns = [];
    $colCount = $stmt->columnCount();
    for ($i = 0; $i < $colCount; $i++) {
        $meta = $stmt->getColumnMeta($i);
        $columns[] = $meta['name'];
    }
    $prefixed = array_map(function($col) use ($table) {
        return "{$table}_{$col}";
    }, $columns);
    fputcsv($fp, $prefixed, ';');

    // Linhas de dados
    foreach ($rows as $record) {
        // Garante separador decimal ponto
        $values = [];
        foreach ($columns as $col) {
            $val = $record[$col];
            if (is_float($val) || (is_numeric($val) && strval($val) !== strval(intval($val)))) {
                $val = str_replace(',', '.', $val);
            }
            $values[] = $val;
        }
        fputcsv($fp, $values, ';');
    }

    fclose($fp);
    fwrite(STDOUT, "Exportado: {$filePath}\n");
}

fwrite(STDOUT, "Exportação concluída em {$outputDir}\n");
