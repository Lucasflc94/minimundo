<?php
require_once 'db.php';

// Inserir Gasto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO tabela_gastos (quem, descricao, valor, mes_referencia, data_gasto) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['quem'], $_POST['descricao'], $_POST['valor'], $_POST['mes_referencia'], $_POST['data_gasto']]);
    header("Location: gastos.php?mes=" . $_POST['mes_referencia']);
    exit;
}

// Excluir Gasto
if (isset($_GET['excluir'])) {
    $mes_atual = $_GET['mes'] ?? date('Y-m');
    $stmt = $pdo->prepare("DELETE FROM tabela_gastos WHERE id = ?");
    $stmt->execute([$_GET['excluir']]);
    header("Location: gastos.php?mes=" . $mes_atual);
    exit;
}

// Filtro de Mês (padrão: mês atual)
$mes_filtro = $_GET['mes'] ?? date('Y-m');

$stmt = $pdo->prepare("SELECT * FROM tabela_gastos WHERE mes_referencia = ? ORDER BY data_gasto DESC, id DESC");
$stmt->execute([$mes_filtro]);
$gastos = $stmt->fetchAll();

// Totais
$totais = ['Lucas' => 0, 'Bruna' => 0];
foreach ($gastos as $g) {
    $totais[$g['quem']] += $g['valor'];
}
$total_casal = $totais['Lucas'] + $totais['Bruna'];
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
            <p>Controle Financeiro Operacional &bull; Drª. Bruna & Sgt/Dev Lucas</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php">🏠 Home</a>
            <a href="itens.php">🍿 Lazer (Filmes/Livros/Rolês)</a>
            <a href="gastos.php" class="active">💰 Gestão de Gastos</a>
            <a href="tarefas.php">📋 Tarefas do Lucas</a>
            <a href="veiculos.php">🏍️🚗 Veículos (Tucson/Ténéré)</a>
        </div>

        <div class="card">
            <h3>💳 Cadastrar Novo Gasto</h3>
            <form method="POST">
                <input type="hidden" name="action" value="cadastrar">
                
                <label>Responsável:</label>
                <select name="quem" required>
                    <option value="Lucas">Sgt/Dev Lucas 🛡️💻</option>
                    <option value="Bruna">Drª. Bruna 🩺</option>
                </select>

                <label>Descrição:</label>
                <input type="text" name="descricao" placeholder="Ex: Mercado, Combustível, Insumos" required>

                <label>Valor (R$):</label>
                <input type="number" step="0.01" name="valor" placeholder="0.00" required>

                <label>Mês de Referência (AAAA-MM):</label>
                <input type="month" name="mes_referencia" value="<?= htmlspecialchars($mes_filtro) ?>" required>

                <label>Data do Gasto:</label>
                <input type="date" name="data_gasto" value="<?= date('Y-m-d') ?>" required>

                <button type="submit">Lançar Gasto</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h2>🔎 Filtrar por Mês</h2>
            <form method="GET" style="display:flex; gap:10px; align-items: flex-end;">
                <div style="flex: 1; margin: 0;">
                    <label>Selecionar Mês:</label>
                    <input type="month" name="mes" value="<?= htmlspecialchars($mes_filtro) ?>" required style="margin:0;">
                </div>
                <button type="submit" style="width:auto; margin:0; height: 42px;">Filtrar Período</button>
            </form>

            <div style="display: flex; justify-content: space-around; margin-top: 25px; background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="text-align: center;">
                    <span style="font-size: 0.9rem; color: #94a3b8;">Lucas 🛡️</span>
                    <p style="margin: 5px 0 0 0; font-size: 1.2rem; color: var(--primary);"><strong>R$ <?= number_format($totais['Lucas'], 2, ',', '.') ?></strong></p>
                </div>
                <div style="text-align: center;">
                    <span style="font-size: 0.9rem; color: #94a3b8;">Bruna 🩺</span>
                    <p style="margin: 5px 0 0 0; font-size: 1.2rem; color: var(--accent);"><strong>R$ <?= number_format($totais['Bruna'], 2, ',', '.') ?></strong></p>
                </div>
                <div style="text-align: center;">
                    <span style="font-size: 0.9rem; color: #94a3b8;">Total do Casal</span>
                    <p style="margin: 5px 0 0 0; font-size: 1.2rem; color: #f59e0b;"><strong>R$ <?= number_format($total_casal, 2, ',', '.') ?></strong></p>
                </div>
            </div>

            <h3 style="margin-top: 25px;">Extrato de Gastos (<?= htmlspecialchars($mes_filtro) ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Quem</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($gastos)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8;">Nenhum gasto registrado para este mês.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($gasto['data_gasto'])) ?></td>
                            <td><strong><?= htmlspecialchars($gasto['quem']) ?></strong></td>
                            <td><?= htmlspecialchars($gasto['descricao']) ?></td>
                            <td>R$ <?= number_format($gasto['valor'], 2, ',', '.') ?></td>
                            <td>
                                <a href="gastos.php?excluir=<?= $gasto['id'] ?>&mes=<?= $mes_filtro ?>" onclick="return confirm('Deseja excluir este lançamento?')" style="color:var(--danger); text-decoration:none;" class="btn-danger">🗑️ Excluir</a>
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