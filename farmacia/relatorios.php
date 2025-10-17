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
    <title>Farmacinto - Relatórios</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="relatorios">
    <h2 class="titulo-relatorios">Relatórios</h2>

    <form method="post" class="form-relatorio">
        <label for="relatorio">Escolha um relatório:</label>
        <select name="relatorio" id="relatorio">
            <option value="">-- Selecione --</option>
            <option value="clientes_que_mais_compraram">Clientes que mais compraram</option>
            <option value="funcionarios_que_mais_vendem">Funcionários que mais vendem</option>
            <option value="lucro_por_produto">Lucro por produto</option>
            <option value="produtos_mais_vendidos">Produtos mais vendidos</option>
            <option value="vendas_nao_pagas">Vendas não pagas</option>
        </select>
        <button type="submit" name="ver">Rever</button>
    </form>

    <div class="tabela-relatorios">
        <table>
            <thead>
                <?php
                if (isset($_POST['ver']) && !empty($_POST['relatorio'])) {
                    $relatorio = $_POST['relatorio'];

                    $queries = [
                        "clientes_que_mais_compraram" => "SELECT c.nome AS Nome, SUM(v.total) AS Total_Gasto
                                                            FROM venda v
                                                            JOIN cliente c ON v.cliente_id = c.cliente_id
                                                            WHERE v.pago = 1
                                                            GROUP BY c.cliente_id, c.nome
                                                            ORDER BY Total_Gasto DESC
                                                            LIMIT 10",

                        "funcionarios_que_mais_vendem" => "SELECT f.nome_funcionario AS Nome, COUNT(v.venda_id) AS Quantidade, SUM(v.total) AS Total_Vendido
                                                            FROM venda v 
                                                            JOIN funcionario f ON v.funcionario_id = f.funcionario_id
                                                            GROUP BY f.nome_funcionario
                                                            ORDER BY Total_Vendido DESC",

                        "lucro_por_produto" => "SELECT p.nome AS Nome, p.Preco_Venda, p.Preco_Compra, (p.Preco_Venda - p.Preco_Compra) AS Lucro
                                                FROM produto p",

                        "produtos_mais_vendidos" => "SELECT p.nome AS Nome, SUM(iv.quantidade) AS Total_Vendido
                                                     FROM itemvenda iv
                                                     JOIN produto p ON iv.produto_id = p.produto_id
                                                     GROUP BY p.nome
                                                     ORDER BY Total_Vendido DESC
                                                     LIMIT 10",

                        "vendas_nao_pagas" => "SELECT v.venda_id AS Número, c.nome AS Cliente, v.total AS Total, v.data_hora AS Data_Hora
                                               FROM venda v
                                               JOIN cliente c ON v.cliente_id = c.cliente_id
                                               WHERE v.pago = 0"
                    ];

                    if (array_key_exists($relatorio, $queries)) {
                        $sql = $queries[$relatorio];
                        $resultado = $conn->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                           
                            $primeira = $resultado->fetch_assoc();
                            echo "<tr>";
                            foreach (array_keys($primeira) as $coluna) {
                                echo "<th>" . htmlspecialchars($coluna) . "</th>";
                            }
                            echo "</tr>";

                            echo "<tr>";
                            foreach ($primeira as $valor) {
                                echo "<td>" . htmlspecialchars($valor) . "</td>";
                            }
                            echo "</tr>";

                            while ($row = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                foreach ($row as $valor) {
                                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Nenhum dado encontrado.</td></tr>";
                        }
                    }
                }
                ?>
            </thead>
        </table>
    </div>
</main>
<br><br>
<?php include("./rodape.php"); ?>

</body>
</html>
