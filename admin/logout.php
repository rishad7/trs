<?php
session_start();
unset($_SESSION["admin_username"]);
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_valid"]);
unset($_SESSION["admin_timeout"]);
unset($_SESSION["admin_data"]);

echo 'You have cleaned session';
header('Location: index.php');
exit();
