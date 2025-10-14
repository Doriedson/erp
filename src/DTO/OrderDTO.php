<?php
namespace App\DTO;

final class OrderDTO {
    public function __construct(
        public readonly string $cliente,
        public readonly string $endereco,
        public readonly string $bairro,
        public readonly float  $total,
        public readonly string $status = 'novo', // novo|em_rota|entregue|cancelado
        public readonly ?string $forma_pagto = null,
        public readonly ?string $telefone = null,
        public readonly ?string $observacao = null
    ) {}

    public static function fromArray(array $in): self {
        $cliente   = trim((string)($in['cliente']   ?? ''));
        $endereco  = trim((string)($in['endereco']  ?? ''));
        $bairro    = trim((string)($in['bairro']    ?? ''));
        $total     = (float)($in['total']           ?? 0);
        $status    = (string)($in['status']         ?? 'novo');
        $forma     = isset($in['forma_pagto']) ? (string)$in['forma_pagto'] : null;
        $telefone  = isset($in['telefone'])   ? (string)$in['telefone']   : null;
        $obs       = isset($in['observacao']) ? (string)$in['observacao'] : null;

        if ($cliente === '' || $endereco === '' || $bairro === '') {
            throw new \InvalidArgumentException('Campos obrigatórios: cliente, endereco, bairro');
        }
        if ($total <= 0) {
            throw new \InvalidArgumentException('Total deve ser maior que zero');
        }
        if (!in_array($status, ['novo','em_rota','entregue','cancelado'], true)) {
            throw new \InvalidArgumentException('Status inválido');
        }
        return new self($cliente, $endereco, $bairro, $total, $status, $forma, $telefone, $obs);
    }
}
