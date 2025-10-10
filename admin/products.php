<?php
require '../config.php';
require 'authen_admin.php';
require '../session_timeout.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    if ($name && $price > 0) {
        $imageName = null;
        if (!empty($_FILES['product_image']['name'])) {
            $file = $_FILES['product_image'];
            $allowed = ['image/jpeg', 'image/png'];

            if (in_array($file['type'], $allowed)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $imageName = 'product_' . time() . '.' . $ext;
                $path = __DIR__ . '/../product_images/' . $imageName;
                move_uploaded_file($file['tmp_name'], $path);
            }
        }

        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category_id, image)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName]);
        header("Location: products.php?added=1");
        exit;
    }
}


if (isset($_GET['delete'])) {
    $product_id = (int) $_GET['delete'];
    $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $imageName = $stmt->fetchColumn();

    try {
        $conn->beginTransaction();
        $del = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $del->execute([$product_id]);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: products.php");
        exit;
    }

    if ($imageName) {
        $baseDir = realpath(__DIR__ . '/../product_images');
        $filePath = realpath($baseDir . '/' . $imageName);
        if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    header("Location: products.php?deleted=1");
    exit;
}


$stmt = $conn->query("
    SELECT 
        p.*, 
        c.category_name, 
        ROUND(AVG(r.rating), 1) AS avg_rating,
        COUNT(r.review_id) AS total_reviews
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN reviews r ON p.product_id = r.product_id
    GROUP BY p.product_id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalProducts = count($products);


$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
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

        .btn-delete {
            background: linear-gradient(135deg, #f7a3adff, #f68594ff);
            color: white;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s;
            border: none;
        }

        .btn-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .stat-card {
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .rating i {
            color: #ffc107;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
            <h2 class="text-gradient"><i class="bi bi-box-seam"></i> จัดการสินค้า</h2>
            <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
        </div>

        <div class="stat-card mb-4">
            <i class="bi bi-box-seam stat-icon text-white"></i>
            <div class="fs-4 fw-bold text-white">จำนวนสินค้าทั้งหมด : <?= $totalProducts ?></div>
        </div>

        <!-- เพิ่มสินค้าใหม่ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>เพิ่มสินค้าใหม่</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="product_name" class="form-control" placeholder="ชื่อสินค้า" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="ราคา" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="stock" class="form-control" placeholder="จำนวน" required>
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" class="form-select" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <textarea name="description" class="form-control" placeholder="รายละเอียดสินค้า" rows="2"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">รูปสินค้า (jpg, png)</label>
                        <input type="file" name="product_image" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="add_product" class="btn btn-gradient btn-edit">+ เพิ่มสินค้า</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- รายการสินค้า -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>รายการสินค้า</h5>
            </div>
            <div class="card-body">
                <?php if (count($products) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อสินค้า</th>
                                    <th>หมวดหมู่</th>
                                    <th>ราคา</th>
                                    <th>คงเหลือ</th>
                                    <th>รูปสินค้า</th>
                                    <th>รีวิว</th>
                                    <th width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['product_name']) ?></td>
                                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                                        <td><?= number_format($p['price'], 2) ?> บาท</td>
                                        <td><?= $p['stock'] ?></td>
                                        <td>
                                            <?php if ($p['image']): ?>
                                                <img src="../product_images/<?= htmlspecialchars($p['image']) ?>" width="50" height="50" class="rounded">
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มีรูป</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="rating">
                                            <?php if ($p['avg_rating']): ?>
                                                <?php
                                                $stars = floor($p['avg_rating']);
                                                $half = ($p['avg_rating'] - $stars >= 0.5);
                                                for ($i = 1; $i <= 5; $i++):
                                                    if ($i <= $stars) echo '<i class="bi bi-star-fill"></i>';
                                                    elseif ($half && $i == $stars + 1) echo '<i class="bi bi-star-half"></i>';
                                                    else echo '<i class="bi bi-star"></i>';
                                                endfor;
                                                ?>
                                                <br><small class="text-muted"><?= $p['avg_rating'] ?> จาก <?= $p['total_reviews'] ?> รีวิว</small>
                                            <?php else: ?>
                                                <span class="text-muted">ยังไม่มีรีวิว</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                           <a href="edit_products.php?id=<?= $p['product_id'] ?>"
                                                class="btn btn-gradient btn-edit btn-sm px-2 me-2"><i
                                                    class="bi bi-pencil-fill"></i>แก้ไข</a>
                                            <button onclick="confirmDelete(<?= $p['product_id'] ?>)"
                                                class="btn btn-delete btn-sm px-2 me-2"><i class="bi bi-trash"></i>ลบ</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">ยังไม่มีสินค้า</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "การลบนี้ไม่สามารถกู้คืนได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "products.php?delete=" + id;
                }
            })
        }

        <?php if (isset($_GET['added'])): ?>
            Swal.fire("เพิ่มสินค้าเรียบร้อย!", "", "success");
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            Swal.fire("ลบสินค้าเรียบร้อย!", "", "success");
        <?php endif; ?>
    </script>
</body>
</html>
