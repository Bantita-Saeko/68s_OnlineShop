<?php
session_start();
require_once '../config.php';
require_once 'authen_admin.php';

// ลบสมาชิก
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    if ($user_id != $_SESSION['user_id']) { // ป้องกันลบตัวเอง
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
    }
    header("Location: users.php");
    exit;
}

// ดึงข้อมูลสมาชิก
$stmt = $conn->prepare("SELECT * FROM users WHERE role='member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// นับจำนวนสมาชิก
$totalUsers = count($users);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสมาชิก</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">
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
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}
.main-card {
    background-color: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    margin-top: 20px;
}
.btn-gradient {
    color: white;
    font-weight: 500;
    border-radius: 12px;
    transition: all 0.3s;
    border: none;
}
.btn-edit { background: linear-gradient(135deg, #bdd1dbff, #a2cde1ff); }
.btn-delete { background: linear-gradient(135deg, #ffdde1, #ee9ca7); }
.btn-gradient:hover { filter: brightness(1.1); transform: translateY(-2px); }
.stat-card {
    border-radius: 15px;
    padding: 15px;
    text-align: center;
    color: white;
    background: linear-gradient(135deg, #cdb4db, #e3c8f2);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.stat-icon { font-size: 1.5rem; margin-bottom: 5px; }


</style>
</head>
<body class="container py-5">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-gradient"><i class="bi bi-people"></i> จัดการสมาชิก</h2>
    <a href="index.php" class="btn btn-gradient btn-edit rounded-pill">← กลับหน้าผู้ดูแล</a>
</div>

<!-- Stat Card -->
<div class="stat-card mb-4">
    <i class="bi bi-people stat-icon"></i><div class="fs-4 fw-bold">จำนวนสมาชิกทั้งหมด : <?= $totalUsers ?></div>
</div>

<!-- Member Table -->
<div class="main-card">
<?php if ($totalUsers === 0): ?>
    <div class="alert alert-warning">ยังไม่มีสมาชิกในระบบ</div>
<?php else: ?>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ชื่อผู้ใช้</th>
                <th>ชื่อ-นามสกุล</th>
                <th>อีเมล</th>
                <th>วันที่สมัคร</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-gradient btn-edit btn-sm me-1">
                        <i class="bi bi-pencil-square"></i> แก้ไข
                    </a>
                    <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-gradient btn-delete btn-sm"
                        onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?')">
                        <i class="bi bi-trash"></i> ลบ
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

</body>
</html>
