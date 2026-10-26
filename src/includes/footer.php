<footer class="footer">
    <p>&copy; <?= date('Y'); ?> GOT - Todos os direitos reservados</p>
</footer>

<script>
    // Toggle do menu mobile
    document.getElementById('btn-menu')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
    });

    // Futuramente: script de atualização em tempo real via API
    // setInterval(buscarDados, 600000); // 10 minutos
</script>