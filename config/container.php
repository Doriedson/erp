<?php
use DI\Container;
use App\Infra\Database\Connection;
use App\Auth\AuthService;
use App\Infra\Repositories\UserRepository;
use App\Infra\Repositories\CollaboratorRepository;
use App\View\ViewRenderer;


return (function (): Container {

	$c = new Container();

	// DB Connection (PDO)
	$c->set(Connection::class, function () {
		$host = $_ENV['DB_HOST'] ?? 'db';
		$db = $_ENV['DB_DATABASE'] ?? 'erp';
		$user = $_ENV['DB_USERNAME'] ?? 'erp';
		$pass = $_ENV['DB_PASSWORD'] ?? 'secret';
		$port = (int)($_ENV['DB_PORT'] ?? 3306);
		return new Connection($host, $db, $user, $pass, $port);
	});

	// Repository de usuário (ajuste SQL/tabela/colunas conforme seu banco)
	$c->set(UserRepository::class, fn(Container $c) => new UserRepository($c->get(Connection::class)));


	// Auth service (sessão + password_verify)
	$c->set(AuthService::class, fn(Container $c) => new AuthService($c->get(UserRepository::class)));

	// ...
	$c->set(CollaboratorRepository::class, fn($c) => new CollaboratorRepository($c->get(Connection::class)));

	// Se você já registra AuthService, troque por:
	$c->set(AuthService::class, fn($c) => new AuthService(
		$c->get(UserRepository::class),
		$c->get(CollaboratorRepository::class)
	));

	// Base do projeto (/var/www/html/erp)
    $BASE = dirname(__DIR__);

    // ViewRenderer com caminho para /templates
    $c->set(ViewRenderer::class, fn() => new ViewRenderer($BASE . '/templates'));

	return $c;
})();