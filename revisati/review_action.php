<?php
// review_action.php - ações sobre revisões (concluir/pular)
require_once __DIR__ . '/lib/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') redirecionar(BASE_URL . '/index.php');
$id = $_REQUEST['id'] ?? '';
$acao = $_REQUEST['action'] ?? '';
// Extrair apenas o caminho (path) do referer, ignorando domínio completo
$volta = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH) ?: '/index.php';
// Se o referer não tiver o BASE_URL, adiciona
if (!empty(BASE_URL) && strpos($volta, BASE_URL) !== 0) {
    $volta = BASE_URL . $volta;
}

if ($acao === 'done') {
    if (marcar_revisao_concluida($id)) definir_mensagem('success', 'Revisão marcada como concluída.');
    else definir_mensagem('error', 'Revisão não encontrada.');
} elseif ($acao === 'skip') {
    if (marcar_revisao_pulada($id)) definir_mensagem('success', 'Revisão pulada.');
    else definir_mensagem('error', 'Revisão não encontrada.');
}
redirecionar($volta);
