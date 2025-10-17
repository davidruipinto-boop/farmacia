
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
    <title>Farmacinto - Funcionários</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>
<?php include("./conexao/conexao.php"); ?>

<main>
    <div class="botoes-acoes">
        <a href="novo_cliente.php" class="btn-criar">➕ Novo Cliente</a>
    </div>
    <h2 class="titulo-funcionarios">Lista de Clientes</h2>

    <table class="tabela-funcionarios">
        <tr>
            <th>Nome</th>
            <th>NIF</th>
            <th>Telefone</th>
            <th>Endereço</th>
        </tr>
        <?php
        $sql = "SELECT f.nome, f.NIF, f.telefone, f.endereco
                FROM cliente f
                WHERE f.cliente_id != 1";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $row['NIF'] . "</td>";
                echo "<td>" . $row['telefone'] . "</td>";
                echo "<td>" . $row['endereco'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum cliente registado.</td></tr>";
        }
        ?>
    </table>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
