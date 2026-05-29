<?php
session_start();
require_once './functions/user_db.php';
require_once './functions/page_layout.php';
showPageContent($conn);
?>