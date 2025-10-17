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
    <title>Produtos da Categoria</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="produtos">
    <?php
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $sqlCat = "SELECT nome FROM categoria WHERE categoria_id = $id";
        $resCat = $conn->query($sqlCat);
        if ($resCat->num_rows > 0) {
            $cat = $resCat->fetch_assoc();
            echo "<h2>Categoria: " . $cat['nome'] . "</h2>";
        } else {
            echo "<h2>Categoria não encontrada</h2>";
        }

        $sql = "SELECT produto_id, nome FROM produto WHERE categoria_id = $id";
        $resultado = $conn->query($sql);

        echo "<div class='grid-produtos'>";
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<a href='produto.php?id=" . $row['produto_id'] . "' class='produto'>";
                echo "<p>" . $row['nome'] . "</p>";
                echo "</a>";
            }
        } else {
            echo "<p>Não existem produtos nesta categoria.</p>";
        }
        echo "</div>";
    } else {
        echo "<p>ID inválido (nenhuma categoria selecionada).</p>";
    }
    ?>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
