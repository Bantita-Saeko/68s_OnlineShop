<?php
session_start();
require 'config.php';
require 'session_timeout.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, products.product_name, products.price
                        FROM cart
                        JOIN products ON cart.product_id = products.product_id
                        WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?");
        $stmt->execute([$quantity, $item['cart_id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    header("Location: cart.php");
    exit;
}


$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}


if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า</title>
    <!-- CDN Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CDN Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">

    <!-- CDN SweetAlert2 -->
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
            font-weight: 700;
            font-size: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        .btn-gradient {
            color: white;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s;
            border: none;
        }
        .btn-edit { 
            background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff); 
        }
        .btn-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }
        .btn-delete {
            background: linear-gradient(135deg, #f7a3adff, #f68594ff);
            color: white;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s;
            border: none;
        }
        .btn-delete:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }
        .card { 
            border-radius: 15px; 
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); 
            margin-top: 50px;
        }
        h2 { 
            font-weight: bold; color: #6c63ff; 
        }
        label { 
            font-weight: 500; 
        }

        .table thead th {
            background: #dadadaff; 
            color: #181818ff; 
        }
    </style>
</head>
<body class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">🛒 ตะกร้าสินค้า</h2>
        <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับไปเลือกสินค้า</a>
    </div>

    <?php if (count($items) === 0): ?>
        <div class="alert alert-warning text-center">ยังไม่มีสินค้าในตะกร้า</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>ราคารวม</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['price'],2) ?> ฿</td>
                                <td><?= number_format($item['price'] * $item['quantity'],2) ?> ฿</td>
                                <td>
                                    <button class="btn btn-md btn-delete w-100" onclick="confirmRemove(<?= $item['cart_id'] ?>)">ลบ</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-end mt-3 d-flex justify-content-between">
                    <strong>รวมทั้งหมด: <strong><?= number_format($total,2) ?> บาท</strong></strong>
                    <a href="checkout.php" class="btn btn-gradient btn-edit btn-md px-5">สั่งซื้อสินค้า</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

<script>
function confirmRemove(cartId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "ต้องการลบสินค้านี้ออกจากตะกร้า?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'cart.php?remove=' + cartId;
        }
    })
}
</script>

</body>
</html>
