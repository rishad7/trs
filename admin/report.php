<?php

define('RESTRICTED', true);
define("encryption_method", "AES-128-CBC");
define("key", "Tr#tech#17");

require('header.php');

function decrypt($data) {
    $key = key;
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher = encryption_method);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
    if (hash_equals($hmac, $calcmac))
    {
        return $original_plaintext;
    }
}

$users = [];
$file = "../data/users.json";
$json_array = json_decode(file_get_contents($file), true);

if (is_array($json_array)) {
    $users = $json_array;
}

$data = [];
$report_data = [];
$selected_agent = "";
$user_id = "";
$status = "";

if(isset($_POST['submit']) && isset($_POST['agent']) && $_POST['agent'] != '') {
    $user_id = $_POST['agent'];
    $status = $_POST['status'];
    $doc_file = "../data/doc" . $user_id . ".json";
    
    if (!file_exists($doc_file)) {
        header('Location: home.php');
        exit();
    }

    $data = json_decode(file_get_contents($doc_file), true);

    $i = 0;

    foreach($data as $d) {
        if($status == '') {
            $report_data[$i]['user_id'] = $d['user_id'];
            $report_data[$i]['username'] = decrypt($d['username']);
            $report_data[$i]['phone_number'] = decrypt($d['phone_number']);
            $report_data[$i]['last_amount'] = $d['last_amount'];
            $report_data[$i]['last_used'] = $d['last_used'];
            $report_data[$i]['promotion'] = $d['promotion'];
            $report_data[$i]['status'] = $d['status'];
            $report_data[$i]['comment'] = $d['comment'];
            $i++;
        } else {
            if($status == $d['status']) {
                $report_data[$i]['user_id'] = $d['user_id'];
                $report_data[$i]['username'] = decrypt($d['username']);
                $report_data[$i]['phone_number'] = decrypt($d['phone_number']);
                $report_data[$i]['last_amount'] = $d['last_amount'];
                $report_data[$i]['last_used'] = $d['last_used'];
                $report_data[$i]['promotion'] = $d['promotion'];
                $report_data[$i]['status'] = $d['status'];
                $report_data[$i]['comment'] = $d['comment'];
                $i++;
            }
        }
    }

    $user_index = $user_id - 1;

    $selected_agent = $users[$user_index]['username'];

    $_SESSION['report_data'] = $report_data;
    $_SESSION['report_name'] = $selected_agent;
    $_SESSION['report_user_id'] = $user_id;
    $_SESSION['report_user_status'] = $status;

}

$is_download = false;
$report_name = "";

if (isset($_POST['download'])) {
    require('export_to_excel.php');
    $is_download = true;
    $report_name = $_SESSION['report_name'] . ".xlsx";
    $download_url = "http://localhost/WC/trs/export/$report_name";
    $selected_agent = $_SESSION['report_name'];
    $user_id = $_SESSION['report_user_id'];
    $status = $_SESSION['report_user_status'];
    $report_data = $_SESSION['report_data'];
}

if (isset($_POST['download_all_users'])) {

    $all_users_data = [];
    for($i = 1; $i <= 54; $i++) {
        $doc_name = "doc" . $i . ".json";
        $file = "../data/" . $doc_name;
        $json_array = json_decode(file_get_contents($file), true);
        if (is_array($json_array)) {

            $decrypted_data = [];

            foreach($json_array as $ja) {
                $ja['username'] = decrypt($ja['username']);
                $ja['phone_number'] = decrypt($ja['phone_number']);
                $decrypted_data[] = $ja;
            }

            $all_users_data = array_merge($all_users_data, $decrypted_data);
        }
    }

    $_SESSION['report_name'] = "TRS_All_Users_Report";
    $_SESSION['report_data'] = $all_users_data;

    require('export_to_excel.php');
    $is_download = true;
    $report_name = $_SESSION['report_name'] . ".xlsx";
    $download_url = "http://localhost/WC/trs/export/$report_name";
}

?>

<!DOCTYPE html lang="en-US">
<html>

<head>
    <title>TRS | Report</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <style>
        #dataTable_filter {
            margin-bottom: 16px;
        }
        #dataTable_wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
</head>

