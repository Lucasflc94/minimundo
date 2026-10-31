<?php
require_once 'db.php';

// Inserir Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $stmt = $pdo->prepare("INSERT INTO tabela_itens (categoria, titulo, status, nota, observacao) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['categoria'], $_POST['titulo'], $_POST['status'], $_POST['nota'] ?: null, $_POST['observacao']]);
    header("Location: itens.php?cat=" . $_POST['categoria']);
    exit;
}

// Excluir Item
if (isset($_GET['excluir'])) {
    $cat_retorno = $_GET['cat'] ?? 'filme';
    $stmt = $pdo->prepare("DELETE FROM tabela_itens WHERE id = ?");
    $stmt->execute([$_GET['excluir']]);
    header("Location: itens.php?cat=" . $cat_retorno);
    exit;
}

$categoria_atual = $_GET['cat'] ?? 'filme';
$stmt = $pdo->prepare("SELECT * FROM tabela_itens WHERE categoria = ? ORDER BY id DESC");
$stmt->execute([$categoria_atual]);
$itens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lazer - MiniMundo da Cloe 🛡️🩺</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">
</head>
<body>
    <div class="container">
        <header>
            <h1>⚡ MINIMUNDO DA CLOE ⚡</h1>
            <p>Painel de Lazer & Entretenimento &bull; Drª. Bruna & Sgt/Dev Lucas</p>
        </header>

        <!-- Menu Principal (Rodapé fixo no celular) -->
        <div class="nav-tabs">
            <a href="index.php">🏠 Home</a>
            <a href="itens.php" class="active">🍿 Lazer</a>
            <a href="gastos.php">💰 Gastos</a>
            <a href="tarefas.php">📋 Tarefas</a>
            <a href="veiculos.php">🏍️🚗 Veículos</a>
        </div>

        <div class="card">
            <h3>➕ Cadastrar Novo Item</h3>
            <form method="POST">
                <input type="hidden" name="action" value="cadastrar">
                <label>Categoria:</label>
                <select name="categoria">
                    <option value="filme" <?= $categoria_atual == 'filme' ? 'selected' : '' ?>>🎬 Filme / Série</option>
                    <option value="livro" <?= $categoria_atual == 'livro' ? 'selected' : '' ?>>📚 Livro</option>
                    <option value="lugar" <?= $categoria_atual == 'lugar' ? 'selected' : '' ?>>📍 Lugar para Ir</option>
                    <option value="comida" <?= $categoria_atual == 'comida' ? 'selected' : '' ?>>🍕 Comida / Restaurante</option>
                </select>

                <label>Título / Nome:</label>
                <input type="text" name="titulo" required placeholder="Ex: Interstellar ou Outback">

                <label>Status:</label>
                <select name="status">
                    <option value="Pendente">Pendente / Quero Ir</option>
                    <option value="Concluído">Concluído / Visitado</option>
                    <option value="Favorito">⭐ Favorito</option>
                </select>

                <label>Nota (1 a 5):</label>
                <input type="number" name="nota" min="1" max="5" placeholder="De 1 a 5">

                <label>Observação:</label>
                <input type="text" name="observacao" placeholder="Ex: Pedir a costela com molho barbecue ou indicação">

                <button type="submit">Salvar Registro</button>
            </form>
        </div>

        <br>

        <!-- Sub-abas de Categorias (Rolagem lateral no celular) -->
        <div class="sub-tabs">
            <a href="itens.php?cat=filme" <?= $categoria_atual == 'filme' ? 'class="active"' : '' ?>>🎬 Filmes</a>
            <a href="itens.php?cat=livro" <?= $categoria_atual == 'livro' ? 'class="active"' : '' ?>>📚 Livros</a>
            <a href="itens.php?cat=lugar" <?= $categoria_atual == 'lugar' ? 'class="active"' : '' ?>>📍 Lugares</a>
            <a href="itens.php?cat=comida" <?= $categoria_atual == 'comida' ? 'class="active"' : '' ?>>🍕 Comidas</a>
        </div>

        <div class="card">
            <h2>Listagem: <?= ucfirst($categoria_atual) ?>s</h2>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Nota</th>
                        <th>Observação</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($itens)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8;">Nenhum registro encontrado nesta categoria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item['titulo']) ?></strong></td>
                            <td><?= htmlspecialchars($item['status']) ?></td>
                            <td><?= $item['nota'] ? str_repeat('⭐', $item['nota']) : '-' ?></td>
                            <td><?= htmlspecialchars($item['observacao']) ?></td>
                            <td>
                                <a href="itens.php?excluir=<?= $item['id'] ?>&cat=<?= $categoria_atual ?>" onclick="return confirm('Deseja realmente excluir este item?')" style="color:var(--danger); text-decoration:none;" class="btn-danger">🗑️ Excluir</a>
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