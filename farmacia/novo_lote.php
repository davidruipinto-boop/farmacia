<?php 
include("./conexao/conexao.php"); 

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['numero_lote']);
    $data_validade = $_POST['data_validade'];
    $quantidade = intval($_POST['quantidade']);

    if (!empty($nome) && !empty($data_validade) && $quantidade > 0) {
        $sql = "INSERT INTO lote (numero_lote, data_validade, quantidade) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $data_validade, $quantidade);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Lote criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar lote: " . $conn->error . "</p>";
        }
    } else {
        $mensagem = "<p class='erro'>⚠ Preencha todos os campos obrigatórios!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Lote</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Novo Lote</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome / Número do Lote:</label>
        <input type="text" name="numero_lote" placeholder="Ex: Lote A23B" required>

        <label>Data de Validade:</label>
        <input type="date" name="data_validade" required>

        <label>Quantidade:</label>
        <input type="number" name="quantidade" min="1" required>

        <button type="submit" class="btn-criar">💾 Guardar Lote</button>
    </form>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
