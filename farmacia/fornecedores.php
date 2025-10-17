<?php

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
    <title>Farmacinto - Fornecedores</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>
<?php include("./conexao/conexao.php"); ?>

<main class="fornecedores">
    <div class="botoes-acoes">
        <a href="novo_fornecedor.php" class="btn-criar">➕ Novo Fornecedore</a>
    </div>
    <h2 class="titulo-fornecedores">Lista de Fornecedores</h2>

    <div class="grid-fornecedores">
        <?php
        $sql = "SELECT fornecedor_id, nome, NIF FROM fornecedor";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<a href='fornecedor.php?id=" . $row['fornecedor_id'] . "' class='fornecedor-card'>";
                echo "<p><strong>" . $row['nome'] . "</strong></p>";
                echo "<p>NIF: " . $row['NIF'] . "</p>";
                echo "</a>";
            }
        } else {
            echo "<p>Não existem fornecedores registados.</p>";
        }
        ?>
    </div>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
