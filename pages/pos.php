<?php
// pages/pos.php
require_once '../includes/init.php';
$page_title = 'Bán hàng POS';
$pdo = getDBConnection('quan_ly_cua_hang');

$products = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers")->fetchAll();

include '../includes/header.php';
?>

<div class="pos-layout">
    <!-- Danh sách sản phẩm -->
    <div class="pos-products">
        <div class="form-group">
            <input type="text" class="form-control" id="search-product" placeholder="Tìm kiếm sản phẩm..." onkeyup="filterProducts()">
        </div>
        <div class="product-grid" id="product-list">
            <?php foreach($products as $p): ?>
                <div class="product-card" onclick='addToCart(<?= json_encode($p) ?>)'>
                    <div style="height:80px; display:flex; align-items:center; justify-content:center; background:#f0f4f8; margin-bottom:10px; border-radius:8px;">
                        <i class="fas fa-box" style="font-size:32px; color:var(--primary-light);"></i>
                    </div>
                    <h4><?= htmlspecialchars($p['name']) ?></h4>
                    <p><?= number_format($p['selling_price']) ?>đ</p>
                    <small>Tồn: <?= $p['stock_quantity'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Giỏ hàng -->
    <div class="pos-cart">
        <div style="padding: 16px; border-bottom: 1px solid var(--border-color);">
            <select id="customer-select" class="form-control">
                <option value="">-- Khách lẻ --</option>
                <?php foreach($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> - <?= htmlspecialchars($c['phone']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="cart-items" id="cart-items">
            <!-- Items injected by JS -->
            <div style="text-align:center; color:var(--gray); margin-top:50px;">Giỏ hàng trống</div>
        </div>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Tổng tiền:</span>
                <span id="total-amount">0đ</span>
            </div>
            <div class="summary-row">
                <span>Khách đưa:</span>
                <input type="number" id="amount-paid" class="form-control" style="width:100px; text-align:right;" value="0" onkeyup="calcChange()">
            </div>
            <div class="summary-row total">
                <span>Tiền thừa:</span>
                <span id="change-amount">0đ</span>
            </div>
            <button class="btn btn-primary" style="width:100%; justify-content:center; margin-top:16px; font-size:16px; padding:12px;" onclick="checkout()">
                Thanh toán
            </button>
        </div>
    </div>
</div>

<script>
let cart = [];

function formatMoney(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + 'đ';
}

function addToCart(product) {
    let item = cart.find(i => i.id === product.id);
    if (item) {
        if (item.qty < product.stock_quantity) {
            item.qty++;
        } else {
            alert('Số lượng trong kho không đủ!');
        }
    } else {
        cart.push({...product, qty: 1});
    }
    renderCart();
}

function updateQty(id, change) {
    let item = cart.find(i => i.id === id);
    if (item) {
        let newQty = item.qty + change;
        if (newQty > 0 && newQty <= item.stock_quantity) {
            item.qty = newQty;
        } else if (newQty === 0) {
            cart = cart.filter(i => i.id !== id);
        }
    }
    renderCart();
}

function renderCart() {
    let cartHtml = '';
    let total = 0;

    if (cart.length === 0) {
        cartHtml = '<div style="text-align:center; color:var(--gray); margin-top:50px;">Giỏ hàng trống</div>';
    }

    cart.forEach(item => {
        let subtotal = item.selling_price * item.qty;
        total += subtotal;
        cartHtml += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <h4 style="margin:0; font-size:14px;">${item.name}</h4>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--primary); font-weight:600;">${formatMoney(item.selling_price)}</p>
                </div>
                <div class="cart-qty">
                    <button class="btn-icon" style="width:24px;height:24px;font-size:12px;background:var(--bg-color);" onclick="updateQty(${item.id}, -1)"><i class="fas fa-minus"></i></button>
                    <input type="text" value="${item.qty}" readonly style="width:30px; border:none; text-align:center; font-weight:bold; background:transparent;">
                    <button class="btn-icon" style="width:24px;height:24px;font-size:12px;background:var(--bg-color);" onclick="updateQty(${item.id}, 1)"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        `;
    });

    document.getElementById('cart-items').innerHTML = cartHtml;
    document.getElementById('total-amount').innerText = formatMoney(total);
    document.getElementById('total-amount').dataset.total = total;
    
    if (document.getElementById('amount-paid').value == 0 || cart.length > 0) {
        document.getElementById('amount-paid').value = total;
    }
    calcChange();
}

function calcChange() {
    let total = parseInt(document.getElementById('total-amount').dataset.total || 0);
    let paid = parseInt(document.getElementById('amount-paid').value || 0);
    let change = paid - total;
    document.getElementById('change-amount').innerText = change >= 0 ? formatMoney(change) : '0đ';
}

function filterProducts() {
    let q = document.getElementById('search-product').value.toLowerCase();
    let cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(q) ? 'block' : 'none';
    });
}

function checkout() {
    if (cart.length === 0) {
        alert('Giỏ hàng trống!');
        return;
    }
    
    let total = parseInt(document.getElementById('total-amount').dataset.total);
    let paid = parseInt(document.getElementById('amount-paid').value || 0);
    let customer_id = document.getElementById('customer-select').value;
    
    if (paid < total) {
        alert('Số tiền khách trả chưa đủ!');
        return;
    }

    let payload = {
        cart: cart,
        total_amount: total,
        amount_paid: paid,
        customer_id: customer_id
    };

    fetch('api/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Thanh toán thành công!');
            cart = [];
            renderCart();
            window.location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(err => {
        alert('Có lỗi xảy ra!');
        console.error(err);
    });
}
</script>

<?php include '../includes/footer.php'; ?>
