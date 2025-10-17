<?php 
include("./conexao/conexao.php"); 

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

// Buscar cargos disponíveis para o dropdown
$sqlCargos = "SELECT cargo_id, nome FROM cargo";
$resCargos = $conn->query($sqlCargos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome_funcionario']);
    $senha = trim($_POST['senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);
    $cargo_id = intval($_POST['cargo_id']);

    // Validação dos campos
    if (empty($nome) || empty($senha) || empty($confirmar_senha) || $cargo_id <= 0) {
        $mensagem = "<p class='erro'>⚠ Preencha todos os campos obrigatórios!</p>";
    } elseif ($senha !== $confirmar_senha) {
        $mensagem = "<p class='erro'>❌ As senhas não coincidem!</p>";
    } else {
        // Inserir diretamente a senha sem hash (apenas para ambiente de testes)
        $sql = "INSERT INTO funcionario (nome_funcionario, senha, cargo_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $senha, $cargo_id);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Funcionário criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar funcionário: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Funcionário</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Novo Funcionário</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome do Funcionário:</label>
        <input type="text" name="nome_funcionario" placeholder="Ex: João Silva" required>

        <label>Senha:</label>
        <input type="password" name="senha" placeholder="Crie uma palavra-passe" required>

        <label>Confirmar Senha:</label>
        <input type="password" name="confirmar_senha" placeholder="Repita a palavra-passe" required>

        <label>Cargo:</label>
        <select name="cargo_id" required>
            <option value="">-- Selecione o Cargo --</option>
            <?php
            if ($resCargos && $resCargos->num_rows > 0) {
                while ($cargo = $resCargos->fetch_assoc()) {
                    echo "<option value='" . $cargo['cargo_id'] . "'>" . htmlspecialchars($cargo['nome']) . "</option>";
                }
            } else {
                echo "<option disabled>Nenhum cargo encontrado</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn-criar">💾 Guardar Funcionário</button>
    </form>
</main>

<?php include("./rodape.php"); ?>

</body>
</html>
