<?php
// cron.php - alertas de revisão (executar via linha de comando)
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Apenas linha de comando.\n";
    exit;
}
require_once __DIR__ . '/lib/bootstrap.php';

$pendentes = revisoes_pendentes();
$atrasadas = array_filter($pendentes, fn($r) => $r['due_date'] < hoje());
$hoje_rev = array_filter($pendentes, fn($r) => $r['due_date'] === hoje());

$linhas = [];
$linhas[] = "=== Alerta de Revisão - " . date('d/m/Y H:i') . " ===";
$linhas[] = "Atrasadas: " . count($atrasadas) . " | Hoje: " . count($hoje_rev) . " | Total pendentes: " . count($pendentes);
$linhas[] = "";

foreach ($pendentes as $revisao) {
    $assunto = encontrar_assunto($revisao['subject_id']);
    $nome = $assunto['lesson'] ?? $assunto['title'] ?? 'Assunto removido';
    $etiqueta = $revisao['due_date'] < hoje() ? 'ATRASADA' : 'HOJE';
    $linhas[] = "[{$etiqueta}] {$nome} ({$revisao['interval']} dias) - venc: " . formatar_data($revisao['due_date']);
}

$relatorio = implode("\n", $linhas) . "\n";
file_put_contents(caminho_dados('alerts.log'), $relatorio, FILE_APPEND);
echo $relatorio;

// Envio de e-mail opcional
$config = ler_json('settings.json', []);
$email = $config['alert_email'] ?? '';
if ($email && count($pendentes) > 0) {
    $assunto_email = NOME_APP . " - " . count($pendentes) . " revisões pendentes";
    $cabecalhos = "From: noreply@revisati.local\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($email, $assunto_email, $relatorio, $cabecalhos);
    echo "E-mail enviado para {$email}\n";
}
