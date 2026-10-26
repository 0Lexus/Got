<?php
/**
 * Script para verificar status do bot
 * Uso: php scripts/verificar_bot.php
 */

require_once __DIR__ . '/../config/config.php';

echo "=== Status do Bot GOT ===\n\n";

// 1. Pesquisas ativas
$stmt = $pdo->query("
    SELECT p.id, p.tema, p.ultima_atualizacao, p.status, u.nome 
    FROM pesquisas p
    INNER JOIN usuarios u ON u.id = p.usuario_id
    WHERE p.status = 'ativo'
");
$pesquisas = $stmt->fetchAll();

echo "📋 Pesquisas Ativas: " . count($pesquisas) . "\n";
foreach ($pesquisas as $p) {
    echo "   - {$p['tema']} (Usuário: {$p['nome']})\n";
    echo "     Última atualização: " . ($p['ultima_atualizacao'] ?? 'Nunca') . "\n";
}

// 2. Total de dados coletados
$stmt = $pdo->query("
    SELECT tipo, COUNT(*) as total 
    FROM dados 
    GROUP BY tipo
");
$dados = $stmt->fetchAll();

echo "\n📊 Dados Coletados:\n";
foreach ($dados as $d) {
    echo "   - {$d['tipo']}: {$d['total']} registros\n";
}

// 3. Última execução
$stmt = $pdo->query("
    SELECT MAX(criado_em) as ultima 
    FROM dados
");
$ultima = $stmt->fetch();

echo "\n Última coleta de dados: " . ($ultima['ultima'] ?? 'Nunca') . "\n";

// 4. Verificar se está desatualizado
if ($ultima['ultima']) {
    $diff = time() - strtotime($ultima['ultima']);
    $minutos = floor($diff / 60);
    
    if ($minutos > 15) {
        echo "\n⚠️  ATENÇÃO: Bot pode estar parado! ({$minutos} minutos desde última coleta)\n";
    } else {
        echo "\n✅ Bot parece estar funcionando normalmente.\n";
    }
}