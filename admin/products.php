<?php
require '../config.php';
require 'authen_admin.php';

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id]);
        header("Location: products.php?added=1");
        exit;
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php?deleted=1");
    exit;
}

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.category_id 
                      ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProducts = count($products);

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CDN Google-Fonts -->
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

        .main-card {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
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
        
        .btn-delete:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
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

    <!-- การ์ดเพิ่มสินค้าใหม่ -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>เพิ่มสินค้าใหม่</h5>
        </div>
        <div class="card-body">
            <form method="post" class="row g-3">
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
                <div class="col-12 text-end">
                    <button type="submit" name="add_product" class="btn btn-gradient btn-edit">+ เพิ่มสินค้า</button>
                </div>
            </form>
        </div>
    </div>

    <!-- การ์ดแสดงสินค้า -->
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
                                    <a href="edit_products.php?id=<?= $p['product_id'] ?>" class="btn btn-gradient btn-edit btn-sm px-2 me-2"><i class="bi bi-pencil-fill"></i>แก้ไข</a>
                                    <button onclick="confirmDelete(<?= $p['product_id'] ?>)" class="btn btn-delete btn-sm px-2 me-2"><i class="bi bi-trash"></i>ลบ</button>
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

// แสดง SweetAlert เมื่อเพิ่มหรือลบเสร็จ
<?php if (isset($_GET['added'])): ?>
Swal.fire("เพิ่มสินค้าเรียบร้อย!", "", "success");
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
Swal.fire("ลบสินค้าเรียบร้อย!", "", "success");
<?php endif; ?>
</script>
</body>
</html>
