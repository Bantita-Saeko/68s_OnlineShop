<?php
    session_start(); // Start the session to use session variables
    require_once 'config.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // รับค่าจากฟอร์ม
        $usernameOremail = trim($_POST['username_or_email']);
        $password = $_POST['password'];

        // เอาค่าที่รับมาจากฟอร์ม ไปตรวจสอบว่ามีข้อมูลตรงกับใน db หรือไม่
        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usernameOremail, $usernameOremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if($user['role'] === 'admin'){
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");

            }
            exit(); // หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง
        } else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }


    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            max-width: 800px;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 150px;
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
                text-align: center;
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
<body class="container-fluid">

    
    <div class="container">
        <div class="row g-0">
            <!-- ซ้าย -->
            <div class="col-md-5 left-side text-center">
                <h2>Welcome!</h2>
                <p>เข้าสู่ระบบ เพื่อเริ่มต้นใช้งาน</p>
            </div>
            <div class="col-md-7 right-side">
                <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                    <div class="alert alert-success">สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>
                <?php endif; ?>
            
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <h2>เข้าสู่ระบบ</h2>
            <form method="post" class="row g-3">
                <div class="mb3">
                    <label for="username_or_email" class="form-label">ชื่อผู้ใช้หรืออีเมล</label>
                    <input type="text" name="username_or_email" id="username_or_email" class="form-control" required>
                </div>
                <div class="mb3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-custom">เข้าสู่ระบบ</button>
                    <a href="register.php" class="btn btn-outline-custom">สมัครสมาชิก</a>
                </div>
            </form>
            </div>
        <div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>