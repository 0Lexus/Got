<header class="topbar">
    <div class="topbar-left">
        <button class="btn-menu" id="btn-menu" aria-label="Menu">☰</button>
        <span class="topbar-title">GOT - Painel de Pesquisa</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-user">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></strong></span>
        <a href="../index.php" class="btn-logout">Sair</a>
    </div>
</header>