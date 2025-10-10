<?php
require '../config.php';
require 'authen_admin.php'; 
require '../session_timeout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['u_id'])) {
    $user_id = $_POST['u_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
    $stmt->execute([$user_id]);

    
    header("Location: users.php");
    exit;
}

?>