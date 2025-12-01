<?php

include 'conexao.php';

try {
    // Consulta SQL que usa JOIN para buscar o nome da fazenda (tabela 'fazendas')
    $sql = "
        SELECT a.*, f.nome_fazenda 
        FROM animais a
        JOIN fazendas f ON a.id_fazenda = f.id_fazenda
        ORDER BY a.id_animal DESC
    ";
    $stmt = $pdo->query($sql);
    $animais = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao listar animais: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Animais</title>
    
</head>
<body>
    <h1> Gerenciamento de Animais</h1>
    <p><a href="animais_create.php"> Cadastrar Novo Animal</a></p>
    <p><a href="fazendas_index.php"> Gerenciar Fazendas</a></p>
    <hr>
    
    <?php if (isset($_GET['erro'])): ?>
        <?php echo htmlspecialchars($_GET['erro']); ?></p>
    <?php endif; ?>

    <?php if (count($animais) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Brinco</th>
                <th>Espécie</th>
                <th>Raça</th>
                <th>Nascimento</th>
                <th>Fazenda de Origem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($animais as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['brinco']); ?></td>
                <td><?php echo htmlspecialchars($a['especie']); ?></td>
                <td><?php echo htmlspecialchars($a['raca']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($a['data_nascimento'])); ?></td>
                <td><?php echo htmlspecialchars($a['nome_fazenda']); ?></td>
                <td>
                    <a href="animais_edit.php?id=<?php echo $a['id_animal']; ?>"> Editar</a> |
                    <a href="animais_delete.php?id=<?php echo $a['id_animal']; ?>" 
                       onclick="return confirm('Tem certeza que deseja deletar este animal?');"> Deletar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Nenhum animal cadastrado ainda.</p>
    <?php endif; ?>
</body>
</html>