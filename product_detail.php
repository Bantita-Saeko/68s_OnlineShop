<?php
require_once 'config.php';
session_start();

if(!isset($_GET['id'])){
    header('Location: index.php');
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);
$product_id = $_GET['id'];

$stmt = $conn->prepare("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// เตรียมรูป
$img = !empty($product['image'])
    ? 'product_images/' . rawurlencode($product['image'])
    : 'product_images/no-image.png';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>รายละเอียดสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Kanit', 'Nunito', sans-serif;
    background: linear-gradient(135deg, #f8f9fa, #e6f2ff);
}
.card {
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}
.text-gradient {
    background: linear-gradient(90deg, #80c2ffff, #a7c5ffff, #cdb4db);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}
.btn-gradient {
    background: linear-gradient(135deg, #80c2ff, #a7c5ff);
    color: white;
    border-radius: 12px;
    border: none;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-gradient:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
}
.btn-custom { background: linear-gradient(135deg, #a8edea, #fed6e3); }
.btn-outline-custom { 
    background: #ffffff; 
    border: 1px solid #86c0fd; 
    color: #86c0fd; 
    transition: all 0.3s; 
}
.btn-outline-custom:hover {
    background: #86c0fd;
    color: white;
}
.btn-danger { 
    background-color: #cc3d3d; 
    border: none;
    transition: all 0.3s;
}
.btn-danger:hover { 
    background-color: #b11a1a; 
    color: white; 
}
input[type=number] {
    width: 80px;
    padding: 5px;
    margin-right: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
img {
    max-width: 75%;
    border-radius: 12px;
    box-shadow: 1px 1px 8px rgba(0,0,0,0.1);
    object-fit: cover;
}
@media (max-width: 768px) {
    .card {
        padding: 20px;
    }
}
</style>
</head>
<body class="container py-4">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h1 class="text-gradient"><i class="bi bi-box-seam"></i> รายละเอียดสินค้า</h1>
    <div class="mt-2">
        <?php if ($isLoggedIn): ?>
            <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
            <a href="profile.php" class="btn btn-outline-custom me-2 mb-2"><i class="bi bi-person-circle"></i> ข้อมูลส่วนตัว</a>
            <a href="cart.php" class="btn btn-outline-custom me-2 mb-2"><i class="bi bi-cart"></i> ดูตะกร้า</a>
            <a href="logout.php" class="btn btn-danger mb-2"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-gradient me-2 mb-2">เข้าสู่ระบบ</a>
            <a href="register.php" class="btn btn-outline-custom mb-2">สมัครสมาชิก</a>
        <?php endif; ?>
    </div>
</div>

<a href="index.php" class="btn btn-outline-custom mb-4"><i class="bi bi-arrow-left"></i> กลับหน้ารายการสินค้า</a>

<!-- Product Detail Card -->
<div class="card mb-5">
    <div class="row g-4">
        <!-- รูปสินค้า -->
        <div class="col-md-5 text-center">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
        </div>

        <!-- ข้อมูลสินค้า -->
        <div class="col-md-7 d-flex flex-column justify-content-between">
            <div>
                <h2 class="card-title text-gradient"><i class="bi bi-tag"></i> <?= htmlspecialchars($product['product_name']) ?></h2>
                <h6 class="text-muted mb-3">หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></h6>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p><strong>ราคา:</strong> <?= number_format($product['price'],2) ?> บาท</p>
                <p><strong>คงเหลือ:</strong> <?= htmlspecialchars($product['stock']) ?> ชิ้น</p>
            </div>

            <!-- ฟอร์มสั่งซื้อ -->
            <?php if ($isLoggedIn): ?>
            <form action="cart.php" method="post" class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <label for="quantity" class="me-2 mb-0">จำนวน:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                <button type="submit" class="btn btn-gradient"><i class="bi bi-cart-plus"></i> เพิ่มในตะกร้า</button>
            </form>
            <?php else: ?>
            <div class="alert alert-info mt-3">กรุณาเข้าสู่ระบบเพื่อสั่งซื้อสินค้า</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