<body oncontextmenu="return false" oncopy="return false" oncut="return false" onpaste="return false">


    <!-- This is an example component -->
    <div>
        <nav class="bg-white border-b border-gray-200 fixed z-30 w-full">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar" class="lg:hidden mr-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded">
                            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg id="toggleSidebarMobileClose" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <a href="#" class="text-xl font-bold flex items-center lg:ml-2.5">
                            <img src="https://demo.themesberg.com/windster/images/logo.svg" class="h-6 mr-2" alt="Windster Logo">
                            <span class="self-center whitespace-nowrap">TRS</span>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <span class="text-base font-normal text-gray-500 mr-5"><?= $_SESSION['admin_username']; ?></span>
                        <a href="logout.php" tite="Logout">
                            <img src="../assets/images/logout.png" class="h-6 cursor-pointer" alt="Logout">
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="flex overflow-hidden bg-white pt-16">
            <aside id="sidebar" class="fixed hidden z-20 h-full top-0 left-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75" aria-label="Sidebar">
                <div class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white pt-0">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex-1 px-3 bg-white divide-y space-y-1">
                            <ul class="space-y-2 pb-2">
                                <li>
                                    <form action="#" method="GET" class="lg:hidden">
                                        <label for="mobile-search" class="sr-only">Search</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                                </svg>
                                            </div>
                                            <input type="text" name="email" id="mobile-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-cyan-600 focus:ring-cyan-600 block w-full pl-10 p-2.5" placeholder="Search">
                                        </div>
                                    </form>
                                </li>
                                <li>
                                    <a href="home.php" class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-6 h-6 text-gray-500 group-hover:text-gray-900 transition duration-75" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                        </svg>
                                        <span class="ml-3">Dashboard</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="report.php" class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 bg-gray-100 group transition duration-75 flex items-center p-2">
                                        <svg class="w-6 h-6 text-gray-500 flex-shrink-0 group-hover:text-gray-900 transition duration-75" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-3">Report</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="history.php" class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 group transition duration-75 flex items-center p-2">
                                        <svg class="w-6 h-6 text-gray-500 flex-shrink-0 group-hover:text-gray-900 transition duration-75" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48h-48z" fill="none"/><path fill="currentColor" d="M25.99 6c-9.95 0-17.99 8.06-17.99 18h-6l7.79 7.79.14.29 8.07-8.08h-6c0-7.73 6.27-14 14-14s14 6.27 14 14-6.27 14-14 14c-3.87 0-7.36-1.58-9.89-4.11l-2.83 2.83c3.25 3.26 7.74 5.28 12.71 5.28 9.95 0 18.01-8.06 18.01-18s-8.06-18-18.01-18zm-1.99 10v10l8.56 5.08 1.44-2.43-7-4.15v-8.5h-3z" opacity=".9"/></svg>
                                        <span class="ml-3">History</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="logout.php" class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 flex items-center p-2 group ">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500 flex-shrink-0 group-hover:text-gray-900 transition duration-75" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                            <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm5.03 4.72a.75.75 0 010 1.06l-1.72 1.72h10.94a.75.75 0 010 1.5H10.81l1.72 1.72a.75.75 0 11-1.06 1.06l-3-3a.75.75 0 010-1.06l3-3a.75.75 0 011.06 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-3 flex-1 whitespace-nowrap">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="bg-gray-900 opacity-50 hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>
                <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
                    <main>

                        <div class="pt-6 px-4 w-full flex justify-end">
                                <form class="form-signin flex items-center" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                    <button name="download_all_users" value="download_all_users" type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                                        <span>Download All Users Report</span>
                                    </button>
                                </form>
                        </div>

                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 gap-4">
                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Agent Report</h3>
                                        </div>
                                    </div>
                                    <form class="w-full" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                        <div class="flex flex-row flex-wrap">
                                            
                                            <div class="w-full xs:w-1/2 sm:w-1/2 px-4 mb-8">
                                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="agent">
                                                    Select an agent
                                                </label>
                                                <div class="relative">
                                                    <select name="agent" id="agent" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                                        <option value="">Select an option</option>
                                                        <?php foreach($users as $u) { ?>
                                                            <option <?=$user_id == $u['id'] ? ' selected="selected"' : '';?> value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full xs:w-1/2 sm:w-1/2 px-4 mb-8">
                                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="status">
                                                    Select a status
                                                </label>
                                                <div class="relative">
                                                    <select name="status" id="status" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                                        <option value="">All</option>
                                                        <option <?=$status == "Account Created - Lion567" ? ' selected="selected"' : '';?> value="Account Created - Lion567">Account Created - Lion567</option>
                                                        <option <?=$status == "Account Created - Topspin247" ? ' selected="selected"' : '';?> value="Account Created - Topspin247">Account Created - Topspin247</option>
                                                        <option <?=$status == "Account Created - King567" ? ' selected="selected"' : '';?> value="Account Created - King567">Account Created - King567</option>
                                                        <option <?=$status == "Active / Existing Player" ? ' selected="selected"' : '';?> value="Active / Existing Player">Active / Existing Player</option>
                                                        <option <?=$status == "User Busy" ? ' selected="selected"' : '';?> value="User Busy">User Busy</option>
                                                        <option <?=$status == "Call Back Later" ? ' selected="selected"' : '';?> value="Call Back Later">Call Back Later</option>
                                                        <option <?=$status == "Ringing No Response" ? ' selected="selected"' : '';?> value="Ringing No Response">Ringing No Response</option>
                                                        <option <?=$status == "Switch Off" ? ' selected="selected"' : '';?> value="Switch Off">Switch Off</option>
                                                        <option <?=$status == "Call Disconnected" ? ' selected="selected"' : '';?> value="Call Disconnected">Call Disconnected</option>
                                                        <option <?=$status == "Invalid Number" ? ' selected="selected"' : '';?> value="Invalid Number">Invalid Number</option>
                                                        <option <?=$status == "Repeat Call" ? ' selected="selected"' : '';?> value="Repeat Call">Repeat Call</option>
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full flex justify-center">
                                                <button type="submit" name="submit" value="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">View Report</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <?php if(count($report_data) > 0 || $user_id) { ?>
                            <div class="pt-6 px-4">
                                <div class="w-full grid grid-cols-1 gap-4">
                                    <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                        <div class="mb-4 flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-2"><?= $selected_agent ?> Report</h3>
                                            </div>
                                            <?php if(count($report_data) > 0) { ?>
                                                <form class="form-signin flex items-center" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                                    <button name="download" value="download" type="submit" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                                                        <span>Download Report</span>
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                        <?php if(count($report_data) > 0) { ?>
                                            <div class="w-full">
                                                <table class="divide-y divide-gray-300" id="dataTable">
                                                    <thead class="bg-black">
                                                        <tr>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                User Id
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Username
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Phone no
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Last amount
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Last used
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Status
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Promotion
                                                            </th>
                                                            <th class="px-6 py-2 text-xs text-white">
                                                                Comment
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-300">
                                                        <?php foreach ($report_data as $d) { ?>
                                                                <tr class="text-center whitespace-nowrap">
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-900">
                                                                            <?= $d['user_id'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-900">
                                                                            <?= $d['username'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-500">
                                                                            <?= $d['phone_number'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-500">
                                                                            <?= $d['last_amount'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-500">
                                                                            <?= $d['last_used'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-900">
                                                                            <?= $d['status'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-900">
                                                                            <?= $d['promotion'] ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-6 py-4">
                                                                        <div class="text-sm text-gray-900">
                                                                            <?= $d['comment'] ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div class="w-full">
                                                <h1 class="text-2xl text-center">No results found!</h1>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>


                        
                    </main>
                    <p class="text-center text-sm text-gray-500 my-10">
                        &copy; 2022 <a href="#" class="hover:underline" target="_blank">TRS Technology LLC</a>. All rights reserved.
                    </p>
                </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();

        });
    </script>
    <?php if ($is_download) { ?>
        <script>
            var anchor = document.createElement('a');
            anchor.href = '<?= $download_url ?>';
            anchor.download = '<?= $report_name ?>';
            document.body.appendChild(anchor);
            anchor.click();
        </script>
    <?php } ?>
    <?php
        require('footer.php');
    ?>
</body>
</html>