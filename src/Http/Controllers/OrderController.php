<?php
namespace App\Http\Controllers;

use App\Infra\Repositories\OrderRepository;

class OrderController {
    private OrderRepository $repo;

    public function __construct() {
        $this->repo = new OrderRepository();
    }

    private function json(array $payload): array { return $payload; }

    public function index(): array {
        $limit  = (int)($_GET['limit']  ?? 20);
        $offset = (int)($_GET['offset'] ?? 0);
        $q      = (string)($_GET['q'] ?? '');
        $sort   = (string)($_GET['sort'] ?? 'data');
        $dir    = (string)($_GET['dir']  ?? 'desc');

        $out = $this->repo->list([
            'limit'  => $limit,
            'offset' => $offset,
            'q'      => $q,
            'sort'   => $sort,
            'dir'    => $dir,
        ]);

        header('X-Total: ' . $out['total']);
        header('X-Limit: ' . $out['limit']);
        header('X-Offset: ' . $out['offset']);

        return $this->json($out);
    }

    public function store(): array {
        // TODO: implementar criação conforme seu fluxo
        http_response_code(501);
        return ['error' => 'Not Implemented'];
    }

    public function updateStatus(int $id): array {
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        if (isset($data['id_vendastatus'])) {
            $ok = $this->repo->updateStatusById($id, (int)$data['id_vendastatus']);
        } elseif (!empty($data['vendastatus'])) {
            $ok = $this->repo->updateStatusByName($id, (string)$data['vendastatus']);
        } else {
            http_response_code(422);
            return ['error' => 'Informe id_vendastatus ou vendastatus'];
        }

        return ['ok' => (bool)$ok];
    }
}
