<?php
require '../config.php';
require 'authen_admin.php';

// ตรวจสอบว่ามี id มั้ย
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// อัปเดตสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE products SET product_name=?, description=?, price=?, stock=?, category_id=? WHERE product_id=?");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $product_id]);

        header("Location: products.php?updated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขสมาชิก</title>

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
    </style>
</head>

<body class="container py-4">

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-gradient"><i class="bi bi-box-seam"></i> แก้ไขข้อมูลสินค้า</h2>
            <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" id="editForm">
            <div class="mb-3">
                <label class="form-label">ชื่อสินค้า</label>
                <input type="text" name="product_name" class="form-control"
                    value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รายละเอียด</label>
                <textarea name="description" class="form-control"
                    rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>"
                        required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">จำนวนคงเหลือ</label>
                    <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">หมวดหมู่</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-gradient btn-edit px-4 py-3">
                    <i class="bi bi-floppy-fill text-white"></i> บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function (e) {
            e.preventDefault();
            Swal.fire({
                title: "คุณต้องการบันทึกการแก้ไขหรือไม่?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "บันทึก",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>