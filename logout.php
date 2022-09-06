<?php
session_start();
unset($_SESSION["username"]);
unset($_SESSION["id"]);
unset($_SESSION["valid"]);
unset($_SESSION["timeout"]);
unset($_SESSION["data"]);

echo 'You have cleaned session';
header('Location: index.php');
exit();
