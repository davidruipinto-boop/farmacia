<?php 
include("./conexao/conexao.php"); 
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
    <title>Dashboard - Farmácia</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="dashboard">

    <h1>Painel de Controlo</h1>
    <p class="subtitulo">Resumo geral da farmácia</p>

    <section class="cards_dash">
        <?php
        $vendas = $conn->query("SELECT COUNT(*) AS total FROM venda")->fetch_assoc()['total'];

        $clientes = $conn->query("SELECT COUNT(*) AS total FROM cliente")->fetch_assoc()['total'];

        $produtos = $conn->query("SELECT COUNT(*) AS total FROM produto")->fetch_assoc()['total'];

        $lucro = $conn->query("SELECT SUM(total) AS total FROM venda WHERE pago = 1")->fetch_assoc()['total'];
        $lucro = $lucro ? number_format($lucro, 2, ',', '.') : "0,00";
        ?>

        <div class="card_dash">
            <h3>💰 Vendas</h3>
            <p><?php echo $vendas; ?></p>
        </div>
        <div class="card_dash">
            <h3>👥 Clientes</h3>
            <p><?php echo $clientes; ?></p>
        </div>
        <div class="card_dash">
            <h3>💊 Produtos</h3>
            <p><?php echo $produtos; ?></p>
        </div>
        <div class="card_dash">
            <h3>📈 Total Recebido (€)</h3>
            <p><?php echo $lucro; ?></p>
        </div>
    </section>

    <section class="graficos">
        <div class="grafico">
            <h3>Vendas desde sempre</h3>
            <canvas id="graficoVendas"></canvas>
        </div>

        <div class="grafico">
            <h3>Produtos mais vendidos</h3>
            <canvas id="graficoProdutos"></canvas>
        </div>
    </section>

    <section class="tabela-dashboard">
        <h3>Últimas Vendas</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Funcionário</th>
                <th>Total (€)</th>
                <th>Data</th>
            </tr>
            <?php
            $res = $conn->query("
                SELECT v.venda_id, c.nome AS cliente, f.nome_funcionario AS funcionario, v.total, v.data_hora
                FROM venda v
                JOIN cliente c ON v.cliente_id = c.cliente_id
                JOIN funcionario f ON v.funcionario_id = f.funcionario_id
                ORDER BY v.data_hora DESC
                LIMIT 5
            ");
            if ($res && $res->num_rows > 0) {
                while ($v = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$v['venda_id']}</td>
                            <td>{$v['cliente']}</td>
                            <td>{$v['funcionario']}</td>
                            <td>" . number_format($v['total'], 2, ',', '.') . "</td>
                            <td>" . date('d/m/Y H:i', strtotime($v['data_hora'])) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Sem vendas recentes</td></tr>";
            }
            ?>
        </table>
    </section>

</main>

<?php include("./rodape.php"); ?>

<?php
$sqlVendas = "
    SELECT DATE(data_hora) AS dia, SUM(total) AS total
    FROM venda
    WHERE DATE(data_hora) >= CURDATE() - INTERVAL 1000 DAY
    GROUP BY DATE(data_hora)
    ORDER BY dia
";
$resultVendas = $conn->query($sqlVendas);
$labelsVendas = [];
$dadosVendas = [];
while ($row = $resultVendas->fetch_assoc()) {
    $labelsVendas[] = date('d/m', strtotime($row['dia']));
    $dadosVendas[] = $row['total'];
}

$sqlProdutos = "
    SELECT p.nome, SUM(iv.quantidade) AS qtd
    FROM itemvenda iv
    JOIN produto p ON iv.produto_id = p.produto_id
    GROUP BY p.nome
    ORDER BY qtd DESC
    LIMIT 5
";
$resultProdutos = $conn->query($sqlProdutos);
$labelsProdutos = [];
$dadosProdutos = [];
while ($row = $resultProdutos->fetch_assoc()) {
    $labelsProdutos[] = $row['nome'];
    $dadosProdutos[] = $row['qtd'];
}
?>

<script>




const ctxVendas = document.getElementById('graficoVendas').getContext('2d');
new Chart(ctxVendas, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labelsVendas); ?>,
        datasets: [{
            label: 'Vendas (€)',
            data: <?php echo json_encode($dadosVendas); ?>,
            borderColor: '#00796b',
            backgroundColor: 'rgba(0,121,107,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: { responsive: true }
});





const ctxProdutos = document.getElementById('graficoProdutos').getContext('2d');
new Chart(ctxProdutos, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labelsProdutos); ?>,
        datasets: [{
            label: 'Quantidade vendida',
            data: <?php echo json_encode($dadosProdutos); ?>,
            backgroundColor: '#26a69a'
        }]
    },
    options: { responsive: true }
});
</script>

</body>
</html>
