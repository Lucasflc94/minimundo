<?php
// lib/storage.php - funções de leitura/escrita de arquivos JSON

// Caminho absoluto do arquivo de dados
function caminho_dados(string $arquivo): string {
    return dirname(__DIR__) . '/data/' . $arquivo;
}

// Lê um arquivo JSON e retorna array (ou valor padrão)
function ler_json(string $arquivo, $padrao = []) {
    $caminho = caminho_dados($arquivo);
    if (!is_file($caminho)) {
        return $padrao;
    }
    $ponteiro = fopen($caminho, 'r');
    if (!$ponteiro) return $padrao;
    flock($ponteiro, LOCK_SH);                 // trava de leitura
    $conteudo = stream_get_contents($ponteiro);
    flock($ponteiro, LOCK_UN);
    fclose($ponteiro);
    $decodificado = json_decode($conteudo, true);
    return (json_last_error() === JSON_ERROR_NONE && is_array($decodificado)) ? $decodificado : $padrao;
}

// Grava um array em arquivo JSON (com trava de escrita)
function gravar_json(string $arquivo, $dados): bool {
    $caminho = caminho_dados($arquivo);
    $diretorio = dirname($caminho);
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0775, true);
    }
    $ponteiro = fopen($caminho, 'c');
    if (!$ponteiro) return false;
    flock($ponteiro, LOCK_EX);                 // trava de escrita
    ftruncate($ponteiro, 0);
    rewind($ponteiro);
    fwrite($ponteiro, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($ponteiro);
    flock($ponteiro, LOCK_UN);
    fclose($ponteiro);
    return true;
}

// Garante que os arquivos de dados existam
function garantir_arquivos_dados(): void {
    if (!is_file(caminho_dados('subjects.json'))) {
        gravar_json('subjects.json', []);
    }
    if (!is_file(caminho_dados('reviews.json'))) {
        gravar_json('reviews.json', []);
    }
    if (!is_file(caminho_dados('settings.json'))) {
        gravar_json('settings.json', [
            'app_name' => 'RevisaTI',
            'intervals' => [7, 15, 30],
            'alert_email' => '',
        ]);
    }
    if (!is_file(caminho_dados('alerts.log'))) {
        file_put_contents(caminho_dados('alerts.log'), '');
    }
}
