<?php
// includes/header.php - cabeçalho e menu
require_once __DIR__ . '/../lib/bootstrap.php';
$pagina_atual = basename($_SERVER['PHP_SELF']);
$pendentes_menu = count(revisoes_pendentes());
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= escapar(NOME_APP) ?> - Revisão Espaçada TI</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand">🧠 <?= escapar(NOME_APP) ?></div>
  <nav>
    <a href="/index.php" class="<?= $pagina_atual==='index.php'?'active':'' ?>">Painel</a>
    <a href="/subjects.php" class="<?= $pagina_atual==='subjects.php'?'active':'' ?>">Assuntos</a>
    <a href="/reviews.php" class="<?= $pagina_atual==='reviews.php'?'active':'' ?>">Revisões<?= $pendentes_menu>0 ? ' <span class="badge badge-overdue">'.$pendentes_menu.'</span>' : '' ?></a>
    <a href="/import.php" class="<?= $pagina_atual==='import.php'?'active':'' ?>">Importar</a>
    <a href="/settings.php" class="<?= $pagina_atual==='settings.php'?'active':'' ?>">Configurações</a>
  </nav>
</header>
<main class="container">
<?php if ($mensagem_flash): ?>
<div class="flash flash-<?= escapar($mensagem_flash['tipo']) ?>"><?= escapar($mensagem_flash['msg']) ?></div>
<?php endif; ?>
