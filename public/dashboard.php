<?php
session_start();
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
    <title>Dashboard - GOT</title>
    <link rel="icon" type="image/jpeg" href="../img/logoGot1.png">
    <link rel="stylesheet" href="../src/styles/style.css">
</head>
<body class="dashboard-body">

    <?php include '../src/includes/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include '../src/includes/topbar.php'; ?>

        <main class="content-dashboard">
            <div class="dashboard-header">
                <h1>Monitoramento</h1>
                <p class="dashboard-subtitle">Pesquisa ativa: <strong id="tema-pesquisa">Carregando...</strong></p>
            </div>

            <!-- Status do Bot -->
            <div class="card card-status">
                <div class="status-indicator">
                    <span class="status-dot"></span>
                    <span class="status-text">Bot Ativo</span>
                </div>
                <div class="status-info">
                    <small>Última atualização:</small>
                    <strong id="ultima-atualizacao">--:--</strong>
                </div>
                <div class="status-info">
                    <small>Próxima execução:</small>
                    <strong id="proxima-execucao">--:--</strong>
                </div>
            </div>

            <!-- Grid de Conteúdo -->
            <div class="dashboard-grid">
                
                <!-- Div de Notícias -->
                <div class="card card-noticias">
                    <h2 class="card-title">📰 Notícias</h2>
                    <div class="card-body" id="lista-noticias">
                        <p class="empty-state">Aguardando dados do bot...</p>
                    </div>
                </div>

                <!-- Div de Gráficos -->
                <div class="card card-graficos">
                    <h2 class="card-title">📊 Gráficos</h2>
                    <div class="card-body" id="area-graficos">
                        <p class="empty-state">Aguardando dados do bot...</p>
                    </div>
                </div>

                <!-- Div de Resumo/Insights -->
                <div class="card card-resumo">
                    <h2 class="card-title">💡 Resumo</h2>
                    <div class="card-body" id="resumo-conteudo">
                        <p class="empty-state">Aguardando dados do bot...</p>
                    </div>
                </div>

                <!-- Div de Fontes -->
                <div class="card card-fontes">
                    <h2 class="card-title">🔗 Fontes</h2>
                    <div class="card-body" id="lista-fontes">
                        <p class="empty-state">Aguardando dados do bot...</p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ============ JAVASCRIPT DE ATUALIZAÇÃO ============ -->
    <script>
        const INTERVALO_MS = 60000; // 60 segundos (1 minuto)

        async function carregarDados() {
            try {
                // Adiciona timestamp para evitar cache
                const response = await fetch('../src/api/get_dados.php?t=' + Date.now(), {
                    cache: 'no-store'
                });
                const dados = await response.json();

                if (dados.erro) {
                    console.error('Erro API:', dados.erro);
                    return;
                }

                // Atualizar header
                document.getElementById('tema-pesquisa').textContent = dados.tema || 'Nenhuma pesquisa';
                document.getElementById('ultima-atualizacao').textContent = dados.ultima_atualizacao || '--:--';
                document.getElementById('proxima-execucao').textContent = dados.proxima_execucao || '--:--';

                // Atualizar Notícias
                const divNoticias = document.getElementById('lista-noticias');
                if (dados.noticias && dados.noticias.length > 0) {
                    divNoticias.innerHTML = dados.noticias.map(n => `
                        <div style="padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <a href="${n.url_fonte}" target="_blank" style="color: #fff; text-decoration: none; font-weight: 600; display: block;">
                                ${n.titulo}
                            </a>
                            <div style="font-size: 12px; color: #aaa; margin-top: 4px;">
                                ${new Date(n.criado_em).toLocaleString('pt-BR')}
                            </div>
                        </div>
                    `).join('');
                } else {
                    divNoticias.innerHTML = '<p class="empty-state">Nenhuma notícia encontrada.</p>';
                }

                // Atualizar Gráfico
                const divGrafico = document.getElementById('area-graficos');
                if (dados.grafico && dados.grafico.labels && dados.grafico.labels.length > 0) {
                    const maxVal = Math.max(...dados.grafico.datasets[0].data);
                    divGrafico.innerHTML = `
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            ${dados.grafico.labels.map((label, i) => `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 40px; font-size: 12px; color: #ddd;">${label}</span>
                                    <div style="flex: 1; background: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; height: 30px;">
                                        <div style="width: ${(dados.grafico.datasets[0].data[i] / maxVal) * 100}%; background: #464444; height: 100%; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; color: #fff; font-size: 12px; font-weight: 600;">
                                            ${dados.grafico.datasets[0].data[i]}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    divGrafico.innerHTML = '<p class="empty-state">Aguardando dados para o gráfico...</p>';
                }

                // Atualizar Fontes
                const divFontes = document.getElementById('lista-fontes');
                if (dados.fontes && dados.fontes.length > 0) {
                    divFontes.innerHTML = dados.fontes.map(f => `
                        <div style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 12px;">
                            <a href="${f.url_fonte}" target="_blank" style="color: #ccc; text-decoration: none; word-break: break-all;">
                                ${f.url_fonte.replace(/^https?:\/\//, '').substring(0, 60)}${f.url_fonte.length > 60 ? '...' : ''}
                            </a>
                        </div>
                    `).join('');
                } else {
                    divFontes.innerHTML = '<p class="empty-state">Nenhuma fonte registrada.</p>';
                }

                console.log('✅ Dados atualizados:', new Date().toLocaleTimeString());

            } catch (error) {
                console.error('Erro ao carregar dados:', error);
            }
        }

        // Carregar ao abrir a página
        document.addEventListener('DOMContentLoaded', carregarDados);

        // Atualizar a cada 60 segundos
        setInterval(carregarDados, INTERVALO_MS);
    </script>

</body>
</html>