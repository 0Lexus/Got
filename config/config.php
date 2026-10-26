<?php
// config/config.php

$host = 'localhost';
$db   = 'got'; // Nome do seu banco de dados
$user = 'root';        // Seu usuário do banco
$pass = '';            // Sua senha do banco
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Em produção, não exiba o erro real para o usuário
    die("Erro na conexão com o banco de dados."); 
}
?>