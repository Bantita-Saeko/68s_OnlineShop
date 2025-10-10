<?php

session_start();
require 'config.php';
require 'session_timeout.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit;
}
$user_id = $_SESSION['user_id'];
$errors = [];


$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id, products.product_name,
products.price, products.image , products.description
                    FROM cart
                    JOIN products ON cart.product_id = products.product_id
WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price']; 
}



$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']); 
    $city = trim($_POST['city']); 
    $postal_code = trim($_POST['postal_code']); 
    $phone = trim($_POST['phone']); 

    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบถ้วน"; 
    }
    if (empty($errors)) {
        
        $conn->beginTransaction();
        try {
            
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $conn->lastInsertId();

            
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                
            }
            
            $stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $address, $city, $postal_code, $phone]);

           
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            
            $conn->commit();
            header("Location: order.php?success=1"); 
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}

$img = !empty($product['image'])
    ? 'product_images/' . rawurlencode($product['image'])
    : 'product_images/no-image.png';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>สั่งซื้อสินค้า</title>
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
            font-weight: bold;
            color: #6c63ff;
        }

        label {
            font-weight: 500;
        }

        .table thead th {
            background: #dadadaff;
            color: #181818ff;
        }

        .bg-gradient {
            background-color: #a2cde1ff;
        }

        .text-gradientSmall {
            background: linear-gradient(90deg, #80c2ffff, #a7c5ffff, #cdb4db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        img {
            max-width: 8%;
            border-radius: 12px;
            box-shadow: 1px 1px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            margin-right: 10px;
        }
        .text-secondary1 {
            color: #bebebeff;
        }
    </style>

</head>

<body class="bg-light">
    <div class="container mb-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-gradient text-white py-3">
                        <h3 class="mb-0"><i class="bi bi-bag-check-fill me-2"></i>ยืนยันการสั่งซื้อ</h3>
                    </div>

                    <div class="card-body p-5">

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger pb-0 rounded-3 shadow-sm">
                                <ul class="mb-2">
                                    <?php foreach ($errors as $e): ?>
                                        <li><?= htmlspecialchars($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- รายการสินค้า -->
                        <h5 class="fw-bold text-gradientSmall mb-3">
                            <i class="bi bi-cart4 me-1"></i> รายการสินค้าในตะกร้า
                        </h5>
                        <div class="table-responsive">
                            <table class="table align-middle border rounded-3 overflow-hidden shadow-sm">
                                <thead class="table-success">
                                    <tr>
                                        <th>สินค้า</th>
                                        <th class="text-center">จำนวน</th>
                                        <th class="text-end">ราคารวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td style="width: 80%;">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="product_images/<?= htmlspecialchars($item['image']) ?>"
                                                            alt="<?= htmlspecialchars($item['product_name']) ?>" width="60"
                                                            height="60" class="rounded">
                                                    <?php else: ?>
                                                        <img src="product_images/no-image.png" alt="ไม่มีรูปภาพ" width="60"
                                                            height="60" class="rounded">
                                                    <?php endif; ?>
                                                    <div class="d-flex flex-column">
                                                        <span><?= htmlspecialchars($item['product_name']) ?></span>
                                                        <span class="text-secondary1"><?= htmlspecialchars($item['description']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">× <?= $item['quantity'] ?></td>
                                            <td class="text-end fw-semibold text-dark">
                                                <?= number_format($item['quantity'] * $item['price'], 2) ?> บาท
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold">
                                        <td colspan="2" class="text-end">รวมทั้งสิ้น :</td>
                                        <td class="text-end text-danger fs-5">
                                            <?= number_format($total, 2) ?> บาท
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4">

                        <!-- ข้อมูลการจัดส่ง -->
                        <h5 class="fw-bold text-gradientSmall mb-3">
                            <i class="bi bi-truck me-1"></i> ข้อมูลการจัดส่ง
                        </h5>
                        <form method="post" class="row g-4">
                            <div class="col-md-6">
                                <label for="address" class="form-label">ที่อยู่</label>
                                <input type="text" name="address" id="address" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">จังหวัด</label>
                                <input type="text" name="city" id="city" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-2">
                                <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control shadow-sm"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" id="phone" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <a href="cart.php" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-arrow-left-circle me-1"></i> กลับตะกร้า
                                </a>
                                <button type="submit" class="btn btn-gradient btn-edit px-5 shadow">
                                    <i class="bi bi-check2-circle me-1"></i> ยืนยันการสั่งซื้อ
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</body>



</html>