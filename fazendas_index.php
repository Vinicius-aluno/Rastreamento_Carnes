<?php

include 'conexao.php'; // Inclui o arquivo de conexão

try {
    // Consulta SQL para selecionar todos os registros da tabela fazendas
    $stmt = $pdo->query("SELECT * FROM fazendas ORDER BY id_fazenda DESC");
    $fazendas = $stmt->fetchAll(); // Obtém todos os resultados em um array
} catch (PDOException $e) {
    die("Erro ao listar fazendas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Fazendas</title>
</head>
<body>
    <h1> Gerenciamento de Fazendas</h1>
    <p><a href="fazendas_create.php"> Cadastrar Nova Fazenda</a></p>
    <hr>
    
    <?php if (isset($_GET['erro'])): ?>
    <?php echo htmlspecialchars($_GET['erro']); ?></p>
    <?php endif; ?>

    <?php if (count($fazendas) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Localização</th>
                <th>Proprietário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fazendas as $f): ?>
            <tr>
                <td><?php echo htmlspecialchars($f['id_fazenda']); ?></td>
                <td><?php echo htmlspecialchars($f['nome_fazenda']); ?></td>
                <td><?php echo htmlspecialchars($f['localizacao']); ?></td>
                <td><?php echo htmlspecialchars($f['proprietario']); ?></td>
                <td>
                    <a href="fazendas_edit.php?id=<?php echo $f['id_fazenda']; ?>"> Editar</a> |
                    <a href="fazendas_delete.php?id=<?php echo $f['id_fazenda']; ?>" 
                       onclick="return confirm('Tem certeza que deseja deletar esta fazenda?');"> Deletar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Nenhuma fazenda cadastrada ainda.</p>
    <?php endif; ?>
</body>
</html>