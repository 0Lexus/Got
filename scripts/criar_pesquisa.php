<?php
/**
 * Script para criar uma pesquisa de teste
 * Uso: php scripts/criar_pesquisa.php
 */

require_once __DIR__ . '/../config/config.php';

echo "=== Criar Nova Pesquisa ===\n\n";

// Listar usuários
$stmt = $pdo->query("SELECT id, nome, email FROM usuarios");
$usuarios = $stmt->fetchAll();

if (empty($usuarios)) {
    echo "❌ Nenhum usuário encontrado no banco.\n";
    exit(1);
}

echo "Usuários disponíveis:\n";
foreach ($usuarios as $u) {
    echo "  [{$u['id']}] {$u['nome']} ({$u['email']})\n";
}

echo "\nDigite o ID do usuário: ";
$usuarioId = trim(fgets(STDIN));

echo "Digite o tema da pesquisa (ex: Inteligência Artificial): ";
$tema = trim(fgets(STDIN));

if (empty($usuarioId) || empty($tema)) {
    echo "❌ Dados inválidos.\n";
    exit(1);
}

try {
    $stmt = $pdo->prepare("INSERT INTO pesquisas (usuario_id, tema, status) VALUES (?, ?, 'ativo')");
    $stmt->execute([$usuarioId, $tema]);
    
    echo "\n✅ Pesquisa criada com sucesso!\n";
    echo "   Usuário ID: {$usuarioId}\n";
    echo "   Tema: {$tema}\n";
    echo "   Status: Ativo\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}