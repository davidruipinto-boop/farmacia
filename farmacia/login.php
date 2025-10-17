<?php
session_start();
include("./conexao/conexao.php");

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT funcionario_id, nome_funcionario, senha 
            FROM funcionario 
            WHERE nome_funcionario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $row = $resultado->fetch_assoc();
        
        if ($password === $row['senha']) {
            $_SESSION['user_id'] = $row['funcionario_id'];
            $_SESSION['username'] = $row['nome_funcionario'];

            header("Location: index.php");
            exit;
        } else {
            $erro = "Password incorreta!";
        }
    } else {
        $erro = "Utilizador não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Farmacinto - login</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<main class="login-container">
    <h2>Login Funcionários</h2>
    <form method="post" class="form-login">
        <input type="text" name="username" placeholder="Nome de utilizador" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Entrar</button>
    </form>
    <?php if ($erro): ?>
        <p class="erro-login"><?= $erro ?></p>
    <?php endif; ?>
</main>

</body>
</html>
