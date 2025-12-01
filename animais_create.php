<?php

include 'conexao.php';

// Carrega as fazendas para o campo <select> (para a chave estrangeira)
try {
    $stmt_fazendas = $pdo->query("SELECT id_fazenda, nome_fazenda FROM fazendas ORDER BY nome_fazenda");
    $fazendas = $stmt_fazendas->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar fazendas: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brinco = trim($_POST['brinco']);
    $especie = $_POST['especie']; // ENUM: 'bovino' ou 'suino'
    $raca = trim($_POST['raca']);
    $data_nascimento = $_POST['data_nascimento'];
    $id_fazenda = (int)$_POST['id_fazenda'];

    if (empty($brinco) || empty($especie) || $id_fazenda == 0) {
        $erro = "Brinco, Espécie e Fazenda são obrigatórios.";
    } else {
        try {
            // Query de INSERT na tabela 'animais'
            $sql = "INSERT INTO animais (brinco, especie, raca, data_nascimento, id_fazenda) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$brinco, $especie, $raca, $data_nascimento, $id_fazenda]);

            header("Location: animais_index.php");
            exit();
        } catch (PDOException $e) {
            // O erro mais comum aqui é o brinco (UNIQUE) já existir
            $erro = "Erro ao cadastrar: O brinco pode já estar em uso. Detalhe: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Animal</title>
</head>
<body>
    <h1> Cadastrar Novo Animal</h1>
    <p><a href="animais_index.php"> Voltar para a Lista</a></p>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="brinco">Brinco / Identificação (UNIQUE):</label><br>
        <input type="text" id="brinco" name="brinco" required><br><br>

        <label for="especie">Espécie:</label><br>
        <select id="especie" name="especie" required>
            <option value="bovino">Bovino</option>
            <option value="suino">Suíno</option>
        </select><br><br>

        <label for="raca">Raça:</label><br>
        <input type="text" id="raca" name="raca"><br><br>
        
        <label for="data_nascimento">Data de Nascimento:</label><br>
        <input type="date" id="data_nascimento" name="data_nascimento"><br><br>

        <label for="id_fazenda">Fazenda de Origem (FK):</label><br>
        <select id="id_fazenda" name="id_fazenda" required>
            <?php foreach ($fazendas as $f): ?>
                <option value="<?php echo $f['id_fazenda']; ?>"><?php echo htmlspecialchars($f['nome_fazenda']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Salvar Animal</button>
    </form>
</body>
</html>