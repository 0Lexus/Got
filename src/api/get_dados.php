<?php
/**
 * API - Retorna dados do dashboard para uma pesquisa ativa
 * Uso: get_dados.php?usuario_id=X
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/config.php';

session_start();

// Validação de sessão
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

try {
    // 1. Buscar pesquisa ativa do usuário
    $stmt = $pdo->prepare("
        SELECT id, tema, ultima_atualizacao, status 
        FROM pesquisas 
        WHERE usuario_id = ? AND status = 'ativo'
        ORDER BY ultima_atualizacao DESC 
        LIMIT 1
    ");
    $stmt->execute([$usuarioId]);
    $pesquisa = $stmt->fetch();

    if (!$pesquisa) {
        echo json_encode([
            'tema' => 'Nenhuma pesquisa ativa',
            'ultima_atualizacao' => null,
            'noticias' => [],
            'grafico' => null,
            'resumo' => null,
            'fontes' => []
        ]);
        exit;
    }

    $pesquisaId = $pesquisa['id'];

    // 2. Buscar notícias (últimas 10)
    $stmtNoticias = $pdo->prepare("
        SELECT titulo, url_fonte, criado_em 
        FROM dados 
        WHERE pesquisa_id = ? AND tipo = 'noticia'
        ORDER BY criado_em DESC 
        LIMIT 10
    ");
    $stmtNoticias->execute([$pesquisaId]);
    $noticias = $stmtNoticias->fetchAll();

    // 3. Buscar dados do gráfico
    $stmtGrafico = $pdo->prepare("
        SELECT conteudo FROM dados 
        WHERE pesquisa_id = ? AND tipo = 'grafico'
        ORDER BY criado_em DESC 
        LIMIT 1
    ");
    $stmtGrafico->execute([$pesquisaId]);
    $graficoRow = $stmtGrafico->fetch();
    $grafico = $graficoRow ? json_decode($graficoRow['conteudo'], true) : null;

    // 4. Buscar fontes únicas
    $stmtFontes = $pdo->prepare("
        SELECT DISTINCT url_fonte 
        FROM dados 
        WHERE pesquisa_id = ? AND tipo = 'noticia' AND url_fonte IS NOT NULL
        LIMIT 10
    ");
    $stmtFontes->execute([$pesquisaId]);
    $fontes = $stmtFontes->fetchAll();

    // 5. Montar resposta
    echo json_encode([
        'tema' => $pesquisa['tema'],
        'ultima_atualizacao' => date('H:i', strtotime($pesquisa['ultima_atualizacao'])),
        'proxima_execucao' => date('H:i', strtotime("+10 minutes")),
        'noticias' => $noticias,
        'grafico' => $grafico,
        'resumo' => null, // Futuro: IA gera resumo
        'fontes' => $fontes
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}