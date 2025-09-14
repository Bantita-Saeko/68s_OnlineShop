<?php
require '../config.php';
require 'authen_admin.php';

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories(category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        $_SESSION['success'] = "เพิ่มหมวดหมู่เรียบร้อยแล้ว 🎉";
        header("Location: category.php");
        exit;
    }
}

// ลบหมวดหมู่
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีสินค้าที่ใช้งานอยู่";
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว 🗑️";
    }
    header("Location: category.php");
    exit;
}

// แก้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['new_name']);
    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        $_SESSION['success'] = "แก้ไขหมวดหมู่เรียบร้อยแล้ว ✏️";
        header("Location: category.php");
        exit;
    }
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);

$totalCategories = count($categories);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่</title>
     <!-- CDN Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CDN Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">

    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', 'Nunito';
            background: linear-gradient(135deg, #f8f9fa, #e6f2ff);
            margin-top: 20px;
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

        .btn-delete:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }
        .card { 
            border-radius: 15px; 
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); 
            margin-top: 50px;
        }
        label { 
            font-weight: 500; 
        }
        .bg-custom {
            background: linear-gradient(135deg, #fffddcff, #ffeea2ff); 
        }
        .stat-card {
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, #fff1b0, #ffe0ac);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
    </style>
</head>

<body class="bg-light py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-gradient"><i class="bi bi-tag"></i> จัดการหมวดหมู่สินค้า</h2>
            <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
        </div>

        <div class="stat-card mb-4">
            <i class="bi bi-tags stat-icon text-black"></i>
            <div class="fs-4 fw-bold text-black">จำนวนหมวดหมู่ทั้งหมด : <?= $totalCategories ?></div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>เพิ่มหมวดหมู่ใหม่</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="category_name" class="form-control" placeholder="กรอกชื่อหมวดหมู่ใหม่" required>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" name="add_category" class="btn btn-gradient btn-edit">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>รายการหมวดหมู่</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อหมวดหมู่</th>
                            <th style="width: 50%;">แก้ไขชื่อ</th>
                            <th style="width: 17%;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                <td>
                                    <form method="post" class="d-flex">
                                        <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                        <input type="text" name="new_name" class="form-control me-2" placeholder="ชื่อใหม่" required>
                                        <button type="submit" name="update_category" class="btn btn-sm btn-gradient btn-edit align-items-center d-flex"><i class="bi bi-pencil-fill mx-1"></i>แก้ไข</button>
                                    </form>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-delete p-2 w-100"
                                        onclick="confirmDelete(<?= $cat['category_id'] ?>)"><i class="bi bi-trash mx-1"></i>ลบ</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($categories) === 0): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">ยังไม่มีหมวดหมู่</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // แสดง SweetAlert2 สำหรับ error/success
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: '<?= $_SESSION['error'] ?>'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= $_SESSION['success'] ?>'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        // Popup ยืนยันก่อนลบ
        function confirmDelete(id) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบหมวดหมู่นี้จริง ๆ หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "category.php?delete=" + id;
                }
            });
        }
    </script>
</body>

</html>
