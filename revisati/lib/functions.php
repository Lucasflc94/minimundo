<?php
// lib/functions.php - funções auxiliares gerais
require_once __DIR__ . '/storage.php';

// Gera um identificador único
function gerar_id(): string {
    return bin2hex(random_bytes(8)) . '-' . time();
}

// Data de hoje (AAAA-MM-DD)
function hoje(): string {
    return date('Y-m-d');
}

// Soma dias a uma data
function somar_dias(string $data, int $dias): string {
    $data_obj = new DateTime($data);
    $data_obj->modify("+{$dias} days");
    return $data_obj->format('Y-m-d');
}

// Escapa texto para HTML
function escapar($texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Formata data para padrão brasileiro
function formatar_data(string $data): string {
    if (!$data) return '-';
    $data_obj = DateTime::createFromFormat('Y-m-d', $data);
    return $data_obj ? $data_obj->format('d/m/Y') : $data;
}

// Diferença em dias entre uma data e hoje
function diferenca_dias(string $data): int {
    $a = new DateTime($data);
    $b = new DateTime(hoje());
    return (int)$b->diff($a)->format('%r%a');
}

// Separa título em categoria e aula
function separar_titulo(string $titulo): array {
    // Ex.: "Conhecimentos Específicos | 1 - Fundamentos do Big Data"
    $partes = explode('|', $titulo, 2);
    $categoria = trim($partes[0] ?? '');
    $aula = trim($partes[1] ?? $titulo);
    return [$categoria, $aula];
}

// Guarda mensagem temporária (flash)
function definir_mensagem(string $tipo, string $msg): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['mensagem_flash'] = ['tipo' => $tipo, 'msg' => $msg];
}

// Recupera e limpa mensagem temporária
function obter_mensagem(): ?array {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (isset($_SESSION['mensagem_flash'])) {
        $m = $_SESSION['mensagem_flash'];
        unset($_SESSION['mensagem_flash']);
        return $m;
    }
    return null;
}

// Redireciona para outra página
function redirecionar(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Intervalos de revisão configurados
function intervalos_revisao(): array {
    $config = ler_json('settings.json', ['intervals' => [7, 15, 30]]);
    return $config['intervals'] ?? [7, 15, 30];
}
