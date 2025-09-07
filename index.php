<?php
session_start();
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>รายการสินค้า</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css">
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
}
.card {
    border-radius: 15px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.btn-gradient {
    background: #14c40eff; 
    border: 1px solid #ffffffff; 
    color: #ffffffff; 
    transition: all 0.3s; 
    font-weight: 500;
}
.btn-gradient:hover {
    background: #0a9706ff;
    color: white;
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
.card-subtitle {
    font-size: 0.9rem;
}
.float-end { float: right; }
</style>
</head>
<body class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-gradient"><i class="bi bi-box-seam"></i> รายละเอียดสินค้า</h1>
    <div>
        <?php if ($isLoggedIn): ?>
            <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
            <a href="profile.php" class="btn btn-outline-custom me-2"><i class="bi bi-person-circle"></i> ข้อมูลส่วนตัว</a>
            <a href="cart.php" class="btn btn-outline-custom me-2"><i class="bi bi-cart"></i> ดูตะกร้า</a>
            <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-gradient me-2">เข้าสู่ระบบ</a>
            <a href="register.php" class="btn btn-outline-custom">สมัครสมาชิก</a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
<?php foreach ($products as $product): ?>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p class="fw-bold">ราคา: <?= number_format($product['price'], 2) ?> บาท</p>
                <div class="mt-auto d-flex justify-content-between">
                    <?php if ($isLoggedIn): ?>
                    <form action="cart.php" method="post" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-sm btn-gradient"><i class="bi bi-cart-plus"></i> เพิ่มในตะกร้า</button>
                    </form>
                    <?php else: ?>
                        <small class="text-muted align-self-center">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                    <?php endif; ?>
                    <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-outline-custom float-end"><i class="bi bi-eye"></i> ดูรายละเอียด</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
