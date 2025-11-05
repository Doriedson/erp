<?php
require __DIR__.'/../vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');
try {
  App\Infra\Database\Connection::pdo()->query('SELECT 1');
  echo json_encode(['db'=>'ok']);
} catch (Throwable $e) {
  echo json_encode(['db'=>'error','msg'=>$e->getMessage()]);
}
