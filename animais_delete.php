<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("ID do animal não fornecido.");
}

try {
    // 1. VERIFICAÇÃO DE CHAVE ESTRANGEIRA (Lotes de Abate)
    // Se o animal já foi para o abate, ele não pode ser deletado.
    $stmt_check_lote = $pdo->prepare("SELECT COUNT(*) FROM animal_no_lote_abate WHERE id_animal = ?");
    $stmt_check_lote->execute([$id]);
    $count = $stmt_check_lote->fetchColumn();

    if ($count > 0) {
        $erro = "Não é possível excluir este animal, pois ele já está vinculado a um ou mais lotes de abate. Exclua a ligação primeiro.";
        header("Location: animais_index.php?erro=" . urlencode($erro));
        exit();
    }
    
    // 2. EXCLUSÃO DE DADOS DEPENDENTES (Transporte)
    // A tabela 'transportes_animais' depende de 'animais'. Se o animal for deletado, o transporte deve ser deletado primeiro.
    $pdo->prepare("DELETE FROM transportes_animais WHERE id_animal = ?")->execute([$id]);

    // 3. EXECUÇÃO DO DELETE
    $sql = "DELETE FROM animais WHERE id_animal = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: animais_index.php"); 
    exit();
} catch (PDOException $e) {
    $erro = "Erro ao deletar: " . $e->getMessage();
    header("Location: animais_index.php?erro=" . urlencode($erro));
    exit();
}
?>