<?php
$host = "localhost"; // Caso rode dentro do Docker ou precise, use 172.17.0.1
$port = "5432";
$dbname = "minimundo"; // Nome do banco criado no seu pgAdmin
$user = "postgres";
$password = "marinha123";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

