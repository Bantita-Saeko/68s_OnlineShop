<?php
require '../config.php';
require 'authen_admin.php';
require '../session_timeout.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = ($_GET['id']);


$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<h3>ไม่พบข้อมูลของสินค้า</h3>";
    exit;
}


$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];

    $oldImage = $_POST['old_image'] ?? null;
    $removeImage = isset($_POST['remove_image']); 

    if ($name && $price > 0) {
        $newImageName = $oldImage; 
        if ($removeImage) {
            $newImageName = null;
        }
    }

    if (!empty($_FILES['product_image']['name'])) {
        $file = $_FILES['product_image'];
        $allowed = ['image/jpeg', 'image/png'];
        if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newImageName = 'product_' . time() . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../product_images');
            $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $newImageName = $oldImage;
            }
        }
    }

    $sql = "UPDATE products
            SET product_name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
            WHERE product_id = ?";
    $args = [$name, $description, $price, $stock, $category_id, $newImageName, $product_id];
    $stmt = $conn->prepare($sql);
    $stmt->execute($args);

    if (!empty($oldImage) && $oldImage !== $newImageName) {
        $baseDir = realpath(__DIR__ . '/../product_images');
        $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
        if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath);
        }
    }
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>

    <!-- CDN Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e6f2ff);
        }

        .text-gradient {
            background: linear-gradient(90deg, #80c2ff, #a7c5ff, #cdb4db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 2rem;
        }

        .btn-gradient {
            color: white;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s;
            border: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, #bdd1db, #a2cde1);
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
    </style>
</head>

<body class="container py-4">

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-gradient"><i class="bi bi-box-seam"></i> แก้ไขข้อมูลสินค้า</h2>
            <a href="products.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
        </div>

        <form enctype="multipart/form-data" method="POST" id="editForm">
            <div class="mb-3">
                <label class="form-label">ชื่อสินค้า</label>
                <input type="text" name="product_name" class="form-control"
                    value="<?= htmlspecialchars($product['product_name']) ?>" required>
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
                <div class="mb-3">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" class="form-control"
                        rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">อัปโหลดรูปใหม่ (jpg, png)</label>
                    <input type="file" name="product_image" id="product_image" class="form-control">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">ลบรูปเดิม</label>
                    </div>
                </div>
                <div class="col-md-6 mt-5">
                    <label class="form-label d-block">รูปปัจจุบัน</label>
                    <?php if (!empty($product['image'])): ?>
                        <img src="../product_images/<?= htmlspecialchars($product['image']) ?>" width="120" height="120"
                            class="rounded mb-2">
                    <?php else: ?>
                        <span class="text-muted">ไม่มีรูป</span>
                    <?php endif; ?>
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
                </div>
            </div>
            <div class="col-12 text-start mt-3">
                <button type="submit" class="btn btn-gradient btn-edit px-4 py-3">
                    <i class="bi bi-floppy-fill text-white"></i> บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelector("#editForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const form = this;
            const name = form.product_name.value;
            const price = form.price.value;
            const stock = form.stock.value;
            const category = form.category_id.options[form.category_id.selectedIndex].text;
            const description = form.description.value;
            const removeImage = form.remove_image.checked ? "ลบรูปเดิม" : "คงไว้";

            const fileInput = document.getElementById("product_image");
            const file = fileInput.files[0];

            let readerPromise = Promise.resolve(null);

            if (file) {
                readerPromise = new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(file);
                });
            }

            readerPromise.then((imgData) => {
                let previewHtml = `
                    <p><strong>ชื่อสินค้า:</strong> ${name}</p>
                    <p><strong>ราคา:</strong> ${price}</p>
                    <p><strong>จำนวนคงเหลือ:</strong> ${stock}</p>
                    <p><strong>หมวดหมู่:</strong> ${category}</p>
                    <p><strong>รายละเอียด:</strong><br>${description || "-"}</p>
                    <p><strong>การจัดการรูปเดิม:</strong> ${removeImage}</p>
                `;

                if (file) {
                    previewHtml += `<p><strong>รูปใหม่:</strong><br><img src="${imgData}" width="150" class="rounded shadow"></p>`;
                } else {
                    previewHtml += `<p><strong>รูปใหม่:</strong> ไม่มีการเลือก</p>`;
                }

                Swal.fire({
                    title: "ตรวจสอบข้อมูลก่อนบันทึก",
                    html: previewHtml,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "บันทึก",
                    cancelButtonText: "แก้ไขต่อ"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
