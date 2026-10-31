<?php
// Tenta obter a URL de conexão do Render (Nuvem)
$dbUrl = getenv('DATABASE_URL');

if ($dbUrl) {
    // Configuração para o Render (Nuvem)
    $dbopts = parse_url($dbUrl);
    $host = $dbopts["host"];
    $port = $dbopts["port"] ?? 5432;
    $user = $dbopts["user"];
    $password = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"], '/');
} else {
    // Configuração Local (Seu computador)
    $host = 'localhost';
    $port = '5432';
    $dbname = 'minimundo';
    $user = 'postgres';
    $password = 'marinha123';
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
