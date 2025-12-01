<?php


include 'conexao.php';

// Obtém o ID da fazenda da URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("ID da fazenda não fornecido.");
}

// 1. Lógica para carregar os dados atuais da fazenda
try {
    $stmt = $pdo->prepare("SELECT * FROM fazendas WHERE id_fazenda = ?");
    $stmt->execute([$id]);
    $fazenda = $stmt->fetch(); // Obtém apenas um registro

    if (!$fazenda) {
        die("Fazenda não encontrada.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar fazenda: " . $e->getMessage());
}

// 2. Lógica para processar a atualização após a submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e limpa os novos dados
    $nome = trim($_POST['nome_fazenda']);
    $localizacao = trim($_POST['localizacao']);
    $proprietario = trim($_POST['proprietario']);

    if (empty($nome) || empty($localizacao)) {
        $erro = "Nome e Localização são obrigatórios.";
    } else {
        try {
            // Prepara a query SQL de UPDATE
            $sql = "UPDATE fazendas SET nome_fazenda = ?, localizacao = ?, proprietario = ? WHERE id_fazenda = ?";
            $stmt = $pdo->prepare($sql);
            
            // Executa a query, o último parâmetro é o ID para o WHERE
            $stmt->execute([$nome, $localizacao, $proprietario, $id]);

            // Sucesso: Redireciona para a lista
            header("Location: fazendas_index.php"); 
            exit();
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Fazenda</title>
</head>
<body>
    <h1>Editar Fazenda: <?php echo htmlspecialchars($fazenda['nome_fazenda']); ?></h1>
    <p><a href="fazendas_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nome_fazenda">Nome da Fazenda:</label><br>
        <input type="text" id="nome_fazenda" name="nome_fazenda" value="<?php echo htmlspecialchars($fazenda['nome_fazenda']); ?>" required><br><br>

        <label for="localizacao">Localização:</label><br>
        <input type="text" id="localizacao" name="localizacao" value="<?php echo htmlspecialchars($fazenda['localizacao']); ?>" required><br><br>

        <label for="proprietario">Proprietário (Opcional):</label><br>
        <input type="text" id="proprietario" name="proprietario" value="<?php echo htmlspecialchars($fazenda['proprietario']); ?>"><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>