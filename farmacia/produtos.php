<?php

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Farmacinto - Produtos</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>
<?php include("./conexao/conexao.php"); ?>


<main class="produtos">

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="botoes-acoes">
            <a href="novo_produto.php" class="btn-criar">➕ Novo Produto</a>
        </div>
    <?php endif; ?>
    

    <h2>Lista de Produtos</h2>
    <div class="grid-produtos">

        <?php
        $sql = "SELECT produto_id, nome FROM produto"; 
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<a href='produto.php?id=" . $row['produto_id'] . "' class='produto'>";
                echo "<p>" . $row['nome'] . "</p>";
                echo "</a>";
            }
        } else {
            echo "<p>Não existem produtos registados.</p>";
        }
        ?>

    </div>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
