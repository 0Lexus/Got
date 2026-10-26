<?php
session_start();
// Proteção: só acessa quem está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - GOT</title>
    <link rel="stylesheet" href="../src/styles/style.css">
</head>
<body class="dashboard-body">

    <?php include '../src/includes/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include '../src/includes/topbar.php'; ?>

        <main class="content-dashboard">
            <div class="dashboard-header">
                <h1>Cadastrar Novo Usuário</h1>
                <p class="dashboard-subtitle">Crie credenciais de acesso para a equipe</p>
            </div>

            <!-- Mensagens de Feedback -->
            <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
                <div class="alert-error">Email já cadastrado ou erro no processo.</div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                <div class="alert-success">Usuário cadastrado com sucesso!</div>
            <?php endif; ?>

            <!-- Card do Formulário -->
            <div class="card card-formulario">
                <form class="form-cadastro" method="POST" action="cadastro_processo.php">
                    
                    <div class="form-group">
                        <label for="nome">Nome de Usuário</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: João Silva" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="email@empresa.com" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    </div>

                    <button type="submit" class="btn-submit">Cadastrar Usuário</button>
                </form>
            </div>

        </main>
    </div>
</body>
</html>