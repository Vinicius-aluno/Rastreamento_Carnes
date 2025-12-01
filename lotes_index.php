<?php

include 'conexao.php';

try {
    // Consulta complexa para unir lotes, produtos e fornecedores
    $sql = "
        SELECT l.*, p.nome AS nome_produto, f.nome AS nome_fornecedor
        FROM lotes l
        JOIN produtos p ON l.produto_id = p.id_produto
        JOIN fornecedores f ON l.fornecedor_id = f.id_fornecedor
        ORDER BY l.id_lote DESC
    ";
    $stmt = $pdo->query($sql);
    $lotes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao listar lotes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Lotes de Produtos</title>
</head>
<body>
    <h1> Gerenciamento de Lotes de Produtos</h1>
    <p><a href="lotes_create.php"> Cadastrar Novo Lote</a></p>
    <p><a href="animais_index.php"> Gerenciar Animais</a> | <a href="fazendas_index.php"> Gerenciar Fazendas</a></p>
    <hr>

    <?php if (isset($_GET['erro'])): ?>
       <?php echo htmlspecialchars($_GET['erro']); ?></p>
    <?php endif; ?>

    <?php if (count($lotes) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Código Lote</th>
                <th>Produto</th>
                <th>Fornecedor</th>
                <th>Origem (Lote Abate)</th>
                <th>Peso (kg)</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lotes as $l): ?>
            <tr>
                <td><?php echo htmlspecialchars($l['codigo_lote']); ?></td>
                <td><?php echo htmlspecialchars($l['nome_produto']); ?></td>
                <td><?php echo htmlspecialchars($l['nome_fornecedor']); ?></td>
                <td><?php echo htmlspecialchars($l['origem']); ?></td>
                <td><?php echo htmlspecialchars($l['peso']); ?></td>
                <td>**<?php echo htmlspecialchars(ucfirst($l['circunstancia'])); ?>**</td>
                <td>
                    <a href="lotes_edit.php?id=<?php echo $l['id_lote']; ?>"> Editar</a> |
                    <a href="lotes_delete.php?id=<?php echo $l['id_lote']; ?>" 
                       onclick="return confirm('Tem certeza que deseja deletar este lote? Esta ação é irreversível e excluirá dados de rastreamento (temperatura, eventos).');"> Deletar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Nenhum lote de produto cadastrado ainda.</p>
    <?php endif; ?>
</body>
</html>