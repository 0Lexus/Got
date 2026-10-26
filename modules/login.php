<?php
// login.php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        // Busca o usuário pelo email
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Verifica se o usuário existe e se a senha bate com o hash
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Cria a sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            
            // Redireciona para o dashboard
            header('Location: ../public/dashboard.php');
            exit;
        } else {
            // Senha ou email incorretos
            echo "<script>alert('Senha errada, otário.');</script>";
            header('Location: ../index.php?erro=1');
            exit;
        }
    } else {
        header('Location: ../index.php?erro=1');
        exit;
    }
} else {
    // Se tentar acessar o login.php direto pelo navegador, volta para o index
    header('Location: ../index.php');
    exit;
}
?>