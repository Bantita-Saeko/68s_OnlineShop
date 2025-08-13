<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $fullname, $email, $hashedPassword]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Kanit', 'Nunito';
            background-color: #e6f2ff;
        }
        .container {
            max-width: 900px;
            border-radius: 20px;
            overflow: hidden;
        }
        .left-side {
            background-image: url("img/bg.jpg");
            color: white;
            padding: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            h2 {
                font-weight: bold;
                color: #87bef8ff;
            }
            img {
                max-width: 100%;
                border-radius: 10px;
            }
        }
        .right-side {
            background-color: #f0f8ff;
            padding: 50px;
            h2 {
                font-weight: bold;
                color: #9bcbffff;
            }
        }
        .form-control {
            border: 1px solid #d9d9d9ff;
        }
        .btn-custom {
            background-color: #86c0fdff;
            border: none;
        }
        .btn-outline-custom {
            background-color: #ffffffff;
            border: 1px solid #86c0fdff;
        }
        .btn-custom:hover {
            background-color: #ffffffff;
            border: 1px solid #86c0fdff;
        }
        .btn-outline-custom:hover {
            background-color: #73b7ffff;
        }
    </style>
</head>
<body>
    <div class="container mt-5 w-50">
        <div class="row g-0">
            <!-- ซ้าย -->
            <div class="col-md-5 left-side text-center">
                <h2>Welcome!</h2>
                <p>สมัครสมาชิก เพื่อเริ่มต้นใช้งาน</p>
            </div>

            <!-- ขวา -->
            <div class="col-md-7 right-side">
                <h2 class="mb-4 text-center">สมัครสมาชิก</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" name="username" id="username" class="form-control rounded-pill" placeholder="กรุณากรอกชื่อผู้ใช้" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">ชื่อ-สกุล</label>
                        <input type="text" name="fullname" id="fullname" class="form-control rounded-pill" placeholder="กรุณากรอกชื่อ-สกุล" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" name="email" id="email" class="form-control rounded-pill" placeholder="กรุณากรอกอีเมล" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" name="password" id="password" class="form-control rounded-pill" placeholder="กรุณากรอกรหัสผ่าน" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmpassword" class="form-label">ยืนยันรหัสผ่าน</label>
                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control rounded-pill" placeholder="กรุณายืนยันรหัสผ่าน" required>
                    </div>
                    <div class="mt-3 text-center">
                        <button type="submit" class="btn btn-custom rounded-pill">สมัครสมาชิก</button>
                        <a href="login.php" class="btn btn-outline-custom rounded-pill">เข้าสู่ระบบ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
