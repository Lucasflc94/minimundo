<?php
// subject_action.php - ações sobre assuntos (marcar estudado)
require_once __DIR__ . '/lib/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') redirecionar(BASE_URL . '/subjects.php');
$id = $_REQUEST['id'] ?? '';
$acao = $_REQUEST['action'] ?? '';
// Extrair apenas o caminho (path) do referer, ignorando domínio completo
$volta = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH) ?: '/subjects.php';
// Se o referer não tiver o BASE_URL, adiciona
if (!empty(BASE_URL) && strpos($volta, BASE_URL) !== 0) {
    $volta = BASE_URL . $volta;
}

if ($acao === 'studied') {
    if (marcar_assunto_estudado($id)) definir_mensagem('success', 'Assunto marcado como estudado. Revisões de 7, 15 e 30 dias agendadas.');
    else definir_mensagem('error', 'Assunto não encontrado.');
}
redirecionar($volta);
