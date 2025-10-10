<?php
    session_start();
    require 'config.php';
    require 'function.php';
    require 'session_timeout.php';

    if (!isset($_SESSION['user_id'])) { 
        header("Location: login"); 
        exit;
    }
    
    $user_id = $_SESSION['user_id']; 

    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC"); 
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ประวัตการสั่งซื้อ </title>
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
        .bg-gradient {
            background-color: #a2cde1ff ;
        }
    </style>
</head>

<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class=" text-gradient"><i class="bi bi-clock-history"></i> ประวัติการสั่งซื้อ</h2>
        <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าหลัก</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success shadow-sm">
            ✅ ทำรายการสั่งซื้อเรียบร้อยแล้ว
        </div>
    <?php endif; ?>

    <?php if (count($orders) === 0): ?>
        <div class="alert alert-warning shadow-sm">
            คุณยังไม่เคยสั่งซื้อสินค้า 🛒
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card border-0 shadow-lg mb-4 rounded-3">
                <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>รหัสคำสั่งซื้อ:</strong> #<?= $order['order_id'] ?>
                        <span class="ms-3"><strong>วันที่:</strong> <?= $order['order_date'] ?></span>
                    </div>
                    <span class="badge 
                        <?php if ($order['status'] == 'pending') echo 'bg-warning text-dark';
                              elseif ($order['status'] == 'completed') echo 'bg-success';
                              elseif ($order['status'] == 'cancelled') echo 'bg-danger';
                              else echo 'bg-secondary'; ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-secondary">รายละเอียดสินค้า</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">ราคา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td class="text-center">× <?= $item['quantity'] ?></td>
                                        <td class="text-end">
                                            <?= number_format($item['price'] * $item['quantity'], 2) ?> บาท
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <p class="fw-bold text-danger mb-0">
                            💰 รวมทั้งสิ้น: <?= number_format($order['total_amount'], 2) ?> บาท
                        </p>
                    </div>

                    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
                    <?php if ($shipping): ?>
                        <hr>
                        <h6 class="text-secondary">ข้อมูลการจัดส่ง</h6>
                        <p class="mb-1"><strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>,
                            <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?>
                        </p>
                        <p class="mb-1"><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                        <p class="mb-0">
                            <strong>สถานะการจัดส่ง:</strong>
                            <span class="badge 
                                <?php if ($shipping['shipping_status'] == 'กำลังจัดส่ง') echo 'bg-info';
                                      elseif ($shipping['shipping_status'] == 'จัดส่งสำเร็จ') echo 'bg-success';
                                      else echo 'bg-secondary'; ?>">
                                <?= ucfirst($shipping['shipping_status']) ?>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>