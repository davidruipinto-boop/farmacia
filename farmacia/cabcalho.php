<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <img src="./img/logo.png" alt="Farmácia" height="40">
            <span class="site-name">Farmacinto</span>
        </div>

        <nav class="navbar">
            <ul class="menu">
                <li><a href="index.php">Início</a></li>
                <li><a href="produtos.php">Produtos</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="#" id="btnGestao">Gestão ▸</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="user-controls">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-welcome">
                    <img style="height: 30px; margin-bottom: -10px; margin-right: 5px;" src="./img/user.svg" alt="">
                    <?= htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Sidebar (apenas aparece se estiver logado) -->
<?php if (isset($_SESSION['user_id'])): ?>
<aside id="sidebarGestao" class="sidebar-gestao">
    <div class="sidebar-header">
        <a href="perfil.php" class="perfil-link">
            <img src="./img/user.svg" alt="Perfil" class="perfil-img">
            <span class="perfil-nome"><?= htmlspecialchars($_SESSION['username']); ?></span>
        </a>
        <button id="btnFecharSidebar" class="btn-fechar">✖</button>
    </div>

    <ul>
        <li><a href="categorias.php">Categorias</a></li>
        <li><a href="lotes.php">Lotes</a></li>
        <li><a href="funcionarios.php">Funcionários</a></li>
        <li><a href="fornecedores.php">Fornecedores</a></li>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="vendas.php">Vendas</a></li>
        <li><a href="relatorios.php">Relatórios</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="POS.php">POS</a></li>
    </ul>
</aside>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btnGestao = document.getElementById('btnGestao');
    const sidebar = document.getElementById('sidebarGestao');
    const btnFechar = document.getElementById('btnFecharSidebar');

    btnGestao?.addEventListener('click', (e) => {
        e.preventDefault();
        sidebar.classList.toggle('active');
    });

    btnFechar?.addEventListener('click', () => {
        sidebar.classList.remove('active');
    });

    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !btnGestao.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
});
</script>
<?php endif; ?>
