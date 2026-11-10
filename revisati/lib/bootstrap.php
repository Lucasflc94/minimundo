<?php
// lib/bootstrap.php - inicialização da aplicação
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/reviews.php';
garantir_arquivos_dados();
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$mensagem_flash = obter_mensagem();
$config = ler_json('settings.json', ['app_name'=>'RevisaTI','intervals'=>[7,15,30],'alert_email'=>'']);
define('NOME_APP', $config['app_name'] ?? 'RevisaTI');

// Importa o cronograma automaticamente na primeira execução
if (count(obter_assuntos()) === 0 && is_file(caminho_dados('cronograma.json'))) {
    importar_cronograma();
}
