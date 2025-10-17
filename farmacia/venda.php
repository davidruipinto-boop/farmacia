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
    <title>Detalhes da Venda</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="detalhes-venda">
    <?php
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $sql = "SELECT v.venda_id, v.total, v.data_hora, v.pago, 
                       f.nome_funcionario AS funcionario, 
                       c.nome AS cliente
                FROM venda v
                LEFT JOIN funcionario f ON v.funcionario_id = f.funcionario_id
                LEFT JOIN cliente c ON v.cliente_id = c.cliente_id
                WHERE v.venda_id = $id";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            echo "<h2 class='titulo-venda'>Detalhes da Venda</h2>";
            echo "<table class='tabela-venda-detalhe'>";
            echo "<tr><th>ID da Venda</th><td>" . $row['venda_id'] . "</td></tr>";
            echo "<tr><th>Funcionário</th><td>" . $row['funcionario'] . "</td></tr>";
            echo "<tr><th>Cliente</th><td>" . $row['cliente'] . "</td></tr>";
            echo "<tr><th>Total</th><td>" . number_format($row['total'], 2, ',', '.') . " €</td></tr>";
            echo "<tr><th>Data e Hora</th><td>" . $row['data_hora'] . "</td></tr>";
            echo "<tr><th>Pago</th><td>" . ($row['pago'] ? "Sim" : "Não") . "</td></tr>";
            echo "</table>";







            echo "<h3>Produtos nesta venda</h3>";

        // Buscar os itens da venda
        $sqlItens = "SELECT 
                        p.nome AS produto,
                        iv.quantidade,
                        iv.preco_unitario,
                        iv.desconto,
                        (iv.quantidade * iv.preco_unitario) - iv.desconto AS subtotal
                     FROM itemvenda iv
                     JOIN produto p ON iv.produto_id = p.produto_id
                     WHERE iv.venda_id = $id";

        $resItens = $conn->query($sqlItens);

        if ($resItens && $resItens->num_rows > 0) {
            echo "<table class='detalhe-tabela' style='margin-top:20px;'>";
            echo "<tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário (€)</th>
                    <th>Desconto (€)</th>
                    <th>Subtotal (€)</th>
                  </tr>";

            while ($item = $resItens->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$item['produto']}</td>";
                echo "<td>{$item['quantidade']}</td>";
                echo "<td>" . number_format($item['preco_unitario'], 2, ',', '.') . "</td>";
                echo "<td>" . number_format($item['desconto'], 2, ',', '.') . "</td>";
                echo "<td>" . number_format($item['subtotal'], 2, ',', '.') . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>⚠ Esta venda não tem produtos associados.</p>";
        }






        } else {
            echo "<p>Venda não encontrada.</p>";
        }
    } else {
        echo "<p>ID inválido (nenhuma venda selecionada).</p>";
    }
    ?>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
