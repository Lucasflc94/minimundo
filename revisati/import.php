<?php
// import.php - importação do cronograma
require_once __DIR__ . '/includes/header.php';
$resultado = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = importar_cronograma();
    definir_mensagem('success', "Importação concluída: {$resultado['added']} novos assuntos adicionados (de {$resultado['imported']} registros).");
    redirecionar('/import.php');
}
$assuntos = obter_assuntos();
$total_bruto = count(ler_json('cronograma.json', []));
?>
<h1>Importar Cronograma</h1>
<div class="card">
  <p>Arquivo <code>data/cronograma.json</code> contém <strong><?= $total_bruto ?></strong> registros extraídos do PDF do cronograma (Nov/2026 a Fev/2027).</p>
  <p>Assuntos já cadastrados: <strong><?= count($assuntos) ?></strong>.</p>
  <form method="post">
    <button class="btn" type="submit">Importar / Atualizar assuntos</button>
  </form>
  <p style="color:var(--muted);font-size:.85rem;margin-top:10px">
    Dias marcados como "Revisão" no cronograma são ignorados (são dias de revisão geral). Assuntos já existentes não são duplicados.
  </p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
