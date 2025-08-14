<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
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
            p {
                font-size: 20px;
            }
        }
        .btn-danger {
            background-color: #cc3d3dff;
            border: none;
        }
        .btn-danger:hover {
            background-color: #000000ff;
            border: 1px solid #000000ff;
            color: white;
        }
        .text-custom {
            color: #87bef8ff;
        }
        .main {
            background-color: white;
            border-radius: 20px;
            padding: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main">
            <h1 class="text-center text-custom">ยินดีต้อนรับสู่หน้าหลัก</h1>
            <p class="text-center mt-3">ผู้ใช้: <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</p>
            <div class="text-center mt-3">
                <a href="logout.php" class="btn btn-danger rounded-pill">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</body>
</html>