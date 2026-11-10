<?php
require_once 'db.php';

// Inserir Manutenção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO tabela_veiculos (veiculo, servico, quilometragem, data_servico, proxima_revisao_km) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['veiculo'], 
        $_POST['servico'], 
        $_POST['quilometragem'] ?: null, 
        $_POST['data_servico'], 
        $_POST['proxima_revisao_km'] ?: null
    ]);
    header("Location: veiculos.php");
    exit;
}

// Excluir Manutenção
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM tabela_veiculos WHERE id = ?");
    $stmt->execute([$_GET['excluir']]);
    header("Location: veiculos.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM tabela_veiculos ORDER BY data_servico DESC, id DESC");
$manutençoes = $stmt->fetchAll();
?>
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
            <p>Manutenção da Frota (Tucson & Ténéré 250)</p>
        </header>

        <div class="nav-tabs">
            <a href="index.php">🏠 Home</a>
            <a href="itens.php">🍿 Lazer (Filmes/Livros/Rolês)</a>
            <a href="gastos.php">💰 Gestão de Gastos</a>
            <a href="tarefas.php">📋 Tarefas do Lucas</a>
            <a href="veiculos.php" class="active">🏍️🚗 Veículos (Tucson/Ténéré)</a>
                    <a href="revisao.php">🧠 Revisão Espaçada TI</a>
</div>

        <div class="card">
            <h3>🔧 Registrar Serviço / Manutenção</h3>
            <form method="POST">
                <input type="hidden" name="action" value="cadastrar">
                
                <label>Veículo:</label>
                <select name="veiculo" required>
                    <option value="Tucson">🚗 Hyundai Tucson</option>
                    <option value="Tenere 250">🏍️ Yamaha Ténéré 250</option>
                </select>

                <label>Serviço Realizado / A Revisar:</label>
                <input type="text" name="servico" placeholder="Ex: Troca de óleo 10W40 + filtro / Kit Relação / Pastilhas de Freio" required>

                <label>Quilometragem (KM) no Serviço:</label>
                <input type="number" name="quilometragem" placeholder="Ex: 85000">

                <label>Próxima Revisão (KM):</label>
                <input type="number" name="proxima_revisao_km" placeholder="Ex: 90000">

                <label>Data da Manutenção:</label>
                <input type="date" name="data_servico" value="<?= date('Y-m-d') ?>" required>

                <button type="submit">Salvar Registro de Manutenção</button>
            </form>
        </div>

        <br>

        <div class="card">
            <h2>📜 Histórico Operacional da Frota</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Veículo</th>
                        <th>Serviço Realizado</th>
                        <th>KM Atual</th>
                        <th>Próxima Revisão (KM)</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($manutençoes)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8;">Nenhum registro de manutenção na frota.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($manutençoes as $v): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($v['data_servico'])) ?></td>
                            <td><strong><?= htmlspecialchars($v['veiculo']) ?></strong></td>
                            <td><?= htmlspecialchars($v['servico']) ?></td>
                            <td><?= $v['quilometragem'] ? number_format($v['quilometragem'], 0, ',', '.') . ' km' : '-' ?></td>
                            <td><?= $v['proxima_revisao_km'] ? '<strong style="color:var(--accent);">' . number_format($v['proxima_revisao_km'], 0, ',', '.') . ' km</strong>' : '-' ?></td>
                            <td>
                                <a href="veiculos.php?excluir=<?= $v['id'] ?>" onclick="return confirm('Deseja excluir este histórico de manutenção?')" style="color:var(--danger); text-decoration:none;" class="btn-danger">🗑️ Excluir</a>
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