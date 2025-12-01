<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("ID do lote não fornecido.");
}

try {
    $pdo->beginTransaction();

    $id_lote = $id;

    // 1. EXCLUSÃO DE DADOS DEPENDENTES (Chaves Estrangeiras)
    // Deleta registros em todas as tabelas que dependem de 'lotes'
    
    // Ligações (muitos para muitos) e histórico de rastreamento
    $pdo->prepare("DELETE FROM comprador_tem_lotes WHERE id_lote = ?")->execute([$id_lote]);
    $pdo->prepare("DELETE FROM fornecedores_tem_lotes WHERE id_lote = ?")->execute([$id_lote]);
    $pdo->prepare("DELETE FROM lotes_veiculos WHERE id_lote = ?")->execute([$id_lote]);
    $pdo->prepare("DELETE FROM produtos_nos_lotes WHERE id_lote = ?")->execute([$id_lote]);
    
    // Dados de Rastreamento (Temperaturas, Eventos e Histórico de Status)
    $pdo->prepare("DELETE FROM temperaturas WHERE id_lote = ?")->execute([$id_lote]);
    $pdo->prepare("DELETE FROM eventos WHERE lote_id = ?")->execute([$id_lote]);
    $pdo->prepare("DELETE FROM lote_status_historico WHERE id_lote = ?")->execute([$id_lote]);
    
    // 2. EXCLUSÃO DO REGISTRO PRINCIPAL
    $sql = "DELETE FROM lotes WHERE id_lote = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_lote]);

    $pdo->commit(); // Confirma a exclusão de todos os dados

    header("Location: lotes_index.php"); 
    exit();
} catch (PDOException $e) {
    $pdo->rollBack(); // Desfaz tudo se alguma exclusão falhou
    $erro = "Erro ao deletar o lote e seus registros associados: " . $e->getMessage();
    header("Location: lotes_index.php?erro=" . urlencode($erro));
    exit();
}
?>