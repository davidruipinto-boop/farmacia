<?php
// POS.php
include("./conexao/conexao.php");
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mensagem de feedback após submeter
$mensagem = "";

// ========== Processamento do POST (finalizar venda) ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalize') {
    // receber o JSON do carrinho enviado pelo JS
    $cartJson = $_POST['cart'] ?? '[]';
    $cart = json_decode($cartJson, true);
    $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;
    $forma_pagamento = !empty($_POST['forma_pagamento_id']) ? intval($_POST['forma_pagamento_id']) : null;
    $pago = (isset($_POST['pago']) && $_POST['pago'] === '1') ? 1 : 0;
    $funcionario_id = intval($_SESSION['user_id']);
    date_default_timezone_set('Europe/Lisbon');
    $data_hora = date('Y-m-d H:i:s');

    if (!is_array($cart) || count($cart) === 0) {
        $mensagem = "<p class='erro'>⚠ O carrinho está vazio — não é possível finalizar a venda.</p>";
    } else {
        // calcular total do lado do servidor (para segurança)
        $total = 0.0;
        foreach ($cart as $item) {
            $q = intval($item['quantidade']);
            $preco = floatval($item['preco_unitario']);
            $desconto = floatval($item['desconto'] ?? 0);
            $subtotal = ($preco * $q) - $desconto;
            $total += $subtotal;
        }

        // inserir venda e itens dentro de transação
        $conn->begin_transaction();

        try {
            $sqlVenda = "INSERT INTO venda (cliente_id, funcionario_id, data_hora, total, forma_pagamento_id, pago) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtVenda = $conn->prepare($sqlVenda);
            // cliente pode ser null
            if ($cliente_id === null) {
                $stmtVenda->bind_param("iisdii",  $cliente_id, $funcionario_id, $data_hora, $total, $forma_pagamento,  $pago);
            } else {
                $stmtVenda->bind_param("iisdii", $cliente_id, $funcionario_id, $data_hora, $total, $forma_pagamento, $pago);
                // NOTE: bind_param types above are placeholders; we'll use proper binding below to avoid issues
            }
            // Safer approach: use two prepared statements variants because of null/client types
            $stmtVenda = $conn->prepare("INSERT INTO venda (cliente_id, funcionario_id, data_hora, total, forma_pagamento_id, pago) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtVenda->bind_param("iisdii", $cliente_id, $funcionario_id, $data_hora, $total, $forma_pagamento, $pago);
            // Above binding uses 's' for datetime; for cliente_id null we pass null as string — MySQL will insert empty string -> bad.
            // To avoid complexity across MySQL versions, we will do an explicit query using NULL handling:
            if ($cliente_id === null) {
                $sqlV = $conn->prepare("INSERT INTO venda (cliente_id, funcionario_id, data_hora, total, forma_pagamento_id, pago) VALUES (?, ?, ?, ?, ?, ?)");
                $sqlV->bind_param("iisdii", $funcionario_id, $data_hora, $total, $forma_pagamento, $pago);
                $okV = $sqlV->execute();
                if (!$okV) throw new Exception("Erro ao criar venda: " . $conn->error);
                $venda_id = $conn->insert_id;
            } else {
                $sqlV = $conn->prepare("INSERT INTO venda (cliente_id, funcionario_id, data_hora, total, forma_pagamento_id, pago) VALUES (?, ?, ?, ?, ?, ?)");
                $sqlV->bind_param("iisdii", $cliente_id, $funcionario_id, $data_hora, $total, $forma_pagamento, $pago);
                if (!$sqlV->execute()) throw new Exception("Erro ao criar venda: " . $conn->error);
                $venda_id = $conn->insert_id;
            }

            // inserir itens
            $stmtItem = $conn->prepare("INSERT INTO itemvenda (venda_id, produto_id, quantidade, preco_unitario, desconto) VALUES (?, ?, ?, ?, ?)");
            foreach ($cart as $item) {
                $produto_id = intval($item['produto_id']);
                $quantidade = intval($item['quantidade']);
                $preco_unitario = floatval($item['preco_unitario']);
                $desconto = floatval($item['desconto'] ?? 0.0);

                $stmtItem->bind_param("iiidd", $venda_id, $produto_id, $quantidade, $preco_unitario, $desconto);
                if (!$stmtItem->execute()) {
                    throw new Exception("Erro ao inserir item: " . $conn->error);
                }
                // (Opcional) atualizar stock nos lotes/produto aqui se desejado
            }

            $conn->commit();
            $mensagem = "<p class='sucesso'>✅ Venda registada com sucesso! ID da venda: {$venda_id}</p>";
        } catch (Exception $e) {
            $conn->rollback();
            $mensagem = "<p class='erro'>❌ Erro ao registar venda: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// ========== Ler categorias e produtos ==========
$categorias = [];
$resCat = $conn->query("SELECT categoria_id, nome FROM categoria ORDER BY nome");
if ($resCat) {
    while ($r = $resCat->fetch_assoc()) $categorias[] = $r;
}

// produtos (pegar preço e imagem se existir)
$produtos = [];
$resProd = $conn->query("SELECT produto_id, nome, preco_venda, categoria_id FROM produto ORDER BY nome");
if ($resProd) {
    while ($p = $resProd->fetch_assoc()) $produtos[] = $p;
}

// clientes para selecionar (opcional)
$clientes = [];
$resCli = $conn->query("SELECT cliente_id, nome FROM cliente ORDER BY nome");
if ($resCli) {
    while ($c = $resCli->fetch_assoc()) $clientes[] = $c;
}


$Forma = [];
$resForma = $conn->query("SELECT forma_pagamento_id, nome FROM forma_pagamento ORDER BY nome");
if ($resForma) {
    while ($for = $resForma->fetch_assoc()) $Forma[] = $for;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>POS - Farmácia</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="icon" type="image/png" href="./img/logo.png">
</head>
<body>

<?php include("cabcalho.php"); ?>

<main style="padding:20px;">

    <?php if ($mensagem) echo $mensagem; ?>

    <div class="pos-wrapper">

        <!-- LEFT: categorias + produtos -->
        <div class="pos-left">
            <div class="pos-categorias" id="categoriaList">
                <button data-cat="all" class="active">Todas</button>
                <?php foreach ($categorias as $cat): ?>
                    <button data-cat="<?php echo $cat['categoria_id']; ?>"><?php echo htmlspecialchars($cat['nome']); ?></button>
                <?php endforeach; ?>
            </div>

            <div class="pos-produtos" id="produtosList">
                <?php foreach ($produtos as $p): 
                    
                ?>
                    <div class="pos-produto" data-prodcat="<?php echo $p['categoria_id']; ?>"
                         data-id="<?php echo $p['produto_id']; ?>"
                         data-nome="<?php echo htmlspecialchars($p['nome'], ENT_QUOTES); ?>"
                         data-preco="<?php echo floatval($p['preco_venda']); ?>">
                        <div class="nome"><?php echo htmlspecialchars($p['nome']); ?></div>
                        <div class="preco"><?php echo number_format($p['preco_venda'], 2, ',', '.'); ?> €</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT: carrinho / console -->
        <div class="pos-right">
            <div style="display:flex; gap:8px; align-items:center; margin-bottom:10px;">
                <label style="font-weight:700; color:#004d40;">Cliente:</label>
                <select id="selectCliente">
                    <option value="">-- Nenhum --</option>
                    <?php
                    $cliente_predefinido = 1; // ID do cliente "Final"
                    foreach ($clientes as $c):
                        $selected = ($c['cliente_id'] == $cliente_predefinido) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $c['cliente_id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($c['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>




                <label style="font-weight:700; color:#004d40;">Pagamento:</label>
                <select id="selectForma">
                    <option value="">-- Nenhum --</option>
                    <?php
                    $Pagamento_pre = 1; // ID do Pagamento "Final"
                    foreach ($Forma as $for):
                        $selected = ($for['forma_pagamento_id'] == $Pagamento_pre) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $for['forma_pagamento_id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($for['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>



                <label style="margin-left:auto; font-weight:700;">Pago:</label>
                <input type="checkbox" id="checkboxPago" checked>
            </div>

            <div class="cart-list" id="cartList">
                <!-- items adicionados dinamicamente -->
                <p style="color:#777;">Carrinho vazio. Clique num produto para adicionar.</p>
            </div>

            <div class="totals">
                <div class="row"><span>Subtotal</span><span id="subtotal">0,00 €</span></div>
                <div class="row"><span>Total Descontos</span><span id="totalDescontos">0,00 €</span></div>
                <div class="row" style="font-size:1.2rem;"><span>Total</span><span id="totalGeral">0,00 €</span></div>
            </div>
            
            <button class="btn-finalizar" id="btnFinalizar">✅ Efetuar Venda</button>
            <button class="btn-cancel" id="btnLimpar">Limpar Carrinho</button>

        </div>
    </div>
</main>

<script>
// ======= Dados iniciais (JS) =======
const produtos = Array.from(document.querySelectorAll('.pos-produto')).map(el => ({
    id: el.dataset.id,
    nome: el.dataset.nome,
    preco: parseFloat(el.dataset.preco),
    el: el
}));

// filtros de categorias
const catButtons = document.querySelectorAll('#categoriaList button');
catButtons.forEach(b => {
    b.addEventListener('click', () => {
        document.querySelectorAll('#categoriaList button').forEach(x=>x.classList.remove('active'));
        b.classList.add('active');
        const cat = b.dataset.cat;
        filterProdutos(cat);
    });
});

function filterProdutos(cat) {
    document.querySelectorAll('.pos-produto').forEach(prod => {
        if (cat === 'all' || prod.dataset.prodcat === cat) {
            prod.style.display = 'inline-block';
        } else {
            prod.style.display = 'none';
        }
    });
}

// adicionar produto ao carrinho
const cart = []; // { produto_id, nome, preco_unitario, quantidade, desconto }
const cartList = document.getElementById('cartList');

document.querySelectorAll('.pos-produto').forEach(prod => {
    prod.addEventListener('click', () => {
        const id = prod.dataset.id;
        const nome = prod.dataset.nome;
        const preco = parseFloat(prod.dataset.preco);

        // se já existir, incrementa quantidade
        const existing = cart.find(i => i.produto_id == id);
        if (existing) {
            existing.quantidade += 1;
        } else {
            cart.push({ produto_id: id, nome: nome, preco_unitario: preco, quantidade: 1, desconto: 0 });
        }
        renderCart();
    });
});

function renderCart() {
    cartList.innerHTML = '';
    if (cart.length === 0) {
        cartList.innerHTML = '<p style="color:#777;">Carrinho vazio. Clique num produto para adicionar.</p>';
    } else {
        cart.forEach((item, idx) => {
            const div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = `
                <div class="info">
                    <div class="nome">${escapeHtml(item.nome)}</div>
                    <div class="small">Preço: ${formatMoney(item.preco_unitario)} €</div>
                </div>
                <div class="qty">
                    <button class="qty-btn" data-idx="${idx}" data-action="dec">−</button>
                    <div style="min-width:34px; text-align:center;">${item.quantidade}</div>
                    <button class="qty-btn" data-idx="${idx}" data-action="inc">+</button>
                </div>
                <input class="desconto" type="number" step="0.01" min="0" value="${item.desconto.toFixed(2)}" data-idx="${idx}" />
                <div style="min-width:90px; text-align:right; font-weight:700;">${formatMoney((item.preco_unitario*item.quantidade)-item.desconto)} €</div>
                <button class="qty-btn" data-idx="${idx}" data-action="remove" title="Remover" style="background:#ffdede;">✖</button>
            `;
            cartList.appendChild(div);
        });

        // hooks para os botões e inputs
        cartList.querySelectorAll('.qty-btn').forEach(b => b.addEventListener('click', e => {
            const idx = parseInt(b.dataset.idx);
            const action = b.dataset.action;
            if (action === 'inc') { cart[idx].quantidade += 1; }
            else if (action === 'dec') { cart[idx].quantidade = Math.max(1, cart[idx].quantidade - 1); }
            else if (action === 'remove') { cart.splice(idx, 1); }
            renderCart();
        }));

        cartList.querySelectorAll('.desconto').forEach(inp => {
            inp.addEventListener('change', e => {
                const idx = parseInt(inp.dataset.idx);
                let v = parseFloat(inp.value);
                if (isNaN(v) || v < 0) v = 0;
                cart[idx].desconto = v;
                renderCart();
            });
        });
    }
    updateTotals();
}

function updateTotals() {
    let subtotal = 0;
    let totalDescontos = 0;
    cart.forEach(i => {
        subtotal += i.preco_unitario * i.quantidade;
        totalDescontos += parseFloat(i.desconto || 0);
    });
    const total = subtotal - totalDescontos;
    document.getElementById('subtotal').innerText = formatMoney(subtotal) + ' €';
    document.getElementById('totalDescontos').innerText = formatMoney(totalDescontos) + ' €';
    document.getElementById('totalGeral').innerText = formatMoney(total) + ' €';
}

// finalize sale -> send to server
document.getElementById('btnFinalizar').addEventListener('click', () => {
    if (cart.length === 0) { alert('Carrinho vazio!'); return; }
    if (!confirm('Confirmar a finalização da venda?')) return;

    const cliente_id = document.getElementById('selectCliente').value || '';
    const forma_pagamento_id = document.getElementById('selectForma').value || '';
    const pago = document.getElementById('checkboxPago').checked ? '1' : '0';

    // enviar via POST (form)
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    const inputAction = document.createElement('input');
    inputAction.name = 'action'; inputAction.value = 'finalize'; form.appendChild(inputAction);

    const inputCart = document.createElement('input');
    inputCart.name = 'cart'; inputCart.value = JSON.stringify(cart); form.appendChild(inputCart);

    const inputCliente = document.createElement('input');
    inputCliente.name = 'cliente_id'; inputCliente.value = cliente_id; form.appendChild(inputCliente);

    const inputForma = document.createElement('input');
    inputForma.name = 'forma_pagamento_id'; inputForma.value = forma_pagamento_id; form.appendChild(inputForma);

    const inputPago = document.createElement('input');
    inputPago.name = 'pago'; inputPago.value = pago; form.appendChild(inputPago);

    document.body.appendChild(form);
    form.submit();
});

// limpar carrinho
document.getElementById('btnLimpar').addEventListener('click', () => {
    if (confirm('Limpar o carrinho?')) {
        cart.length = 0;
        renderCart();
    }
});

// helpers
function formatMoney(v) {
    return v.toFixed(2).replace('.', ',');
}
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'": '&#39;'}[m]; });
}

const produtosOriginais = Array.from(document.querySelectorAll('.pos-produto')).map(el => {
    return {
        id: el.dataset.id,
        nome: el.dataset.nome,
        preco: parseFloat(el.dataset.preco),
        cat: el.dataset.prodcat,
        html: el.outerHTML, // backup (não usado diretamente)
        node: el
    };
});

// Remove original nodes from DOM (we'll rebuild into columns)
const produtosListEl = document.getElementById('produtosList');
produtosOriginais.forEach(p => {
    if (p.node.parentNode) p.node.parentNode.removeChild(p.node);
});

// Helper: chunk array into groups of size n
function chunkArray(arr, size) {
    const out = [];
    for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
    return out;
}

// Function to rebuild columns from a filtered list of produtos
function rebuildColumns(filteredProdutos) {
    // clear current
    produtosListEl.innerHTML = '';

    // chunk into groups of 3
    const cols = chunkArray(filteredProdutos, 3);
    cols.forEach(group => {
        const col = document.createElement('div');
        col.className = 'pos-col';
        group.forEach(p => {
            // recreate product element (keep dataset attributes)
            const card = document.createElement('div');
            card.className = 'pos-produto';
            card.setAttribute('data-id', p.id);
            card.setAttribute('data-nome', p.nome);
            card.setAttribute('data-preco', p.preco);
            card.setAttribute('data-prodcat', p.cat);

            // inner content (ajusta se tiveres imagem ou outras info)
            card.innerHTML = `
                <div class="nome">${escapeHtml(p.nome)}</div>
                <div class="preco">${formatMoney(p.preco)} €</div>
            `;
            // attach click listener to add to cart
            card.addEventListener('click', () => {
                addProductToCart(p.id, p.nome, p.preco);
            });

            col.appendChild(card);
        });
        produtosListEl.appendChild(col);
    });

    // if no product, show message
    if (filteredProdutos.length === 0) {
        produtosListEl.innerHTML = '<p style="color:#777; padding:16px;">Nenhum produto encontrado.</p>';
    }
}

// Filtering: returns array of product objects filtered by cat (string or 'all')
function getFilteredProducts(cat) {
    if (!cat || cat === 'all') return produtosOriginais.slice(); // copy
    return produtosOriginais.filter(p => String(p.cat) === String(cat));
}

// initial render: all products
rebuildColumns(getFilteredProducts('all'));

// Category button handlers
document.querySelectorAll('#categoriaList button').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#categoriaList button').forEach(x => x.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        const filtered = getFilteredProducts(cat);
        rebuildColumns(filtered);
    });
});

// ===== Cart helpers (ensure used functions exist) =====
function addProductToCart(id, nome, preco) {
    // reuse existing cart logic: find existing, increment, renderCart()
    const existing = cart.find(i => i.produto_id == id);
    if (existing) existing.quantidade += 1;
    else cart.push({ produto_id: id, nome: nome, preco_unitario: preco, quantidade: 1, desconto: 0 });
    renderCart();
}

// small helpers (reused from earlier)
function formatMoney(v) {
    return v.toFixed(2).replace('.', ',');
}
function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'": '&#39;'}[m]; });
}

</script>

</body>
</html>
