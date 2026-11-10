<?php
// lib/bootstrap.php - inicialização da aplicação
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/reviews.php';
garantir_arquivos_dados();
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$mensagem_flash = obter_mensagem();
$config = ler_json('settings.json', ['app_name'=>'RevisaTI','intervals'=>[7,15,30],'alert_email'=>'']);
define('NOME_APP', $config['app_name'] ?? 'RevisaTI');

// URL base para links (detecta automaticamente quando integrado em subpasta, ex: /revisati)
// Permite override via variável de ambiente REVISATI_BASE_URL
if (isset($_SERVER['REVISATI_BASE_URL'])) {
    define('BASE_URL', rtrim($_SERVER['REVISATI_BASE_URL'], '/'));
} else {
    // Detecta o caminho base a partir do nome do script executado
    $script = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
    $partes = explode('/', trim($script, '/'));
    // Remove o nome do arquivo (última parte) para obter o diretório base
    array_pop($partes);
    $base = implode('/', $partes);
    define('BASE_URL', $base ? '/' . $base : '');
}

// Importa o cronograma automaticamente na primeira execução
if (count(obter_assuntos()) === 0 && is_file(caminho_dados('cronograma.json'))) {
    importar_cronograma();
}
