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
    $descricao = trim($_POST['descricao']);

    if (!empty($nome)) {
        $sql = "INSERT INTO categoria (nome, descricao) 
        VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $descricao);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Categoria criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar categoria: " . $conn->error . "</p>";
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
    <title>Nova Categoria</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Nova Categoria</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome da Categoria:</label>
        <input type="text" name="nome" required>

        <label>Descrição (Opcional):</label>
        <input type="text" name="descricao" required>

        <button type="submit" class="btn-criar">💾 Guardar Categoria</button>
    </form>
</main>

<?php include("rodape.php"); ?>

</body>
</html>
