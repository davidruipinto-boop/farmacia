<?php 



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
    <title>Farmacinto</title>
</head>
<body>
    
<?php include("cabcalho.php"); ?>




    <main>
        <section class="capa">
            <img style="height: 650px; width: 1400px;" src="./img/capa.png" alt="">
        </section>
        
        <section class="topicos">
            <h2>O que oferecemos:</h2>
            <div class="cards">
                <div class="card">
                    <h3><a href="produtos.php">Produtos</a></h3>
                    <p>Veja a lista de medicamentos e produtos disponíveis.</p>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card">
                        <h3><a href="relatorios.php">Relatórios</a></h3>
                        <p>Acompanhe vendas, melhores funcionários e muito mais.</p>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card">
                        <h3><a href="POS.php">POS</a></h3>
                        <p>Faça as suas vendas rapidamente através do nosso sistema.</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </section>

        <section class="sobre">
            <h2>Sobre Nós</h2>
            <p>
                A Farmacinto é especializada em medicamentos de qualidade, com atendimento personalizado,
                serviços de aconselhamento farmacêutico e acompanhamento dos nossos clientes.
            </p>
        </section>
    </main>

    <?php include("rodape.php"); ?>
</body>
</html>
