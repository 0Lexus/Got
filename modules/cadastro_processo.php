<?php
session_start();

// Proteção: só processa se estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        
        // 1. Verifica se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            header('Location: cadastro.php?erro=1');
            exit;
        }

        // 2. Criptografa a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // 3. Insere no banco
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmtInsert->execute([$nome, $email, $senhaHash]);
            
            header('Location: cadastro.php?sucesso=1');
            exit;
            
        } catch (PDOException $e) {
            header('Location: cadastro.php?erro=1');
            exit;
        }
    } else {
        header('Location: cadastro.php?erro=1');
        exit;
    }
} else {
    header('Location: cadastro.php');
    exit;
}
?>