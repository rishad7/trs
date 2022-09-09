<?php
session_start();
unset($_SESSION["admin_username"]);
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_valid"]);
unset($_SESSION["admin_timeout"]);
unset($_SESSION["report_data"]);
unset($_SESSION["report_name"]);
unset($_SESSION["report_user_id"]);
unset($_SESSION["report_user_status"]);

header('Location: index.php');
exit();
