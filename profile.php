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
$success = "";

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email)) {
        $errors[] = "กรณุณากรอกชื่อ -นามสกุลและอีเมล";
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "อีเมลนี้ถูก ใช้งานนแลว้";
    }

    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "รหัสผ่ำนเดิมไม่ถูกต ้อง";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "รหัสผ่ำนใหม่ต ้องมีอย่ำงน้อย 6 ตัวอักษร";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "รหัสผ่ำนใหม่และกำรยืนยันไม่ตรงกัน";
        } else {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    if (empty($errors)) {
        if (!empty($new_hashed)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $new_hashed, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }
        $success = "บันทึกข ้อมูลเรียบร ้อยแล้ว";

        $_SESSION['username'] = $user['username'];
        $user['full_name'] = $full_name;
        $user['email'] = $email;
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์สมาชิก</title>
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
        .btn-edit { background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff); }
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
            font-weight: bold; color: #6c63ff; 
        }
        label { 
            font-weight: 500; 
        }
    </style>
</head>

<body class="container py-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-gradient"><i class="bi bi-person-circle me-2"></i>โปรไฟล์ของคุณ</h2>
            <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าหลัก</a>
        </div>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label for="full_name" class="form-label">ชื่อ -นามสกุล</label>
                <input type="text" name="full_name" class="form-control" required value="<?=
                    htmlspecialchars($user['full_name']) ?>">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required value="<?=
                    htmlspecialchars($user['email']) ?>">
            </div>
            <div class="col-12">
                <hr>
                <h5>เปลี่ยนรหัสผ่าน (ไม่จำเป็น)</h5>
            </div>
            <div class="col-md-6">
                <label for="current_password" class="form-label">รหัสผ่านเดิม</label>
                <input type="password" name="current_password" id="current_password" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="new_password" class="form-label">รหัสผ่านใหม่ (ต้องมากกว่าหรือเท่ากับ 6 ตัวอักษร)</label>
                <input type="password" name="new_password" id="new_password" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
            </div>
            <div class="col-12 text-center">
                <button type="submit-" class="btn btn-gradient btn-edit px-4 py-3"><i class="bi bi-floppy-fill text-white me-2"></i>บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>
    </div>
     <script>
    document.querySelector("form").addEventListener("submit", function(e) {
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