<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
require_once './functions/user_db.php';
require_once './functions/page_layout.php';
showPageContent($conn);
?>