<?php
require_once __DIR__ . '/../config/config.php';

// Configurações
define('INTERVALO_MINUTOS', 10);
define('MAX_NOTICIAS_POR_PESQUISA', 10); // Limite por execução

echo "[" . date('Y-m-d H:i:s') . "] 🤖 Bot iniciado...\n";

try {
    // 1. Buscar pesquisas ativas
    $stmt = $pdo->query("
        SELECT p.*, u.nome as usuario_nome 
        FROM pesquisas p
        INNER JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.status = 'ativo'
    ");
    $pesquisas = $stmt->fetchAll();

    if (empty($pesquisas)) {
        echo "⚠️  Nenhuma pesquisa ativa encontrada.\n";
        exit(0);
    }

    echo "📋 Pesquisas ativas: " . count($pesquisas) . "\n";

    foreach ($pesquisas as $pesquisa) {
        echo "\n🔍 Processando: \"{$pesquisa['tema']}\" (Usuário: {$pesquisa['usuario_nome']})\n";
        
        // 2. Montar URL do Google News RSS
        $query = urlencode($pesquisa['tema']);
        $rssUrl = "https://news.google.com/rss/search?q={$query}&hl=pt-BR&gl=BR&ceid=BR:pt-419";
        
        // 3. Buscar o RSS
        $xml = @simplexml_load_file($rssUrl, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if (!$xml || !isset($xml->channel->item)) {
            echo "❌ Erro ao acessar Google News para: {$pesquisa['tema']}\n";
            continue;
        }

        $items = $xml->channel->item;
        $count = 0;

        // 4. Processar cada notícia
        foreach ($items as $item) {
            if ($count >= MAX_NOTICIAS_POR_PESQUISA) break;

            $titulo = trim((string) $item->title);
            $link = trim((string) $item->link);
            $pubDate = (string) $item->pubDate;
            $source = isset($item->source) ? (string) $item->source : 'Google News';
            
            // Evitar duplicatas (mesmo título na mesma pesquisa)
            $stmtCheck = $pdo->prepare("
                SELECT id FROM dados 
                WHERE pesquisa_id = ? AND tipo = 'noticia' AND titulo = ?
            ");
            $stmtCheck->execute([$pesquisa['id'], $titulo]);
            
            if ($stmtCheck->fetch()) {
                echo "   ⏭️  Já existe: {$titulo}\n";
                continue;
            }

            // Inserir notícia
            $stmtInsert = $pdo->prepare("
                INSERT INTO dados (pesquisa_id, tipo, titulo, conteudo, url_fonte) 
                VALUES (?, 'noticia', ?, ?, ?)
            ");
            $stmtInsert->execute([
                $pesquisa['id'],
                $titulo,
                $titulo, // conteúdo = título (pode ser expandido com descrição)
                $link
            ]);

            echo "   ✅ Nova notícia: {$titulo}\n";
            $count++;
        }

        // 5. Gerar dados para gráfico (notícias por dia nas últimas 24h)
        gerarGrafico($pdo, $pesquisa['id']);

        // 6. Atualizar timestamp da pesquisa
        $stmtUpdate = $pdo->prepare("UPDATE pesquisas SET ultima_atualizacao = NOW() WHERE id = ?");
        $stmtUpdate->execute([$pesquisa['id']]);
    }

    echo "\n✅ Bot finalizado com sucesso.\n";

} catch (Exception $e) {
    echo "❌ Erro fatal: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Gera dados de gráfico: quantidade de notícias por dia (últimos 7 dias)
 */
function gerarGrafico($pdo, $pesquisaId) {
    // Buscar notícias dos últimos 7 dias
    $stmt = $pdo->prepare("
        SELECT DATE(criado_em) as dia, COUNT(*) as total
        FROM dados
        WHERE pesquisa_id = ? AND tipo = 'noticia'
        AND criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(criado_em)
        ORDER BY dia ASC
    ");
    $stmt->execute([$pesquisaId]);
    $resultados = $stmt->fetchAll();

    // Montar JSON para o gráfico
    $dadosGrafico = [
        'labels' => [],
        'datasets' => [
            [
                'label' => 'Notícias por dia',
                'data' => []
            ]
        ]
    ];

    foreach ($resultados as $r) {
        $dadosGrafico['labels'][] = date('d/m', strtotime($r['dia']));
        $dadosGrafico['datasets'][0]['data'][] = (int) $r['total'];
    }

    $jsonGrafico = json_encode($dadosGrafico, JSON_UNESCAPED_UNICODE);

    // Salvar/Atualizar no banco como tipo 'grafico'
    $stmtUpsert = $pdo->prepare("
        INSERT INTO dados (pesquisa_id, tipo, titulo, conteudo) 
        VALUES (?, 'grafico', 'Evolução de Notícias', ?)
        ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo), criado_em = NOW()
    ");
    $stmtUpsert->execute([$pesquisaId, $jsonGrafico]);

    echo "   📊 Gráfico atualizado com " . count($resultados) . " dias de dados.\n";
}