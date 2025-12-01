<?php


include 'conexao.php';

// Verifica se a requisição foi feita via método POST (o formulário foi submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coleta e limpa os dados do formulário
    $nome = trim($_POST['nome_fazenda']);
    $localizacao = trim($_POST['localizacao']);
    $proprietario = trim($_POST['proprietario']);

    // 2. Validação mínima
    if (empty($nome) || empty($localizacao)) {
        $erro = "Nome e Localização são obrigatórios.";
    } else {
        try {
            // 3. Prepara a query SQL de INSERT
            $sql = "INSERT INTO fazendas (nome_fazenda, localizacao, proprietario) VALUES (?, ?, ?)";
            
            // Prepara a declaração para evitar SQL Injection (os '?' são placeholders)
            $stmt = $pdo->prepare($sql);
            
            // Executa a query, passando os valores como um array
            $stmt->execute([$nome, $localizacao, $proprietario]);

            // 4. Sucesso: Redireciona o usuário de volta para a lista
            header("Location: fazendas_index.php"); 
            exit();
        } catch (PDOException $e) {
            // 5. Erro: Captura e exibe qualquer erro do banco de dados (ex: campo NOT NULL)
            $erro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Fazenda</title>
</head>
<body>
    <h1> Cadastrar Nova Fazenda</h1>
    <p><a href="fazendas_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nome_fazenda">Nome da Fazenda:</label><br>
        <input type="text" id="nome_fazenda" name="nome_fazenda" required><br><br>

        <label for="localizacao">Localização:</label><br>
        <input type="text" id="localizacao" name="localizacao" required><br><br>

        <label for="proprietario">Proprietário (Opcional):</label><br>
        <input type="text" id="proprietario" name="proprietario"><br><br>

        <button type="submit">Salvar Fazenda</button>
    </form>
</body>
</html>