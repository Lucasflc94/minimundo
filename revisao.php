<?php
// revisao.php — Integração do RevisaTI dentro do MiniMundo (layout compartilhado)
session_start();

// Carrega as funções do RevisaTI (subpasta revisati/)
require_once __DIR__ . '/revisati/lib/bootstrap.php';
require_once __DIR__ . '/revisati/lib/functions.php';
require_once __DIR__ . '/revisati/lib/reviews.php';

$assuntos     = obter_assuntos();
$revisoes     = obter_revisoes();
$pendentes    = revisoes_pendentes();
$proximas     = revisoes_proximas(7);
$atrasadas    = array_filter($pendentes, fn($r) => $r['due_date'] < hoje());
$hoje_rev     = array_filter($pendentes, fn($r) => $r['due_date'] === hoje());
$concluidas   = count(array_filter($revisoes, fn($r) => $r['status'] === 'done'));
$pendentes_t  = count(array_filter($revisoes, fn($r) => $r['status'] === 'pending'));
$estudados    = count(array_filter($assuntos, fn($a) => ($a['status'] ?? '') === 'studied'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Revisão Espaçada TI — MiniMundo da Cloe</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">
    <style>
        .nav-tabs a[href="revisao.php"] { background: var(--primary); }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>⚡ MINIMUNDO DA CLOE ⚡</h1>
            <p>🧠 Revisão Espaçada TI &bull; Ciclo de 7, 15 e 30 dias</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php">🏠 Home</a>
            <a href="itens.php">🍿 Lazer (Filmes/Livros/Rolês)</a>
            <a href="gastos.php">💰 Gestão de Gastos</a>
            <a href="tarefas.php">📋 Tarefas do Lucas</a>
            <a href="veiculos.php">🏍️🚗 Veículos (Tucson/Ténéré)</a>
            <a href="revisao.php" class="active">🧠 Revisão Espaçada TI</a>
        </div>

        <div class="card" style="text-align: center;">
            <h2>🧠 Revisão Espaçada — TI</h2>
            <p>Sistema de revisão dos assuntos de estudo de TI. A cada assunto estudado, são agendadas revisões em <strong>7, 15 e 30 dias</strong>.</p>
        </div>

        <div class="grid">
            <div class="stat"><div class="n"><?= count($assuntos) ?></div><div class="l">Assuntos cadastrados</div></div>
            <div class="stat"><div class="n"><?= $estudados ?></div><div class="l">Assuntos estudados</div></div>
            <div class="stat"><div class="n" style="color:var(--danger)"><?= count($atrasadas) ?></div><div class="l">Revisões atrasadas</div></div>
            <div class="stat"><div class="n" style="color:var(--warn)"><?= count($hoje_rev) ?></div><div class="l">Revisões para hoje</div></div>
            <div class="stat"><div class="n"><?= $pendentes_t ?></div><div class="l">Revisões pendentes</div></div>
            <div class="stat"><div class="n" style="color:var(--ok)"><?= $concluidas ?></div><div class="l">Revisões concluídas</div></div>
        </div>

        <?php if (count($pendentes) === 0): ?>
        <div class="card">
            <p class="empty">🎉 Nenhuma revisão pendente. Bom trabalho!</p>
        </div>
        <?php else: ?>
        <div class="card">
            <h2>⚠️ Revisões pendentes</h2>
            <div class="table-wrap">
            <table>
                <thead><tr><th>Assunto</th><th>Categoria</th><th>Intervalo</th><th>Vencimento</th><th>Status</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($pendentes as $revisao): 
                    $assunto = encontrar_assunto($revisao['subject_id']);
                    $atrasada = $revisao['due_date'] < hoje();
                ?>
                <tr>
                    <td><?= escapar($assunto['lesson'] ?? $assunto['title'] ?? 'Assunto removido') ?><br><small style="color:var(--muted)"><?= escapar($assunto['category'] ?? '') ?></small></td>
                    <td><?= escapar($assunto['category'] ?? '') ?></td>
                    <td><?= (int)$revisao['interval'] ?> dias</td>
                    <td><?= formatar_data($revisao['due_date']) ?> <?= $atrasada ? '<span style="color:var(--danger);font-weight:bold;">(atrasada)</span>' : '<span style="color:var(--warn);font-weight:bold;">(hoje)</span>' ?></td>
                    <td><span style="color:var(--warn);font-weight:bold;">pendente</span></td>
                    <td>
                        <a href="revisati/review_action.php?id=<?= escapar($revisao['id']) ?>&action=done" class="btn-sm" onclick="return confirm('Marcar esta revisão como concluída?')">✅ Revisado</a>
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
                    <td><?= escapar($assunto['lesson'] ?? $assunto['title'] ?? '') ?></td>
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
            <h2>🔗 Acesso rápido ao RevisaTI completo</h2>
            <p>Gerencie assuntos, importe cronograma e configure alertas:</p>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="revisati/subjects.php" class="btn">📚 Assuntos</a>
                <a href="revisati/reviews.php" class="btn">🔁 Revisões</a>
                <a href="revisati/import.php" class="btn">⬇️ Importar cronograma</a>
                <a href="revisati/settings.php" class="btn">⚙️ Configurações</a>
            </div>
            <p style="margin-top:12px; font-size:0.85rem; color:#64748b;">
                Dica: execute <code>php revisati/cron.php</code> no servidor para gerar alertas diários via CLI.
            </p>
        </div>

    </div>
</body>
</html>
