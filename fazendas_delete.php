<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("ID da fazenda não fornecido.");
}

try {
    // 1. VERIFICAÇÃO DE CHAVE ESTRANGEIRA (Foreign Key Check)
    // Antes de deletar a fazenda, verifica se existem animais na tabela 'animais' usando este ID.
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM animais WHERE id_fazenda = ?");
    $stmt_check->execute([$id]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        $erro = "Não é possível excluir esta fazenda porque ela possui **{$count}** animais vinculados. Exclua os animais primeiro.";
        // Redireciona de volta para a lista com a mensagem de erro na URL
        header("Location: fazendas_index.php?erro=" . urlencode($erro));
        exit();
    }

    // 2. EXECUÇÃO DO DELETE
    $sql = "DELETE FROM fazendas WHERE id_fazenda = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    // Sucesso: Redireciona para a lista
    header("Location: fazendas_index.php");
    exit();
} catch (PDOException $e) {
    // Se ocorrer outro erro de banco de dados (ex: outra FK não verificada), exibe.
    $erro = "Erro ao deletar: " . $e->getMessage();
    header("Location: fazendas_index.php?erro=" . urlencode($erro));
    exit();
}
?>