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
    <title>Farmacinto - Vendas</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main>
    <h2 class="titulo-vendas">Lista de Vendas</h2>

    <table class="tabela-vendas">
        <tr>
            <th>ID</th>
            <th>Funcionário</th>
            <th>Cliente</th>
            <th>Total (€)</th>
        </tr>
        <?php
        $sql = "SELECT v.venda_id, f.nome_funcionario, c.nome, v.total
                FROM si_farmacia.venda v
                join si_farmacia.funcionario f on f.funcionario_id = v.funcionario_id
                join si_farmacia.cliente c on c.cliente_id = v.cliente_id";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr onclick=\"window.location='venda.php?id=" . $row['venda_id'] . "'\" style='cursor:pointer'>";
                echo "<td>" . $row['venda_id'] . "</td>";
                echo "<td>" . $row['nome_funcionario'] . "</td>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . number_format($row['total'], 2, ',', '.') . " €</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhuma venda registada.</td></tr>";
        }
        ?>
    </table>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
