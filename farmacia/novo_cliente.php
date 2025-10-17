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
    $nif = trim($_POST['nif']);
    $telefone = trim($_POST['telefone']);
    $morada = trim($_POST['morada']);

    if (!empty($nome) && !empty($nif)) {
        $sql = "INSERT INTO cliente (nome, NIF, telefone, endereco) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $nome, $nif, $telefone, $morada);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Cliente criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar cliente: " . $conn->error . "</p>";
        }
    } else {
        $mensagem = "<p class='erro'>⚠ Preencha pelo menos os campos obrigatórios: Nome e NIF!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Cliente</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Novo Cliente</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome do Cliente:</label>
        <input type="text" name="nome" placeholder="Ex: Maria Santos" required>

        <label>NIF:</label>
        <input type="text" name="nif" placeholder="Ex: 123456789" required>

        <label>Telefone:</label>
        <input type="int" name="telefone" placeholder="Ex: 912345678">

        <label>Morada:</label>
        <input type="text" name="morada" placeholder="Ex: Rua das Flores, nº 15">

        <button type="submit" class="btn-criar">💾 Guardar Cliente</button>
    </form>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
