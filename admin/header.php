<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('BASE_URL', 'http://localhost/WC/trs');

if (defined('RESTRICTED')) {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit();
    }
} else {
    if (isset($_SESSION['admin_id'])) {
        header('Location: home.php');
        exit();
    }
}
