<?php

include 'conexao.php';

// Carrega dados auxiliares (Produtos, Fornecedores, Lotes de Abate)
try {
    $stmt_produtos = $pdo->query("SELECT id_produto, nome FROM produtos ORDER BY nome");
    $produtos = $stmt_produtos->fetchAll();

    $stmt_fornecedores = $pdo->query("SELECT id_fornecedor, nome FROM fornecedores ORDER BY nome");
    $fornecedores = $stmt_fornecedores->fetchAll();

    // Carrega códigos de lotes de abate (usados para o campo 'origem')
    $stmt_lotes_abate = $pdo->query("SELECT codigo_lote FROM lotes_abate ORDER BY data_abate DESC");
    $lotes_abate = $stmt_lotes_abate->fetchAll(PDO::FETCH_COLUMN, 0); // Busca apenas a coluna codigo_lote

} catch (PDOException $e) {
    die("Erro ao carregar dados auxiliares: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta dados
    $codigo_lote = trim($_POST['codigo_lote']);
    $produto_id = (int)$_POST['produto_id'];
    $fornecedor_id = (int)$_POST['fornecedor_id'];
    $origem = trim($_POST['origem']);
    $destino = trim($_POST['destino']);
    $data_fabricacao = $_POST['data_fabricacao'];
    $data_validade = $_POST['data_validade'];
    $peso = (float)$_POST['peso'];
    $circunstancia = $_POST['circunstancia']; // Status inicial
    $id_usuario = 2; // Exemplo: ID de um usuário genérico (Funcionário)

    if (empty($codigo_lote) || empty($origem) || $produto_id == 0) {
        $erro = "Campos essenciais são obrigatórios.";
    } else {
        try {
            $pdo->beginTransaction(); // Inicia uma transação: todas as queries devem ter sucesso

            // 1. INSERT na tabela 'lotes'
            $sql = "INSERT INTO lotes (codigo_lote, produto_id, fornecedor_id, origem, destino, data_fabricacao, data_validade, peso, circunstancia) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codigo_lote, $produto_id, $fornecedor_id, $origem, $destino, $data_fabricacao, $data_validade, $peso, $circunstancia]);

            $id_lote = $pdo->lastInsertId(); // Pega o ID do lote recém-criado

            // 2. INSERT na tabela 'produtos_nos_lotes' (ligação M:N, simplificada aqui)
            $pdo->prepare("INSERT INTO produtos_nos_lotes (id_produto, id_lote, quantidade) VALUES (?, ?, 1)")
                ->execute([$produto_id, $id_lote]);

            // 3. INSERT no histórico (rastreabilidade do status)
            $stmt_status = $pdo->prepare("SELECT id_status FROM status_lote WHERE nome_status = ?");
            $stmt_status->execute([$circunstancia]);
            $id_status = $stmt_status->fetchColumn();

            if ($id_status) {
                $pdo->prepare("INSERT INTO lote_status_historico (id_lote, id_status, id_usuario) VALUES (?, ?, ?)")
                    ->execute([$id_lote, $id_status, $id_usuario]);
            }
            
            $pdo->commit(); // Confirma todas as operações da transação

            header("Location: lotes_index.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack(); // Desfaz todas as operações se algo falhou
            $erro = "Erro ao cadastrar lote: O código do lote pode já existir. Detalhe: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Lote de Produto</title>
</head>
<body>
    <h1> Cadastrar Novo Lote</h1>
    <p><a href="lotes_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="codigo_lote">Código do Lote (UNIQUE):</label><br>
        <input type="text" id="codigo_lote" name="codigo_lote" required><br><br>

        <label for="produto_id">Produto (FK):</label><br>
        <select id="produto_id" name="produto_id" required>
            <?php foreach ($produtos as $p): ?>
                <option value="<?php echo $p['id_produto']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="fornecedor_id">Frigorífico (Fornecedor - FK):</label><br>
        <select id="fornecedor_id" name="fornecedor_id" required>
            <?php foreach ($fornecedores as $f): ?>
                <option value="<?php echo $f['id_fornecedor']; ?>"><?php echo htmlspecialchars($f['nome']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="origem">Origem (Código do Lote de Abate):</label><br>
        <select id="origem" name="origem" required>
            <?php foreach ($lotes_abate as $la): ?>
                <option value="<?php echo $la; ?>"><?php echo htmlspecialchars($la); ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="destino">Destino Inicial:</label><br>
        <input type="text" id="destino" name="destino" required><br><br>

        <label for="data_fabricacao">Data de Fabricação:</label><br>
        <input type="date" id="data_fabricacao" name="data_fabricacao"><br><br>
        
        <label for="data_validade">Data de Validade:</label><br>
        <input type="date" id="data_validade" name="data_validade"><br><br>
        
        <label for="peso">Peso (kg):</label><br>
        <input type="number" step="0.01" id="peso" name="peso"><br><br>

        <label for="circunstancia">Status Inicial (ENUM):</label><br>
        <select id="circunstancia" name="circunstancia" required>
            <option value="preparado">Preparado</option>
            <option value="em_transito">Em Trânsito</option>
            <option value="entregue">Entregue</option>
            <option value="armazenado">Armazenado</option>
        </select><br><br>

        <button type="submit">Salvar Lote</button>
    </form>
</body>
</html>