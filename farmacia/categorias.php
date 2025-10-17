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
    <title>Farmacinto - Categorias</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>
<?php include("./conexao/conexao.php"); ?>

<main class="categorias">
    <div class="botoes-acoes">
        <a href="nova_categoria.php" class="btn-criar">➕ Nova Categoria</a>
    </div>
    <h2>Categorias</h2>
    <div class="grid-categorias">

        <?php
        $sql = "SELECT categoria_id, nome FROM categoria";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<a href='categoria.php?id=" . $row['categoria_id'] . "' class='categoria'>";
                echo "<p>" . $row['nome'] . "</p>";
                echo "</a>";
            }
        } else {
            echo "<p>Não existem categorias registadas.</p>";
        }
        ?>

    </div>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
