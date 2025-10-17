<?php include("./conexao/conexao.php"); 


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="produto-detalhe">
    <?php
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $sql = "SELECT nome, preco_venda FROM produto WHERE produto_id = $id";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo "<div class='detalhe-card'>";
            echo "<div class='detalhe-info'>";
            echo "<h2>" . $row['nome'] . "</h2>";
            echo "<p class='preco'>Preço: €" . number_format($row['preco_venda'], 2, ',', '.') . "</p>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<p>Produto não encontrado.</p>";
        }
    } else {
        echo "<p>ID inválido (nenhum id passado pela URL).</p>";
    }
    ?>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
