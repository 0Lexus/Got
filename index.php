<?php
require_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GOT</title>
    <link rel="icon" type="image/jpeg" href="img/logoGot.jpg">
    <link rel="stylesheet" href="src/styles/style.css">
</head>
<body>
    <div class="content-login">
        <img src="img/logoGot1.png" alt="Logo GOT" class="login-logo">
        <h1 class="login-title">Acesse sua conta</h1>
        <form class="login-form" method="POST" action="modules/login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="seu@email.com" required>
            
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
            
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>