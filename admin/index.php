<?php
require '../config.php';
require 'authen_admin.php';

$isLoggedIn = isset($_SESSION['user_id']);

// ดึงจำนวนข้อมูลแต่ละตาราง
$stmt = $conn->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM orders");
$totalOrders = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role='member'");
$totalUsers = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(*) FROM categories");
$totalCategories = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e6f2ff);
        }
        .text-custom { color: #5b5bff; }
        .welcome-text { font-weight: 500; }

        /* Card Main */
        .main-card {
            background-color: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        /* Gradient Buttons Pastel */
        .btn-gradient {
            color: white;
            font-weight: 500;
            border-radius: 12px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.3s;
            border: none;
        }
        .btn-products { background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff); }
        .btn-orders { background: linear-gradient(135deg, #ffdde1, #ee9ca7); }
        .btn-users { background: linear-gradient(135deg, #cdb4db, #e3c8f2); }
        .btn-categories { background: linear-gradient(135deg, #fff1b0, #ffe0ac); }

        .btn-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .btn-icon { margin-right: 8px; font-size: 1.3rem; }

        /* Logout button */
        .btn-logout { background-color: #cc3d3d; border: none; transition: all 0.3s; }
        .btn-logout:hover { background-color: #b11a1a; }

        /* Stat Cards */
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-products { background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff); }
        .stat-orders { background: linear-gradient(135deg, #ffdde1, #ee9ca7); }
        .stat-users { background: linear-gradient(135deg, #cdb4db, #e3c8f2); }
        .stat-categories { background: linear-gradient(135deg, #fff1b0, #ffe0ac); }

        .stat-icon { font-size: 2rem; margin-bottom: 10px; }
        .stat-number { font-size: 1.5rem; font-weight: bold; }

        .text-gradient-shadow {
        background: linear-gradient(90deg, #80c2ffff, #a7c5ffff, #cdb4db);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 2.2rem;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.15);
}

    </style>
</head>
<body class="container py-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gradient-shadow">ระบบผู้ดูแลระบบ</h2>
        <div>
            <?php if ($isLoggedIn): ?>
                <span class="me-3 welcome-text">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
                <a href="../logout.php" class="btn btn-logout rounded-pill"><i class="bi bi-box-arrow-right pe-1"></i>ออกจากระบบ</a>
            <?php else: ?>
                <a href="../login.php" class="btn btn-gradient btn-products rounded-pill me-2">เข้าสู่ระบบ</a>
                <a href="../register.php" class="btn btn-outline-primary rounded-pill">สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-products">
                <i class="bi bi-box-seam stat-icon"></i>
                <div>สินค้า</div>
                <div class="stat-number"><?= $totalProducts ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-orders">
                <i class="bi bi-cart-check stat-icon"></i>
                <div>คำสั่งซื้อ</div>
                <div class="stat-number"><?= $totalOrders ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-users">
                <i class="bi bi-people stat-icon"></i>
                <div>สมาชิก</div>
                <div class="stat-number"><?= $totalUsers ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-categories">
                <i class="bi bi-tags stat-icon"></i>
                <div>หมวดหมู่</div>
                <div class="stat-number"><?= $totalCategories ?></div>
            </div>
        </div>
    </div>

    <!-- Dashboard Buttons -->
    <div class="main-card">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="products.php" class="btn btn-gradient btn-products w-100">
                    <i class="bi bi-box-seam btn-icon"></i>จัดการสินค้า
                </a>
            </div>
            <div class="col-md-3">
                <a href="orders.php" class="btn btn-gradient btn-orders w-100">
                    <i class="bi bi-cart-check btn-icon"></i>จัดการคำสั่งซื้อ
                </a>
            </div>
            <div class="col-md-3">
                <a href="users.php" class="btn btn-gradient btn-users w-100">
                    <i class="bi bi-people btn-icon"></i>จัดการสมาชิก
                </a>
            </div>
            <div class="col-md-3">
                <a href="category.php" class="btn btn-gradient btn-categories w-100">
                    <i class="bi bi-tags btn-icon"></i>จัดการหมวดหมู่
                </a>
            </div>
        </div>
    </div>

</body>
</html>
