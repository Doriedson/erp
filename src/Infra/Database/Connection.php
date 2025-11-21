<?php
namespace App\Infra\Database;


use PDO; use PDOException; use RuntimeException;


class Connection
{
    public function __construct(
        private string $host,
        private string $db,
        private string $user,
        private string $pass,
        private int $port = 3306,
        private string $charset = 'utf8mb4'
    ) {}


    public function pdo(): PDO
    {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";

        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try { return new PDO($dsn, $this->user, $this->pass, $opts); }
            catch (PDOException $e) { throw new RuntimeException('DB connection failed: '.$e->getMessage(), 0, $e); }
        }
}