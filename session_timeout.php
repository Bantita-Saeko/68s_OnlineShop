<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 30 * 60;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {

    session_unset();
    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

?>