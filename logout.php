<?php
session_start();

$user_history_file = "./data/user_history.json";
$json_user_history_file_array = json_decode(file_get_contents($user_history_file), true);

$user_details['user_id'] = $_SESSION['id'];
$user_details['username'] = $_SESSION['username'];
$user_details['status'] = 'logout';
$user_details['time'] = date('Y-m-d H:i:s');
$user_details['client_details'] = $_SERVER;

$json_user_history_file_array[] = $user_details;

$new_user_history_json_string = json_encode($json_user_history_file_array);
file_put_contents($user_history_file, $new_user_history_json_string);

unset($_SESSION["username"]);
unset($_SESSION["id"]);
unset($_SESSION["valid"]);
unset($_SESSION["timeout"]);
unset($_SESSION["data"]);

header('Location: index.php');
exit();
