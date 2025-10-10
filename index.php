<?php
session_start();
require_once 'config.php';
require 'session_timeout.php';

$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT p.*, c.category_name, 
                             AVG(r.rating) AS average_rating
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      LEFT JOIN reviews r ON p.product_id = r.product_id  -- *เพิ่ม JOIN กับตาราง reviews*
                      GROUP BY p.product_id, p.product_name, p.price, p.description, p.image, p.stock, p.created_at, c.category_name 
                      -- *GROUP BY ต้องรวมคอลัมน์ทั้งหมดจากตาราง p และ c*
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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">

    <!-- CDN Boostrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .product-card {
            border: 1;
            background: #fff;
        }

        .product-thumb {
            height: 250px;
            object-fit: cover;
            border-radius: .5rem;
        }

        .product-meta {
            font-size: .75rem;
            letter-spacing: .05em;
            color: #8a8f98;
            text-transform: uppercase;
        }

        .product-title {
            font-size: 1rem;
            margin: .25rem 0 .5rem;
            font-weight: 600;
            color: #222;
        }

        .price {
            font-weight: 700;
        }

        .rating i {
            color: #ffc107;
        }

        /* ดำวสที อง */
        .wishlist {
            color: #b9bfc6;
        }

        .wishlist:hover {
            color: #ff5b5b;
        }

        .badge-top-left {
            position: absolute;
            top: .5rem;
            left: .5rem;
            z-index: 2;
            border-radius: .375rem;
        }

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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-gradient {
            background: #86c0fd;
            border: 1px solid #ffffffff;
            color: #ffffffff;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-gradient:hover {
            background: #56a8ffff;
            color: white;
        }

        .btn-custom {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
        }

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

        .float-end {
            float: right;
        }

        .img-frame {
            border: 2px solid #edf4ffff;
            border-radius: 8px;
            padding: 2px;
            box-shadow: 1px 1px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #80c2ff, #a7c5ff);
            color: white;
            border: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }
        .bg-success-custom {
            background-color: #75d85eff ;
        }
        /* เพิ่ม CSS นี้ */
        .card.out-of-stock {
            opacity: 0.6; /* ทำให้การ์ดจางลง 40% */
            filter: grayscale(80%); /* (ไม่บังคับ) ทำให้ภาพเป็นสีเทาเล็กน้อยเพื่อเน้น */
        }
        
        .card.out-of-stock .btn-gradient,
        .card.out-of-stock .btn-outline-primary,
        .card.out-of-stock a {
            pointer-events: none; /* ทำให้คลิกปุ่ม/ลิงก์ไม่ได้ */
            cursor: default;
        }

        .card.out-of-stock .product-thumb {
            filter: brightness(0.8); /* (ไม่บังคับ) ทำให้รูปภาพดูมืดลง */
        }
        .card .badge {
            z-index: 10; /* กำหนดค่า z-index ให้สูงพอ */
            border-radius: .375rem; /* รักษารูปแบบเดิม */
        }
        
    </style>
</head>

<body class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-gradient"><i class="bi bi-box-seam"></i> รายการสินค้า</h1>

        <div>
            <?php if ($isLoggedIn): ?>
                <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> <!-- เปลี่ยนเป็น Fullname -->
                    (<?= $_SESSION['role'] ?>)</span>
                <a href="profile.php" class="btn btn-outline-custom me-2"><i class="bi bi-person-circle"></i>
                    ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-outline-custom me-2"><i class="bi bi-cart"></i> ดูตะกร้า</a>
                    <a href="order.php" class="btn btn-outline-custom me-2"><i class="bi bi-clock-history"></i>
                        ดูประวัติการสั่งซื้อ</a>
                <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-gradient me-2">เข้าสู่ระบบ</a>
                <a href="register.php" class="btn btn-outline-custom">สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class=" d-flex gap-2 me-auto">
                <a href="index.php" class="btn btn-gradient active">
                    <i class="bi bi-grid-fill"></i> สินค้า
                </a>
                <a href="review.php" class="btn btn-outline-custom">
                    <i class="bi bi-chat-square-text-fill"></i> รีวิว
                </a>
            </div>
    </div>

    <div class="row g-4">
        <?php foreach ($products as $product):
            
            $img = !empty($product['image']) ? 'product_images/' . rawurlencode($product['image']) : 'product_images/no-image.png';
            
            $isNew = isset($product['created_at']) && (time() - strtotime($product['created_at']) <= 7 * 24 * 3600);
            $isHot = (int) $product['stock'] > 0 && (int) $product['stock'] < 5;
            $isRunOut = (int) $product['stock'] <= 0;
            
            $rating = isset($product['average_rating']) && $product['average_rating'] !== null 
                      ? (float) $product['average_rating'] 
                      : 2.5;
                          
             $full = floor($rating);
            $full = floor($rating);
            $half = ($rating - $full) >= 0.5 ? 1 : 0;
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 position-relative <?= $isRunOut ? 'out-of-stock' : '' ?>"> 
                    <?php if ($isNew): ?>
                        <span class="badge bg-success-custom position-absolute top-0 start-0 m-2">NEW</span>
                    <?php elseif ($isHot): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">HOT</span>
                    <?php elseif ($isRunOut): ?>
                        <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Out of Stock</span>
                    <?php endif; ?>

                    <a href="product_detail.php?id=<?= (int) $product['product_id'] ?>" class="d-block p-3">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>"
                            class="img-fluid w-100 product-thumb rounded img-frame">
                    </a>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?= htmlspecialchars($product['category_name'] ?? 'Category') ?></h6>
                        <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                        <div class="rating mb-2">
                            <?php for ($i = 0; $i < $full; $i++): ?><i class="bi bi-star-fill text-warning"></i><?php endfor; ?>
                            <?php if ($half): ?><i class="bi bi-star-half text-warning"></i><?php endif; ?>
                            <?php for ($i = 0; $i < 5 - $full - $half; $i++): ?><i class="bi bi-star text-warning"></i><?php endfor; ?>
                        </div>

                        <p class="fw-bold">ราคา: <?= number_format($product['price'], 2) ?> บาท</p>

                        <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                            <?php if ($isLoggedIn): ?>
                                <form action="cart.php" method="post" class="d-inline-flex gap-2">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-gradient"><i class="bi bi-cart-plus"></i>
                                        เพิ่มในตะกร้า</button>
                                </form>
                            <?php else: ?>
                                <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                            <?php endif; ?>
                            <a href="product_detail.php?id=<?= (int) $product['product_id'] ?>"
                                class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>