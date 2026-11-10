<?php
// subject_action.php - ações sobre assuntos (marcar estudado)
require_once __DIR__ . '/lib/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('/subjects.php');
$id = $_POST['id'] ?? '';
$acao = $_POST['action'] ?? '';
$volta = $_SERVER['HTTP_REFERER'] ?? '/subjects.php';

if ($acao === 'studied') {
    if (marcar_assunto_estudado($id)) definir_mensagem('success', 'Assunto marcado como estudado. Revisões de 7, 15 e 30 dias agendadas.');
    else definir_mensagem('error', 'Assunto não encontrado.');
}
redirecionar($volta);
