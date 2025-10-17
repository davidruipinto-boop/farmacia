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
        <a href="novo_funcionario.php" class="btn-criar">➕ Novo Funcionário</a>
    </div>
    <h2 class="titulo-funcionarios">Lista de Funcionários</h2>

    <table class="tabela-funcionarios">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Cargo</th>
        </tr>
        <?php
        $sql = "SELECT f.funcionario_id, f.nome_funcionario, c.nome AS cargo
                FROM funcionario f
                LEFT JOIN cargo c ON f.cargo_id = c.cargo_id";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['funcionario_id'] . "</td>";
                echo "<td>" . $row['nome_funcionario'] . "</td>";
                echo "<td>" . ($row['cargo'] ? $row['cargo'] : "Sem cargo") . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum funcionário registado.</td></tr>";
        }
        ?>
    </table>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
