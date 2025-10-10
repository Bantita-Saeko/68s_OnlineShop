<?php
session_start();
require_once 'config.php';
require 'session_timeout.php';
if (isset($_SESSION['review_message'])) {
    $reviewMessage = $_SESSION['review_message'];
    unset($_SESSION['review_message']);
}

$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT r.*, p.product_name, u.username
                      FROM reviews r
                      JOIN products p ON r.product_id = p.product_id
                      JOIN users u ON r.user_id = u.user_id
                      ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getRatingColor($rating)
{
    if ($rating >= 4.5)
        return 'bg-success';
    if ($rating >= 3.0)
        return 'bg-warning text-dark';
    return 'bg-danger';
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีวิวสินค้าทั้งหมด</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Kanit&family=Nunito&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
        }

        .btn-gradient {
            background: linear-gradient(135deg, #80c2ff, #a7c5ff);
            color: white;
            border: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-outline-custom {
            background: #ffffff;
            border: 1px solid #86c0fd;
            color: #86c0fd;
            transition: all 0.3s;
        }

        .btn-outline-custom:hover {
            background: #86c0fd;
            color: white;
        }

        .review-card {
            border-left: 5px solid #80c2ff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .review-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .rating-star i {
            color: #ffc107;
            font-size: 1.1rem;
        }
    </style>
</head>

<body class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-gradient"><i class="bi bi-chat-square-text"></i> รีวิวสินค้า</h1>

        <div>
            <?php if ($isLoggedIn): ?>
                <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?>
                    (<?= $_SESSION['role'] ?>)</span>
                <a href="profile.php" class="btn btn-outline-custom me-2"><i class="bi bi-person-circle"></i>
                    ข้อมูลส่วนตัว</a>
                <a href="cart.php" class="btn btn-outline-custom me-2"><i class="bi bi-cart"></i> ดูตะกร้า</a>
                <a href="order.php" class="btn btn-outline-custom me-2"><i class="bi bi-clock-history"></i>
                    ดูประวัติการสั่งซื้อ</a>
                <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-gradient me-2">เข้าสู่ระบบ</a>
                <a href="register.php" class="btn btn-outline-custom">สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </div>


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class=" d-flex gap-2 me-auto">
            <a href="index.php" class="btn btn-outline-custom">
                <i class="bi bi-grid-fill"></i> สินค้า
            </a>
            <a href="review.php" class="btn btn-gradient active">
                <i class="bi bi-chat-square-text-fill"></i> รีวิว
            </a>
            <?php if ($isLoggedIn): ?>
                <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-pencil-square"></i> เขียนรีวิว
                </button>
            <?php else: ?>
                <a href="login.php" class="btn btn-warning me-2">
                    <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบเพื่อรีวิว
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($reviewMessage)): ?>
        <?= $reviewMessage ?>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (empty($reviews)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-info-circle-fill"></i> ยังไม่มีรีวิวสำหรับแสดง

                </div>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review):
                $rating = (float) $review['rating'];
                $full = floor($rating);
                $half = ($rating - $full) >= 0.5 ? 1 : 0;
                ?>
                <div class="col-lg-6">
                    <div class="card review-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1">
                                    <span class="text-gradient"><i class="bi bi-tag-fill"></i> สินค้า:</span>
                                    <a href="product_detail.php?id=<?= (int) $review['product_id'] ?>"
                                        class="text-gradient fw-bold">
                                        <?= htmlspecialchars($review['product_name']) ?>
                                    </a>
                                </h5>
                                <div class="rating-star">
                                    <?php for ($i = 0; $i < $full; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                    <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
                                    <?php for ($i = 0; $i < 5 - $full - $half; $i++): ?><i
                                            class="bi bi-star"></i><?php endfor; ?>
                                </div>
                            </div>

                            <span class="badge <?= getRatingColor($rating) ?> p-2 fs-6">
                                <?= number_format($rating, 1) ?>/5
                            </span>
                        </div>

                        <blockquote class="blockquote my-3 pb-2 border-bottom">
                            <p class="mb-0 fst-italic">"<?= nl2br(htmlspecialchars($review['comment'])) ?>"</p>
                        </blockquote>

                        <footer class="blockquote-footer mt-auto pt-2">
                            รีวิวโดย: <cite title="Source Title"
                                class="fw-bold text-dark"><?= htmlspecialchars($review['username']) ?></cite>
                            <span class="float-end text-muted">เมื่อ:
                                <?= date('d M Y', strtotime($review['created_at'])) ?></span>
                        </footer>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="submit_review.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">เขียนรีวิวสินค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php

                        $productStmt = $conn->query("SELECT product_id, product_name, image FROM products ORDER BY product_name ASC");
                        $availableProducts = $productStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="mb-3">
                            <label class="form-label">สินค้าที่ต้องการรีวิว</label>
                            <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                                <?php if (empty($availableProducts)): ?>
                                    <p class="text-muted p-2">ไม่มีสินค้าให้รีวิวในขณะนี้</p>
                                <?php else: ?>
                                    <?php foreach ($availableProducts as $prod):
                                        $img = !empty($prod['image']) ? 'product_images/' . rawurlencode($prod['image']) : 'product_images/no-image.png';
                                        ?>
                                        <label class="list-group-item d-flex align-items-center">
                                            <input class="form-check-input me-3" type="radio" name="product_id"
                                                value="<?= (int) $prod['product_id'] ?>" required>
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                alt="<?= htmlspecialchars($prod['product_name']) ?>"
                                                style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 5px;">
                                            <?= htmlspecialchars($prod['product_name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rating" class="form-label">คะแนน (1 - 5)</label>
                            <input type="number" step="0.5" min="1" max="5" class="form-control" id="rating"
                                name="rating" required>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">ความคิดเห็น</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary" id="submitReviewButton"
                                >บันทึกรีวิว</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const radios = document.querySelectorAll('.product-radio');
        const formContainer = document.getElementById('reviewFormContainer');
        const submitButton = document.getElementById('submitReviewButton');
        const titleSpan = document.getElementById('reviewSelectionTitle').querySelector('span');

        const ratingInput = document.getElementById('ratingInput');
        const commentInput = document.getElementById('commentInput');
        const selectedProductIdInput = document.getElementById('selectedProductId');


        function updateReviewForm(event) {
            const selectedRadio = document.querySelector('.product-radio:checked');

            if (selectedRadio) {
                const productId = selectedRadio.value;
                const productName = selectedRadio.dataset.productName;

                titleSpan.textContent = productName;

                formContainer.style.display = 'block';
                ratingInput.disabled = false;
                commentInput.disabled = false;
                submitButton.disabled = false;

                selectedProductIdInput.value = productId;


            } else {
                titleSpan.textContent = 'กรุณาเลือกสินค้าด้านบน';
                formContainer.style.display = 'none';
                ratingInput.disabled = true;
                commentInput.disabled = true;
                submitButton.disabled = true;
                selectedProductIdInput.value = '';
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateReviewForm);
        });

        const reviewModalElement = document.getElementById('reviewModal');

        reviewModalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('singleReviewForm').reset();
            updateReviewForm();

        reviewModalElement.addEventListener('hidden.bs.modal', function () {
            document.getElementById('singleReviewForm').reset();
            updateReviewForm(); 
        });
    });
</script>

    

</body>

</html>