<?php
require '../config.php';
require 'authen_admin.php';
require '../function.php';  
require '../session_timeout.php';

$stmt = $conn->query("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        echo json_encode(['success' => true, 'msg' => 'อัปเดตสถานะเรียบร้อย']);
        exit;
    }
    if (isset($_POST['update_shipping'])) {
        $stmt = $conn->prepare("UPDATE shipping SET shipping_status = ? WHERE shipping_id = ?");
        $stmt->execute([$_POST['shipping_status'], $_POST['shipping_id']]);
        echo json_encode(['success' => true, 'msg' => 'อัปเดตการจัดส่งเรียบร้อย']);
        exit;
    }
}


$totalOrders = count($orders);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            font-family: 'Kanit', 'Nunito', sans-serif; 
            background: linear-gradient(135deg, #f8f9fa, #e6f2ff);
        }
        .text-gradient { 
            background: linear-gradient(90deg, #80c2ffff, #a7c5ffff, #cdb4db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; 
            font-weight:700; 
            font-size:2rem; 
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-radius:15px; 
            padding:15px; 
            text-align:center; 
            color:white; 
            background: linear-gradient(135deg,#ffdde1,#ee9ca7); 
            box-shadow:0 4px 12px rgba(0,0,0,0.08); 
            margin-bottom:20px;
        }
        .stat-icon {
            font-size:1.5rem; 
            margin-bottom:5px;
        }
        .btn-gradient {
            color:white; 
            font-weight:500;
            border-radius:12px; 
            transition: all 0.3s; 
            border:none;
        }
        .btn-edit {
             background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff);
        }
    </style>
</head>
<body class="container py-4">

<div class="d-flex justify-content-between align-items-center mt-5 mb-3">
    <h2 class="mb-4 text-gradient fw-bold"><i class="bi bi-cart-check stat-icon"></i> จัดการคำสั่งซื้อ</h2>
    <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
</div>

<div class="stat-card mb-4">
    <i class="bi bi-box-seam stat-icon text-white"></i>
    <div class="fs-4 fw-bold text-white">จำนวนคำสั่งซื้อ : <?= $totalOrders ?></div>
</div>

<div class="accordion" id="ordersAccordion">
<?php foreach ($orders as $index => $order): ?>
    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
    <div class="accordion-item mb-3">
        <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                <div class="d-flex flex-column w-100">
                    <span><strong>คำสั่งซื้อ #<?= $order['order_id'] ?></strong> | <?= htmlspecialchars($order['username']) ?></span>
                    <small class="text-muted"><?= date("d/m/Y H:i", strtotime($order['order_date'])) ?></small>
                </div>
                <?php
                    $badgeColors = ['pending'=>'secondary','processing'=>'warning','shipped'=>'info','completed'=>'success','cancelled'=>'danger'];
                    $status = $order['status'];
                    $badgeClass = $badgeColors[$status] ?? 'secondary';
                ?>
                <span class="ms-auto badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
            </button>
        </h2>
        <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#ordersAccordion">
            <div class="accordion-body">
                <h5 class="mb-3">🛒 รายการสินค้า</h5>
                <ul class="list-group mb-3">
                    <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                            <span class="fw-bold text-success"><?= number_format($item['quantity'] * $item['price'], 2) ?> ฿</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="fw-bold fs-5">ยอดรวม: <span class="text-danger"><?= number_format($order['total_amount'], 2) ?> ฿</span></p>


                <form class="row g-2 mb-4 update-status-form" data-order-id="<?= $order['order_id'] ?>">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <?php foreach (['pending','processing','shipped','completed','cancelled'] as $s): 
                                $selected = ($order['status']===$s)?'selected':''; ?>
                                <option value="<?= $s ?>" <?= $selected ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-gradient btn-edit w-100">อัปเดตสถานะ</button>
                    </div>
                </form>


                <?php if ($shipping): ?>
                    <h5 class="mb-3">🚚 ข้อมูลการจัดส่ง</h5>
                    <div class="card p-3 mb-3">
                        <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?></p>
                        <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                    </div>
                    <form class="row g-2 update-shipping-form" data-shipping-id="<?= $shipping['shipping_id'] ?>">
                        <div class="col-md-4">
                            <select name="shipping_status" class="form-select">
                                <?php foreach (['not_shipped','shipped','delivered'] as $s): 
                                    $selected = ($shipping['shipping_status']===$s)?'selected':''; ?>
                                    <option value="<?= $s ?>" <?= $selected ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-gradient btn-edit w-100">อัปเดตการจัดส่ง</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.querySelectorAll('.update-status-form').forEach(form=>{
    form.addEventListener('submit', async e=>{
        e.preventDefault();
        const orderId = form.dataset.orderId;
        const status = form.querySelector('select[name="status"]').value;
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', status);
        formData.append('update_status', true);

        const res = await fetch('orders.php', { method:'POST', body: formData });
        const data = await res.json();
        Swal.fire({icon:'success', title:data.msg, timer:1500, showConfirmButton:false}).then(()=>location.reload());
    });
});


document.querySelectorAll('.update-shipping-form').forEach(form=>{
    form.addEventListener('submit', async e=>{
        e.preventDefault();
        const shippingId = form.dataset.shippingId;
        const shippingStatus = form.querySelector('select[name="shipping_status"]').value;
        const formData = new FormData();
        formData.append('shipping_id', shippingId);
        formData.append('shipping_status', shippingStatus);
        formData.append('update_shipping', true);

        const res = await fetch('orders.php', { method:'POST', body: formData });
        const data = await res.json();
        Swal.fire({icon:'success', title:data.msg, timer:1500, showConfirmButton:false}).then(()=>location.reload());
    });
});

</script>
</body>
</html>
