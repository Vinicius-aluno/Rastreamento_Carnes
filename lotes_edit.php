<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    die("ID do lote não fornecido.");
}

// 1. Carrega dados auxiliares e o lote atual
try {
    $stmt_produtos = $pdo->query("SELECT id_produto, nome FROM produtos ORDER BY nome");
    $produtos = $stmt_produtos->fetchAll();

    $stmt_fornecedores = $pdo->query("SELECT id_fornecedor, nome FROM fornecedores ORDER BY nome");
    $fornecedores = $stmt_fornecedores->fetchAll();

    $stmt_lotes_abate = $pdo->query("SELECT codigo_lote FROM lotes_abate ORDER BY data_abate DESC");
    $lotes_abate = $stmt_lotes_abate->fetchAll(PDO::FETCH_COLUMN, 0);

    $stmt = $pdo->prepare("SELECT * FROM lotes WHERE id_lote = ?");
    $stmt->execute([$id]);
    $lote = $stmt->fetch();

    if (!$lote) {
        die("Lote não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// 2. Lógica de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta novos dados
    $codigo_lote_novo = trim($_POST['codigo_lote']);
    $produto_id = (int)$_POST['produto_id'];
    $fornecedor_id = (int)$_POST['fornecedor_id'];
    $origem = trim($_POST['origem']);
    $destino = trim($_POST['destino']);
    $data_fabricacao = $_POST['data_fabricacao'];
    $data_validade = $_POST['data_validade'];
    $peso = (float)$_POST['peso'];
    $circunstancia_nova = $_POST['circunstancia'];
    
    $id_usuario = 2; 

    if (empty($codigo_lote_novo) || empty($origem) || $produto_id == 0) {
        $erro = "Campos obrigatórios faltando.";
    } else {
        try {
            $pdo->beginTransaction();

            // 2.1. UPDATE na tabela 'lotes'
            $sql = "UPDATE lotes SET codigo_lote=?, produto_id=?, fornecedor_id=?, origem=?, destino=?, data_fabricacao=?, data_validade=?, peso=?, circunstancia=? WHERE id_lote=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codigo_lote_novo, $produto_id, $fornecedor_id, $origem, $destino, $data_fabricacao, $data_validade, $peso, $circunstancia_nova, $id]);

            // 2.2. Verifica e registra a mudança de status
            if ($lote['circunstancia'] != $circunstancia_nova) {
                // Se o status mudou, busca o ID do novo status
                $stmt_status = $pdo->prepare("SELECT id_status FROM status_lote WHERE nome_status = ?");
                $stmt_status->execute([$circunstancia_nova]);
                $id_status = $stmt_status->fetchColumn();

                if ($id_status) {
                    // Insere um novo registro na tabela de histórico
                    $pdo->prepare("INSERT INTO lote_status_historico (id_lote, id_status, id_usuario) VALUES (?, ?, ?)")
                        ->execute([$id, $id_status, $id_usuario]);
                }
            }
            
            $pdo->commit();

            header("Location: lotes_index.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao atualizar lote: O código do lote pode já existir. Detalhe: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Lote</title>
</head>
<body>
    <h1> Editar Lote: <?php echo htmlspecialchars($lote['codigo_lote']); ?></h1>
    <p><a href="lotes_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="codigo_lote">Código do Lote (UNIQUE):</label><br>
        <input type="text" id="codigo_lote" name="codigo_lote" value="<?php echo htmlspecialchars($lote['codigo_lote']); ?>" required><br><br>

        <label for="produto_id">Produto (FK):</label><br>
        <select id="produto_id" name="produto_id" required>
            <?php foreach ($produtos as $p): ?>
                <option value="<?php echo $p['id_produto']; ?>" <?php echo ($lote['produto_id'] == $p['id_produto']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="fornecedor_id">Frigorífico (Fornecedor - FK):</label><br>
        <select id="fornecedor_id" name="fornecedor_id" required>
            <?php foreach ($fornecedores as $f): ?>
                <option value="<?php echo $f['id_fornecedor']; ?>" <?php echo ($lote['fornecedor_id'] == $f['id_fornecedor']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($f['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="origem">Origem (Código do Lote de Abate):</label><br>
        <select id="origem" name="origem" required>
            <?php foreach ($lotes_abate as $la): ?>
                <option value="<?php echo $la; ?>" <?php echo ($lote['origem'] == $la) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($la); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="destino">Destino Inicial:</label><br>
        <input type="text" id="destino" name="destino" value="<?php echo htmlspecialchars($lote['destino']); ?>" required><br><br>

        <label for="data_fabricacao">Data de Fabricação:</label><br>
        <input type="date" id="data_fabricacao" name="data_fabricacao" value="<?php echo htmlspecialchars($lote['data_fabricacao']); ?>"><br><br>
        
        <label for="data_validade">Data de Validade:</label><br>
        <input type="date" id="data_validade" name="data_validade" value="<?php echo htmlspecialchars($lote['data_validade']); ?>"><br><br>
        
        <label for="peso">Peso (kg):</label><br>
        <input type="number" step="0.01" id="peso" name="peso" value="<?php echo htmlspecialchars($lote['peso']); ?>"><br><br>

        <label for="circunstancia">Status Atual (ENUM):</label><br>
        <select id="circunstancia" name="circunstancia" required>
            <option value="preparado" <?php echo ($lote['circunstancia'] == 'preparado') ? 'selected' : ''; ?>>Preparado</option>
            <option value="em_transito" <?php echo ($lote['circunstancia'] == 'em_transito') ? 'selected' : ''; ?>>Em Trânsito</option>
            <option value="entregue" <?php echo ($lote['circunstancia'] == 'entregue') ? 'selected' : ''; ?>>Entregue</option>
            <option value="armazenado" <?php echo ($lote['circunstancia'] == 'armazenado') ? 'selected' : ''; ?>>Armazenado</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>