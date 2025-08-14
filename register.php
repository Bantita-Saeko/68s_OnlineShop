<?php
    require_once 'config.php';

    $error = []; // Array to hold error messages

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
        
        // ตรวจสอบว่ากรอกข้อมูลมาครบหรือไม่ (empty)
        if (empty($username)||empty($fullname)||empty($email)||empty($password)||empty($confirmpassword) ) {
            $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง"; 
            // ตรวจสอบว่าอีเมลถูกต้องหรือไม่ (filter_var)
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
        }elseif($password !== $confirmpassword){
            $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
        }else {
            // ตรวจสอบว่ามีชื่อผู้ใช้หรืออีเมลถูกใช้ไปแล้วหรือไม่
            $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $email]);
            
            if($stmt->rowCount() > 0){
                $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
            }
            
        }

        if (empty($error)) { // ถ้าไม่มีข้อผิดพลาดใดๆ 
            // นำข้อมูลไปบันทึกในฐานข้อมูล

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
            $sql = "INSERT INTO users(username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $fullname, $email, $hashedPassword]);
            // ถ้าบันทึกสำเร็จให้เปลี่ยนเส้นทางไปหน้า login
            header("Location: login.php?register=success");
            exit(); // หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง
        }
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
            border-radius: 20px 0 0 20px;
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
            border-radius: 0 20px 20px 0;
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
    <div class="container mt-2 w-50">
        <div class="row g-0">
            <!-- ซ้าย -->
            <div class="col-md-5 left-side text-center">
                <h2>Welcome!</h2>
                <p>สมัครสมาชิก เพื่อเริ่มต้นใช้งาน</p>
            </div>

            <!-- ขวา -->
            <div class="col-md-7 right-side">
                <h2 class="mb-4 text-center">สมัครสมาชิก</h2>
                <?php if (!empty($error)): // ถ ้ำมีข ้อผิดพลำด ให้แสดงข ้อควำม ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($error as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>

                            <!--ใช ้ htmlspecialchars เพื่อป้องกัน XSS-->
                            <!-- < ? = คือ short echo tag ?> -->
                            <!-- ถ้าเขียนเต็ม จะได ้แบบด ้ำนล่ำง -->
                            <?php // echo "<li>" . htmlspecialchars($e) . "</li>"; ?>

                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>


                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" name="username" id="username" class="form-control rounded-pill" placeholder="กรุณากรอกชื่อผู้ใช้" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">ชื่อ-สกุล</label>
                        <input type="text" name="fullname" id="fullname" class="form-control rounded-pill" placeholder="กรุณากรอกชื่อ-สกุล" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>" required > 
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="text" name="email" id="email" class="form-control rounded-pill" placeholder="กรุณากรอกอีเมล" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required >
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
