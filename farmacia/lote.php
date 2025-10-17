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
    <title>Detalhes do Lote</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="lote-detalhes">
    <?php
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Buscar informações do lote
        $sqlLote = "SELECT lote_id, numero_lote, quantidade, data_validade 
                    FROM lote 
                    WHERE lote_id = $id";
        $resLote = $conn->query($sqlLote);

        if ($resLote && $resLote->num_rows > 0) {
            $lote = $resLote->fetch_assoc();

            echo "<h2>Detalhes do Lote Nº " . $lote['numero_lote'] . "</h2>";

            echo "<table class='lote-tabela'>";
            echo "<tr><th>ID do Lote</th><td>" . $lote['lote_id'] . "</td></tr>";
            echo "<tr><th>Número do Lote</th><td>" . $lote['numero_lote'] . "</td></tr>";
            echo "<tr><th>Quantidade em stock</th><td>" . $lote['quantidade'] . "</td></tr>";
            echo "<tr><th>Validade</th><td>" . date("d/m/Y", strtotime($lote['data_validade'])) . "</td></tr>";
            echo "</table>";

            echo "<h3 style='margin-top:30px;'>Produtos neste lote</h3>";

            // Buscar produtos associados a este lote
            $sqlProdutos = "SELECT produto_id, nome 
                            FROM produto 
                            WHERE lote_id = $id";
            $resProdutos = $conn->query($sqlProdutos);

            if ($resProdutos && $resProdutos->num_rows > 0) {
                echo "<div class='lote-produtos'>";
                while ($row = $resProdutos->fetch_assoc()) {
                    echo "<a href='produto.php?id=" . $row['produto_id'] . "' class='produto'>";
                    echo "<p>" . $row['nome'] . "</p>";
                    echo "</a>";
                }
                echo "</div>";
            } else {
                echo "<p>⚠ Este lote não tem produtos registados.</p>";
            }

        } else {
            echo "<p>⚠ Lote não encontrado.</p>";
        }
    } else {
        echo "<p>⚠ ID inválido (nenhum lote selecionado).</p>";
    }
    ?>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
