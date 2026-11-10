<?php
// review_action.php - ações sobre revisões (concluir/pular)
require_once __DIR__ . '/lib/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('/index.php');
$id = $_POST['id'] ?? '';
$acao = $_POST['action'] ?? '';
$volta = $_SERVER['HTTP_REFERER'] ?? '/index.php';

if ($acao === 'done') {
    if (marcar_revisao_concluida($id)) definir_mensagem('success', 'Revisão marcada como concluída.');
    else definir_mensagem('error', 'Revisão não encontrada.');
} elseif ($acao === 'skip') {
    if (marcar_revisao_pulada($id)) definir_mensagem('success', 'Revisão pulada.');
    else definir_mensagem('error', 'Revisão não encontrada.');
}
redirecionar($volta);
