<?php
require '../config.php'; 
require 'authen_admin.php';
require '../session_timeout.php';

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<h3>ไม่พบสมาชิก</h3>";
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($username === '' || $email === '') {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    if (!$error) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $chk->execute([$username, $email, $user_id]);
        if ($chk->fetch()) {
            $error = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้วในระบบ";
        }
    }

    $updatePassword = false;
    $hashed = null;
    if (!$error && ($password !== '' || $confirm !== '')) {
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องยาวอย่างน้อย 6 อักขระ";
        } elseif ($password !== $confirm) {
            $error = "รหัสผ่านใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    if (!$error) {
        if ($updatePassword) {
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?, password = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $user_id];
        }
        $upd = $conn->prepare($sql);
        $upd->execute($args);
        header("Location: users.php?updated=1");
        exit;
    }

    $user['username'] = $username;
    $user['full_name'] = $full_name;
    $user['email'] = $email;
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
            <h2 class="text-gradient"><i class="bi bi-people"></i> แก้ไขข้อมูลสมาชิก</h2>
            <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required
                    value="<?= htmlspecialchars($user['username']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">ชื่อ - นามสกุล</label>
                <input type="text" name="full_name" class="form-control"
                    value="<?= htmlspecialchars($user['full_name']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required
                    value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="col-md-6"></div>

            <div class="col-md-6">
                <label class="form-label">รหัสผ่านใหม่
                    <small class="text-muted">(ถ้าไม่ต้องการเปลี่ยน ให้เว้นว่าง)</small>
                </label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-gradient btn-edit px-4 py-3">
                    <i class="bi bi-floppy-fill text-white"></i> บันทึกการแก้ไข</button>
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
