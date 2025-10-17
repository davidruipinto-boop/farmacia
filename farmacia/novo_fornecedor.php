<?php 
include("./conexao/conexao.php"); 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $morada = trim($_POST['morada']);
    $email = trim($_POST['email']);
    $nif = intval($_POST['NIF']);
    $tlm = intval($_POST['telefone']);

    if (!empty($nome)) {
        $sql = "INSERT INTO fornecedor (nome, NIF, telefone, email, morada) 
        VALUES (?, ? , ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiss", $nome, $nif, $tlm, $email, $morada);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Fornecedor criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar Fornecedor: " . $conn->error . "</p>";
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
    <title>Novo Fornecedor</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Novo Fornecedor</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome do Fornecedor:</label>
        <input type="text" name="nome" required>

        <label>NIF:</label>
        <input type="number" name="NIF" required>

        <label>Telefone:</label>
        <input type="number" name="telefone">

        <label>Email:</label>
        <input type="text" name="email" required>

        <label>Morada:</label>
        <input type="text" name="morada">

        <button type="submit" class="btn-criar">💾 Guardar Fornecedor</button>
    </form>
</main>

<?php include("rodape.php"); ?>

</body>
</html>
