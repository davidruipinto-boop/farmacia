<?php
include("./conexao/conexao.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obter info do funcionário logado
$userId = $_SESSION['user_id'];
$sql = "SELECT nome_funcionario, cargo_id, senha FROM funcionario WHERE funcionario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

// Obter o nome do cargo
$sqlCargo = "SELECT nome FROM cargo WHERE cargo_id = ?";
$stmtCargo = $conn->prepare($sqlCargo);
$stmtCargo->bind_param("i", $usuario['cargo_id']);
$stmtCargo->execute();
$resCargo = $stmtCargo->get_result();
$cargo = $resCargo->fetch_assoc()['nome'];

// Processar alteração de senha
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_pass'], $_POST['conf_pass'])) {
    $nova = $_POST['nova_pass'];
    $conf = $_POST['conf_pass'];

    if ($nova === $conf && !empty($nova)) {
        $sqlUpd = "UPDATE funcionario SET senha = ? WHERE funcionario_id = ?";
        $stmtUpd = $conn->prepare($sqlUpd);
        $stmtUpd->bind_param("si", $nova, $userId);
        if ($stmtUpd->execute()) {
            $msg = "Palavra-passe alterada com sucesso!";
        } else {
            $msg = "Erro ao atualizar a palavra-passe.";
        }
    } else {
        $msg = "As palavras-passe não coincidem ou estão vazias.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>
<?php include("cabcalho.php"); ?>

<main class="perfil-page">
    <h2>Perfil de <?= htmlspecialchars($usuario['nome_funcionario']) ?></h2>
    <div class="perfil-info">
        <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome_funcionario']) ?></p>
        <p><strong>Cargo:</strong> <?= htmlspecialchars($cargo) ?></p>
        <p><strong>Palavra-passe:</strong> ********</p>
    </div>

    <form method="post" class="alterar-pass-form">
        <button type="button" id="btnAlterarPass">Alterar palavra-passe</button>

        <div id="passFields" style="display:none; margin-top:10px;">
            <input type="password" name="nova_pass" placeholder="Nova palavra-passe" required>
            <input type="password" name="conf_pass" placeholder="Confirmar palavra-passe" required>
            <button type="submit">Guardar</button>
        </div>
    </form>

    <?php if($msg): ?>
        <p style="margin-top:10px; font-weight:bold; color:#004d40;"><?= $msg ?></p>
    <?php endif; ?>
</main>

<script>
    const btn = document.getElementById('btnAlterarPass');
    const fields = document.getElementById('passFields');
    btn.addEventListener('click', () => {
        fields.style.display = fields.style.display === 'none' ? 'block' : 'none';
    });
</script>
</body>
</html>
