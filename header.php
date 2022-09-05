<?php
ob_start();
session_start();

if (defined('RESTRICTED')) {
    if (!isset($_SESSION['id'])) {
        header('Location: index.php');
        exit();
    }
} else {
    if (isset($_SESSION['id'])) {
        header('Location: home.php');
        exit();
    }
}
