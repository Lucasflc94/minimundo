<?php
// subjects.php - lista de assuntos do cronograma
require_once __DIR__ . '/includes/header.php';

$busca = trim($_GET['q'] ?? '');
$filtro_mes = $_GET['month'] ?? '';
$filtro_status = $_GET['status'] ?? '';
$filtro_tipo = $_GET['type'] ?? '';

$assuntos = obter_assuntos();
$filtrados = array_filter($assuntos, function($assunto) use ($busca, $filtro_mes, $filtro_status, $filtro_tipo) {
    if ($busca && stripos($assunto['title'] ?? '', $busca) === false && stripos($assunto['lesson'] ?? '', $busca) === false) return false;
    if ($filtro_mes) {
        $mes = substr($assunto['study_date'] ?? '', 0, 7);
        if ($mes !== $filtro_mes) return false;
    }
    if ($filtro_status && ($assunto['status'] ?? '') !== $filtro_status) return false;
    if ($filtro_tipo && ($assunto['type'] ?? '') !== $filtro_tipo) return false;
    return true;
});
usort($filtrados, fn($a, $b) => strcmp($a['study_date'] ?? '', $b['study_date'] ?? ''));

$meses = [];
foreach ($assuntos as $assunto) {
    if ($assunto['study_date']) $meses[substr($assunto['study_date'], 0, 7)] = true;
}
ksort($meses);
?>
<h1>Assuntos do Cronograma</h1>
<form method="get" class="filters">
  <div class="form-row"><label>Busca</label><input type="text" name="q" value="<?= escapar($busca) ?>"></div>
  <div class="form-row"><label>Mês</label>
    <select name="month">
      <option value="">Todos</option>
      <?php foreach (array_keys($meses) as $mes): ?>
        <option value="<?= escapar($mes) ?>" <?= $filtro_mes===$mes?'selected':'' ?>><?= escapar($mes) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-row"><label>Status</label>
    <select name="status">
      <option value="">Todos</option>
      <option value="pending_study" <?= $filtro_status==='pending_study'?'selected':'' ?>>Não estudado</option>
      <option value="studied" <?= $filtro_status==='studied'?'selected':'' ?>>Estudado</option>
    </select>
  </div>
  <div class="form-row"><label>Tipo</label>
    <select name="type">
      <option value="">Todos</option>
      <option value="Videoaula" <?= $filtro_tipo==='Videoaula'?'selected':'' ?>>Videoaula</option>
      <option value="Revisao" <?= $filtro_tipo==='Revisao'?'selected':'' ?>>Revisão</option>
      <option value="Revisão" <?= $filtro_tipo==='Revisão'?'selected':'' ?>>Revisão</option>
    </select>
  </div>
  <div class="form-row"><label>&nbsp;</label><button class="btn" type="submit">Filtrar</button></div>
</form>

<p>Total: <?= count($filtrados) ?> assuntos</p>
<div class="table-wrap">
<table>
  <thead><tr><th>Data estudo</th><th>Assunto</th><th>Categoria</th><th>Tipo</th><th>Status</th><th>Ação</th></tr></thead>
  <tbody>
  <?php foreach ($filtrados as $assunto): ?>
  <tr>
    <td><?= formatar_data($assunto['study_date'] ?? '') ?></td>
    <td><?= escapar($assunto['lesson'] ?? $assunto['title']) ?></td>
    <td><small><?= escapar($assunto['category'] ?? '') ?></small></td>
    <td><?= escapar($assunto['type']) ?></td>
    <td>
      <?php if (($assunto['status'] ?? '') === 'studied'): ?>
        <span class="badge badge-done">estudado</span>
      <?php else: ?>
        <span class="badge badge-pending">pendente</span>
      <?php endif; ?>
    </td>
    <td>
      <?php if (($assunto['status'] ?? '') !== 'studied'): ?>
        <form method="post" action="<?= BASE_URL ?>/subject_action.php" style="display:inline">
          <input type="hidden" name="id" value="<?= escapar($assunto['id']) ?>">
          <input type="hidden" name="action" value="studied">
          <button class="btn btn-sm" type="submit">Marcar estudado</button>
        </form>
      <?php else: ?>
        <a class="btn btn-sm btn-secondary" href="<?= BASE_URL ?>/reviews.php?subject=<?= escapar($assunto['id']) ?>">Ver revisões</a>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (count($filtrados)===0): ?>
  <tr><td colspan="6" class="empty">Nenhum assunto encontrado.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
