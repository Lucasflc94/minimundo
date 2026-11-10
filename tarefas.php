<?php
require_once 'db.php';

// Inserir Tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO tabela_tarefas (tarefa) VALUES (?)");
    $stmt->execute([$_POST['tarefa']]);
    header("Location: tarefas.php");
    exit;
}

// Alternar Status (Pendente <-> Feito)
if (isset($_GET['status']) && isset($_GET['id'])) {
    $novo_status = $_GET['status'] == 'Pendente' ? 'Feito' : 'Pendente';
    $stmt = $pdo->prepare("UPDATE tabela_tarefas SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $_GET['id']]);
    header("Location: tarefas.php");
    exit;
}

// Excluir Tarefa
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM tabela_tarefas WHERE id = ?");
    $stmt->execute([$_GET['excluir']]);
    header("Location: tarefas.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM tabela_tarefas ORDER BY status DESC, id DESC");
$tarefas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Veículos - MiniMundo da Cloe 🛡️🩺</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">
</head>
<body>
    <div class="container">
        <header>
            <h1>⚡ MINIMUNDO DA CLOE ⚡</h1>
            <p>Ordens de Serviço do QG &bull; Sgt/Dev Lucas</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php">🏠 Home</a>
            <a href="itens.php">🍿 Lazer (Filmes/Livros/Rolês)</a>
            <a href="gastos.php">💰 Gestão de Gastos</a>
            <a href="tarefas.php" class="active">📋 Tarefas do Lucas</a>
            <a href="veiculos.php">🏍️🚗 Veículos (Tucson/Ténéré)</a>
                    <a href="revisao.php">🧠 Revisão Espaçada TI</a>
</div>

        <div class="card">
            <h3>➕ Nova Missão para o Lucas</h3>
            <form method="POST">
                <input type="hidden" name="action" value="cadastrar">
                <label>Descrição da Tarefa:</label>
                <input type="text" name="tarefa" placeholder="Ex: Ajustar infraestrutura da rede / Arrumar a garagem" required>
                <button type="submit">Cadastrar Missão</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h2>📋 Painel de Tarefas Operacionais</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 150px;">Status</th>
                        <th>Descrição da Tarefa</th>
                        <th style="width: 100px;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tarefas)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8;">Nenhuma missão cadastrada no momento.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tarefas as $t): ?>
                        <tr>
                            <td>
                                <a href="tarefas.php?id=<?= $t['id'] ?>&status=<?= $t['status'] ?>" style="text-decoration:none; font-weight: bold;">
                                    <?= $t['status'] == 'Feito' ? '✅ <span style="color:var(--accent);">Concluído</span>' : '⏳ <span style="color:#f59e0b;">Pendente</span>' ?>
                                </a>
                            </td>
                            <td style="<?= $t['status'] == 'Feito' ? 'text-decoration: line-through; color: #64748b;' : 'color: var(--text-color);' ?>">
                                <?= htmlspecialchars($t['tarefa']) ?>
                            </td>
                            <td>
                                <a href="tarefas.php?excluir=<?= $t['id'] ?>" onclick="return confirm('Deseja excluir esta tarefa?')" style="color:var(--danger); text-decoration:none;" class="btn-danger">🗑️ Excluir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>