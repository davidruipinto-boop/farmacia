<?php 
include("./conexao/conexao.php"); 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $preco_compra = floatval($_POST['preco_compra']);
    $preco_venda = floatval($_POST['preco_venda']);
    $categoria_id = intval($_POST['categoria_id']);
    $lote_id = intval($_POST['lote_id']);

    if (!empty($nome) && $preco_venda > 0) {
        $sql = "INSERT INTO produto (sku, nome, categoria_id, preco_venda, preco_compra, codigo_barras, quantidade_minima, lote_id) 
        VALUES (NULL,? , ?,? , ?, NULL, NULL, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siddi", $nome, $categoria_id, $preco_venda, $preco_compra, $lote_id);

        if ($stmt->execute()) {
            $mensagem = "<p class='sucesso'>✅ Produto criado com sucesso!</p>";
        } else {
            $mensagem = "<p class='erro'>❌ Erro ao criar produto: " . $conn->error . "</p>";
        }
    } else {
        $mensagem = "<p class='erro'>⚠ Preencha todos os campos obrigatórios!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Produto</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("./cabcalho.php"); ?>

<main class="form-container">
    <h2>Criar Novo Produto</h2>

    <?= $mensagem ?>

    <form method="POST" class="form-criar">
        <label>Nome do Produto:</label>
        <input type="text" name="nome" required>

        <label>Preço de Compra (€):</label>
        <input type="number" step="0.01" name="preco_compra" required>

        <label>Preço de Venda (€):</label>
        <input type="number" step="0.01" name="preco_venda" required>

        <label>Categoria:</label>
        <select name="categoria_id" required>
            <option value="">-- Escolha --</option>
            <?php
            $resCat = $conn->query("SELECT categoria_id, nome FROM categoria");
            while ($cat = $resCat->fetch_assoc()) {
                echo "<option value='{$cat['categoria_id']}'>{$cat['nome']}</option>";
            }
            ?>
        </select>

        <label>Lote:</label>
        <select name="lote_id" required>
            <option value="">-- Escolha --</option>
            <?php
            $resLote = $conn->query("SELECT lote_id, numero_lote FROM lote");
            while ($lote = $resLote->fetch_assoc()) {
                echo "<option value='{$lote['lote_id']}'>Lote nº {$lote['numero_lote']}</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn-criar">💾 Guardar Produto</button>
    </form>
</main>

<?php include("rodape.php"); ?>

</body>
</html>
