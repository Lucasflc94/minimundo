<?php
// settings.php - configurações da aplicação
require_once __DIR__ . '/includes/header.php';
$config = ler_json('settings.json', ['app_name'=>'RevisaTI','intervals'=>[7,15,30],'alert_email'=>'']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['app_name'] = trim($_POST['app_name'] ?? 'RevisaTI');
    $config['alert_email'] = trim($_POST['alert_email'] ?? '');
    $bruto = trim($_POST['intervals'] ?? '7,15,30');
    $intervalos = array_map('intval', array_map('trim', explode(',', $bruto)));
    $intervalos = array_values(array_filter($intervalos, fn($x) => $x > 0));
    if (empty($intervalos)) $intervalos = [7, 15, 30];
    $config['intervals'] = $intervalos;
    gravar_json('settings.json', $config);
    definir_mensagem('success', 'Configurações salvas.');
    redirecionar('/settings.php');
}
?>
<h1>Configurações</h1>
<div class="card">
  <form method="post">
    <div class="form-row"><label>Nome do aplicativo</label><input type="text" name="app_name" value="<?= escapar($config['app_name']) ?>"></div>
    <div class="form-row"><label>E-mail para alertas (opcional)</label><input type="email" name="alert_email" value="<?= escapar($config['alert_email']) ?>"></div>
    <div class="form-row"><label>Intervalos de revisão (dias, separados por vírgula)</label>
      <input type="text" name="intervals" value="<?= escapar(implode(',', $config['intervals'])) ?>">
    </div>
    <button class="btn" type="submit">Salvar</button>
  </form>
  <p style="color:var(--muted);font-size:.85rem;margin-top:10px">Intervalos padrão: 7, 15 e 30 dias.</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
