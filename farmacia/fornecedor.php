<?php include("./conexao/conexao.php"); 


session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Fornecedor</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="fornecedor-detalhe">
    <?php
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $sql = "SELECT * FROM fornecedor WHERE fornecedor_id = $id";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            echo "<h2 class='titulo-fornecedor'>Detalhes do Fornecedor</h2>";
            echo "<table class='tabela-fornecedor'>";
            echo "<tr><th>Nome</th><td>" . $row['nome'] . "</td></tr>";
            echo "<tr><th>NIF</th><td>" . $row['NIF'] . "</td></tr>";
            echo "<tr><th>Telefone</th><td>" . $row['telefone'] . "</td></tr>";
            echo "<tr><th>Email</th><td>" . $row['email'] . "</td></tr>";
            echo "<tr><th>Endereço</th><td>" . $row['morada'] . "</td></tr>";
            echo "</table>";
        } else {
            echo "<p>Fornecedor não encontrado.</p>";
        }
    } else {
        echo "<p>ID inválido (nenhum fornecedor selecionado).</p>";
    }
    ?>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
