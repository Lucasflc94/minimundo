<?php
// index.php - painel principal
require_once __DIR__ . '/includes/header.php';

$assuntos = obter_assuntos();
$revisoes = obter_revisoes();
$pendentes = revisoes_pendentes();
$proximas = revisoes_proximas(7);
$atrasadas = array_filter($pendentes, fn($r) => $r['due_date'] < hoje());
$hoje_rev = array_filter($pendentes, fn($r) => $r['due_date'] === hoje());

$concluidas = count(array_filter($revisoes, fn($r) => $r['status'] === 'done'));
$pendentes_total = count(array_filter($revisoes, fn($r) => $r['status'] === 'pending'));
$assuntos_estudados = count(array_filter($assuntos, fn($a) => ($a['status'] ?? '') === 'studied'));
?>
<h1>Painel</h1>
<div class="grid">
  <div class="stat"><div class="n"><?= count($assuntos) ?></div><div class="l">Assuntos cadastrados</div></div>
  <div class="stat"><div class="n"><?= $assuntos_estudados ?></div><div class="l">Assuntos estudados</div></div>
  <div class="stat"><div class="n" style="color:var(--danger)"><?= count($atrasadas) ?></div><div class="l">Revisões atrasadas</div></div>
  <div class="stat"><div class="n" style="color:var(--warn)"><?= count($hoje_rev) ?></div><div class="l">Revisões para hoje</div></div>
  <div class="stat"><div class="n"><?= $pendentes_total ?></div><div class="l">Revisões pendentes</div></div>
  <div class="stat"><div class="n" style="color:var(--ok)"><?= $concluidas ?></div><div class="l">Revisões concluídas</div></div>
</div>

<?php if (count($pendentes) === 0): ?>
<div class="card"><p class="empty">🎉 Nenhuma revisão pendente. Bom trabalho!</p></div>
<?php else: ?>
<div class="card">
  <h2>⚠️ Revisões pendentes (atrasadas + hoje)</h2>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Assunto</th><th>Intervalo</th><th>Vencimento</th><th>Status</th><th>Ação</th></tr></thead>
    <tbody>
    <?php foreach ($pendentes as $revisao):
        $assunto = encontrar_assunto($revisao['subject_id']);
        $atrasada = $revisao['due_date'] < hoje();
    ?>
    <tr>
      <td><?= escapar($assunto['lesson'] ?? $assunto['title'] ?? 'Assunto removido') ?><br><small style="color:var(--muted)"><?= escapar($assunto['category'] ?? '') ?></small></td>
      <td><?= (int)$revisao['interval'] ?> dias</td>
      <td><?= formatar_data($revisao['due_date']) ?> <?= $atrasada ? '<span class="badge badge-overdue">atrasada</span>' : '<span class="badge badge-today">hoje</span>' ?></td>
      <td><span class="badge badge-pending">pendente</span></td>
      <td>
        <form method="post" action="<?= BASE_URL ?>/review_action.php" style="display:inline">
          <input type="hidden" name="id" value="<?= escapar($revisao['id']) ?>">
          <input type="hidden" name="action" value="done">
          <button class="btn btn-sm" type="submit">Marcar revisado</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <h2>📅 Próximas revisões (7 dias)</h2>
  <?php if (count($proximas) === 0): ?>
    <p class="empty">Nenhuma revisão agendada para os próximos 7 dias.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Assunto</th><th>Intervalo</th><th>Vencimento</th></tr></thead>
    <tbody>
    <?php foreach ($proximas as $revisao):
        $assunto = encontrar_assunto($revisao['subject_id']);
    ?>
    <tr>
      <td><?= escapar($assunto['lesson'] ?? $assunto['title'] ?? 'Assunto removido') ?></td>
      <td><?= (int)$revisao['interval'] ?> dias</td>
      <td><?= formatar_data($revisao['due_date']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>🔔 Alertas</h2>
  <p>Use o script <code>php cron.php</code> (linha de comando) para gerar alertas diários em <code>data/alerts.log</code>. Revisões atrasadas são destacadas acima.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
