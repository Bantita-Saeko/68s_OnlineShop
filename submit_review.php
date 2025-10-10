<?php
session_start();
require_once 'config.php';
require 'session_timeout.php';
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (float) $_POST['rating'] : 0.0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($product_id <= 0 || $rating < 1 || $rating > 5) {
        $message = "<div class='alert alert-danger'>❌ บันทึกรีวิวไม่สำเร็จ: กรุณาเลือกสินค้าและให้คะแนน 1-5</div>";
    } else {
        try {
            $prodNameStmt = $conn->prepare("SELECT product_name FROM products WHERE product_id = :product_id");
            $prodNameStmt->execute(['product_id' => $product_id]);
            $productName = $prodNameStmt->fetchColumn() ?: 'สินค้าที่ไม่รู้จัก';

            $checkStmt = $conn->prepare("
    SELECT created_at 
    FROM reviews 
    WHERE user_id = :user_id AND product_id = :product_id 
    ORDER BY created_at DESC 
    LIMIT 1
   ");
            $checkStmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
            $lastReviewTime = $checkStmt->fetchColumn();

            $canReview = true;
            if ($lastReviewTime) {
                $secondsSinceLastReview = time() - strtotime($lastReviewTime);
                $oneDayInSeconds = 24 * 60 * 60;

                if ($secondsSinceLastReview < $oneDayInSeconds) {
                    $canReview = false;
                
                    $hoursRemaining = ceil(($oneDayInSeconds - $secondsSinceLastReview) / 3600);
                }
            }

            if (!$canReview) {
                $message = "<div class='alert alert-warning'>⚠️ บันทึกรีวิวไม่สำเร็จ: คุณรีวิว **{$productName}** ไปเมื่อไม่นานนี้ กรุณารออีกประมาณ {$hoursRemaining} ชั่วโมง จึงจะรีวิวซ้ำได้</div>";
            } else {
                $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) 
      VALUES (:product_id, :user_id, :rating, :comment)";
                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    'product_id' => $product_id,
                    'user_id' => $user_id,
                    'rating' => $rating,
                    'comment' => $comment
                ]);

                $message = "<div class='alert alert-success'>✅ บันทึกรีวิวสำหรับ **{$productName}** สำเร็จแล้ว!</div>";
            }

        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>❌ ข้อผิดพลาดในการบันทึกรีวิว: {$e->getMessage()}</div>";
        }
    }
} else {
    $message = "<div class='alert alert-info'>ℹ️ ไม่พบข้อมูลรีวิวที่ต้องการบันทึก</div>";
}

$_SESSION['review_message'] = $message;
header("Location: review.php");
exit();
?>