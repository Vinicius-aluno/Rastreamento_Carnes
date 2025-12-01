<?php

include 'conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("ID do animal não fornecido.");
}

// 1. Carrega dados auxiliares (fazendas) e o animal atual
try {
    $stmt_fazendas = $pdo->query("SELECT id_fazenda, nome_fazenda FROM fazendas ORDER BY nome_fazenda");
    $fazendas = $stmt_fazendas->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM animais WHERE id_animal = ?");
    $stmt->execute([$id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        die("Animal não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// 2. Lógica de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brinco = trim($_POST['brinco']);
    $especie = $_POST['especie'];
    $raca = trim($_POST['raca']);
    $data_nascimento = $_POST['data_nascimento'];
    $id_fazenda = (int)$_POST['id_fazenda'];

    if (empty($brinco) || empty($especie) || $id_fazenda == 0) {
        $erro = "Campos obrigatórios faltando.";
    } else {
        try {
            // Query de UPDATE
            $sql = "UPDATE animais SET brinco = ?, especie = ?, raca = ?, data_nascimento = ?, id_fazenda = ? WHERE id_animal = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$brinco, $especie, $raca, $data_nascimento, $id_fazenda, $id]);

            header("Location: animais_index.php");
            exit();
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar: Verifique se o Brinco já está sendo usado. Detalhe: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>
</head>
<body>
    <h1> Editar Animal: <?php echo htmlspecialchars($animal['brinco']); ?></h1>
    <p><a href="animais_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="brinco">Brinco / Identificação (UNIQUE):</label><br>
        <input type="text" id="brinco" name="brinco" value="<?php echo htmlspecialchars($animal['brinco']); ?>" required><br><br>

        <label for="especie">Espécie:</label><br>
        <select id="especie" name="especie" required>
            <option value="bovino" <?php echo ($animal['especie'] == 'bovino') ? 'selected' : ''; ?>>Bovino</option>
            <option value="suino" <?php echo ($animal['especie'] == 'suino') ? 'selected' : ''; ?>>Suíno</option>
        </select><br><br>

        <label for="raca">Raça:</label><br>
        <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($animal['raca']); ?>"><br><br>
        
        <label for="data_nascimento">Data de Nascimento:</label><br>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($animal['data_nascimento']); ?>"><br><br>

        <label for="id_fazenda">Fazenda de Origem (FK):</label><br>
        <select id="id_fazenda" name="id_fazenda" required>
            <?php foreach ($fazendas as $f): ?>
                <option value="<?php echo $f['id_fazenda']; ?>" 
                    <?php echo ($animal['id_fazenda'] == $f['id_fazenda']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($f['nome_fazenda']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>