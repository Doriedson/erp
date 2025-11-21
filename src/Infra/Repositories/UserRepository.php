<?php
namespace App\Infra\Repositories;


use App\Infra\Database\Connection; use PDO;


class UserRepository
{
	public function __construct(private Connection $conn) {}


	/** Ajuste a TABELA/CAMPOS conforme seu schema */
	public function findByUsername(string $username): ?array
	{
		$sql = 'SELECT id, username, password_hash, name FROM users WHERE username = :u LIMIT 1';
		$st = $this->conn->pdo()->prepare($sql);
		$st->bindValue(':u', $username);
		$st->execute();
		$row = $st->fetch(PDO::FETCH_ASSOC) ?: null;
		return $row ?: null;
	}
}