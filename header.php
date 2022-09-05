<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);


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
