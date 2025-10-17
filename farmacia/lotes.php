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
    <title>Farmacinto - Lotes</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>
<?php include("./conexao/conexao.php"); ?>

<main class="lotes">
    <div class="botoes-acoes">
        <a href="novo_lote.php" class="btn-criar">➕ Novo Lote</a>
    </div>
    <h2>Lista de Lotes</h2>
    <div class="grid-lotes">

        <?php
        $sql = "SELECT lote_id, numero_lote, data_validade, quantidade FROM lote";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<a href='lote.php?id=" . $row['lote_id'] . "' class='lote'>";
                echo "<p><strong>Lote Nº " . $row['numero_lote'] . "</strong></p>";
                echo "<p>Quantidade: " . $row['quantidade'] . "</p>";
                echo "<p>Validade: " . date("d/m/Y", strtotime($row['data_validade'])) . "</p>";
                echo "</a>";
            }
        } else {
            echo "<p>Não existem lotes registados.</p>";
        }
        ?>

    </div>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
