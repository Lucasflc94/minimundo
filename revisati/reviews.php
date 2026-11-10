<?php
// reviews.php - lista de revisões
require_once __DIR__ . '/includes/header.php';

$filtro_assunto = $_GET['subject'] ?? '';
$filtro_status = $_GET['status'] ?? '';
$filtro_intervalo = $_GET['interval'] ?? '';

$revisoes = obter_revisoes();
$filtradas = array_filter($revisoes, function($revisao) use ($filtro_assunto, $filtro_status, $filtro_intervalo) {
    if ($filtro_assunto && $revisao['subject_id'] !== $filtro_assunto) return false;
    if ($filtro_status && $revisao['status'] !== $filtro_status) return false;
    if ($filtro_intervalo && (int)$revisao['interval'] !== (int)$filtro_intervalo) return false;
    return true;
});
usort($filtradas, fn($a, $b) => strcmp($a['due_date'], $b['due_date']));
?>
<h1>Revisões</h1>
<form method="get" class="filters">
  <div class="form-row"><label>Status</label>
    <select name="status">
      <option value="">Todos</option>
      <option value="pending" <?= $filtro_status==='pending'?'selected':'' ?>>Pendente</option>
      <option value="done" <?= $filtro_status==='done'?'selected':'' ?>>Concluída</option>
      <option value="skipped" <?= $filtro_status==='skipped'?'selected':'' ?>>Pulada</option>
    </select>
  </div>
  <div class="form-row"><label>Intervalo</label>
    <select name="interval">
      <option value="">Todos</option>
      <option value="7" <?= $filtro_intervalo==='7'?'selected':'' ?>>7 dias</option>
      <option value="15" <?= $filtro_intervalo==='15'?'selected':'' ?>>15 dias</option>
      <option value="30" <?= $filtro_intervalo==='30'?'selected':'' ?>>30 dias</option>
    </select>
  </div>
  <div class="form-row"><label>&nbsp;</label><button class="btn" type="submit">Filtrar</button></div>
</form>

<p>Total: <?= count($filtradas) ?> revisões</p>
<div class="table-wrap">
<table>
  <thead><tr><th>Assunto</th><th>Intervalo</th><th>Vencimento</th><th>Status</th><th>Concluída em</th><th>Ação</th></tr></thead>
  <tbody>
  <?php foreach ($filtradas as $revisao):
    $assunto = encontrar_assunto($revisao['subject_id']);
    $atrasada = $revisao['status']==='pending' && $revisao['due_date'] < hoje();
  ?>
  <tr>
    <td><?= escapar($assunto['lesson'] ?? $assunto['title'] ?? 'Assunto removido') ?></td>
    <td><?= (int)$revisao['interval'] ?> dias</td>
    <td><?= formatar_data($revisao['due_date']) ?> <?= $atrasada?'<span class="badge badge-overdue">atrasada</span>':'' ?></td>
    <td>
      <?php if ($revisao['status']==='done'): ?><span class="badge badge-done">concluída</span>
      <?php elseif ($revisao['status']==='skipped'): ?><span class="badge badge-skipped">pulada</span>
      <?php else: ?><span class="badge badge-pending">pendente</span><?php endif; ?>
    </td>
    <td><?= $revisao['done_at'] ? formatar_data($revisao['done_at']) : '-' ?></td>
    <td>
      <?php if ($revisao['status']==='pending'): ?>
        <form method="post" action="<?= BASE_URL ?>/review_action.php" style="display:inline">
          <input type="hidden" name="id" value="<?= escapar($revisao['id']) ?>">
          <input type="hidden" name="action" value="done">
          <button class="btn btn-sm" type="submit">Revisado</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/review_action.php" style="display:inline">
          <input type="hidden" name="id" value="<?= escapar($revisao['id']) ?>">
          <input type="hidden" name="action" value="skip">
          <button class="btn btn-sm btn-secondary" type="submit">Pular</button>
        </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (count($filtradas)===0): ?>
  <tr><td colspan="6" class="empty">Nenhuma revisão encontrada.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
