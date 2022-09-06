<?php

define('RESTRICTED', true);

require('header.php');

// if (isset($_GET['doc'])) {
//     $postfix = $_GET['doc'];
// } else {
//     $postfix = 1;
// }

$doc_name = $_SESSION['doc_name'];

$file = "./data/" . $doc_name;

if (!file_exists($file)) {
    header('Location: home.php');
    exit();
}

if (isset($_SESSION['data'])) {
    $json_array = $_SESSION['data'];
} else {
    $json_array = json_decode(file_get_contents($file), true);
}

$data = array();
$selected_data = [];
$index = 0;

if (is_array($json_array)) {
    $data = $json_array;
    $_SESSION['data'] = $data;

    foreach($data as $d) {
        $selected_index = $index++;
        if($d['status'] == '') {
            $selected_data = $d;
            break;
        }
    }

}

$server = $_SERVER['SERVER_NAME'];
$uri = $_SERVER['PHP_SELF'];
$current_url = "http://" . $server . $uri;
// $next_doc = $current_url . "?doc=" . $postfix + 1;

// if (isset($_POST['add']) && isset($_POST['index'])) {

//     $index = $_POST['index'];
//     $comment = trim(htmlspecialchars($_POST['comment']));
//     $status = trim(htmlspecialchars($_POST['status']));

//     $data[$index]['comment'] = $comment;
//     $data[$index]['status'] = $status;

//     $new_json_string = json_encode($data);
//     file_put_contents($file, $new_json_string);
// }

if(isset($_POST['submit'])) {
    $index = $_POST['index'];
    $comment = trim(htmlspecialchars($_POST['comment']));
    $status = trim(htmlspecialchars($_POST['status']));

    $data[$index]['comment'] = $comment;
    $data[$index]['status'] = $status;

    $new_json_string = json_encode($data);
    file_put_contents($file, $new_json_string);

    $_SESSION['data'] = $data;
    $selected_data = [];
    $index = 0;

    foreach($data as $d) {
        $selected_index = $index++;
        if($d['status'] == '') {
            $selected_data = $d;
            break;
        }
    }

}

$is_download = false;

if (isset($_POST['download'])) {
    $csv = './export/download.csv';
    $file_pointer = fopen($csv, 'w');
    foreach($data as $i){
        fputcsv($file_pointer, $i);
    }
    fclose($file_pointer);
    $is_download = true;
}

?>

<!DOCTYPE html lang="en-US">
<html>

<head>
    <title>TRS | Dashboard</title>
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
        .text-area-div {
            height: 180px;
            background-color: white;
            border: 1px solid #ECECEC;
            border-radius: 28px;
        }
    </style>
</head>

<body>


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
                        <span class="text-base font-normal text-gray-500 mr-5"><?= $_SESSION['username']; ?></span>
                        <a href="logout.php" tite="Logout">
                            <img src="./assets/images/logout.png" class="h-6 cursor-pointer" alt="Logout">
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
                                    <a href="<?= $current_url ?>" class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-6 h-6 text-gray-500 group-hover:text-gray-900 transition duration-75" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                        </svg>
                                        <span class="ml-3">Dashboard</span>
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
                        <div class="pt-6 px-4">
                            <div class="w-full grid grid-cols-1 gap-4">

                                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 ">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">User Details</h3>
                                            <!-- <span class="text-base font-normal text-gray-500">List short discription</span> -->
                                        </div>
                                        <!-- <div class="flex-shrink-0">
                                            <a href="<?= $next_doc ?>" class="text-sm font-medium text-cyan-600 hover:bg-gray-100 rounded-lg p-2">Next doc</a>
                                        </div> -->
                                        <!-- <form class="form-signin flex items-center" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                            <button name="download" value="download" type="submit" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                                                <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                                                <span>Download</span>
                                            </button>
                                        </form> -->
                                    </div>


                                    <?php if(count($selected_data) > 0) { ?>
                                        <form class="w-full" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                            <div class="flex flex-row flex-wrap">
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-userid">
                                                        User Id
                                                    </label>
                                                    <div id="view-userid" class="h-12 appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded flex items-center px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                        <?= $selected_data['user_id'] ?>
                                                    </div>
                                                </div>
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-username">
                                                        Username
                                                    </label>
                                                    <div id="view-username" class="h-12 appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded flex items-center px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                        <?= $selected_data['username'] ?>
                                                    </div>
                                                </div>
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-phone">
                                                        Phone number
                                                    </label>
                                                    <div id="view-phone" class="h-12 appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded flex items-center px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                        <?= $selected_data['phone_number'] ?>
                                                    </div>
                                                </div>
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-lastamount">
                                                        Last amount
                                                    </label>
                                                    <div id="view-lastamount" class="h-12 appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded flex items-center px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                        <?= $selected_data['last_amount'] ?>
                                                    </div>
                                                </div>
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-lastused">
                                                        Last used
                                                    </label>
                                                    <div id="view-lastused" class="h-12 appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded flex items-center px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                        <?= $selected_data['last_used'] ?>
                                                    </div>
                                                </div>
                                                <div class="w-full xs:w-1/2 sm:w-1/3 px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-status">
                                                        Status
                                                    </label>
                                                    <div class="relative">
                                                        <select name="status" id="status" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                                                            <option value=""><?= $selected_data['status'] == '' ? 'Select an option' : $selected_data['status']; ?></option>
                                                            <option value="Account Created - Lion567">Account Created - Lion567</option>
                                                            <option value="Account Created - Topspin247">Account Created - Topspin247</option>
                                                            <option value="Active / Existing Player">Active / Existing Player</option>
                                                            <option value="Busy">Busy</option>
                                                            <option value="Call Back Later">Call Back Later</option>
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="w-full px-4 mb-8">
                                                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="view-comment">
                                                        Add Comment
                                                    </label>
                                                    <div class="text-area-div p-4">
                                                        <textarea id="view-comment" name="comment" class="h-full w-full text-sm outline-none resize-none" placeholder="Add your comment"><?= $selected_data['comment'] ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="w-full flex justify-center">
                                                    <input type="hidden" name="index" value="<?= $selected_index; ?>" />
                                                    <button type="submit" name="submit" value="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php } else { ?>
                                        <p class="text-4xl text-center">No users found!</p>
                                    <?php } ?>    


                                    <!-- <div class="w-full">
                                        <div class="p-8 border-b border-gray-200 shadow">
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
                                                            Add comment
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-300">
                                                    <?php $i = 0; foreach ($data as $d) { ?>
                                                            <tr class="text-center whitespace-nowrap">
                                                            <form class="form-signin flex items-center" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
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
                                                                    <select name="status" id="status">
                                                                        <option <?php if($d['status'] == "") { ?>selected<?php } ?> value="">Select an option</option>
                                                                        <option <?php if($d['status'] == "Account Created - Lion567") { ?>selected<?php } ?> value="Account Created - Lion567">Account Created - Lion567</option>
                                                                        <option <?php if($d['status'] == "Account Created - Topspin247") { ?>selected<?php } ?> value="Account Created - Topspin247">Account Created - Topspin247</option>
                                                                        <option <?php if($d['status'] == "Active / Existing Player") { ?>selected<?php } ?> value="Active / Existing Player">Active / Existing Player</option>
                                                                        <option <?php if($d['status'] == "Busy") { ?>selected<?php } ?> value="Busy">Busy</option>
                                                                        <option <?php if($d['status'] == "Call Back Later") { ?>selected<?php } ?> value="Call Back Later">Call Back Later</option>
                                                                    </select>
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    <textarea id="comment" name="comment" rows="4" class="p-2 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mr-1" placeholder="Add your comment here">
                                                                        <?= htmlspecialchars($d['comment']) ?>
                                                                    </textarea>
                                                                    <input type="hidden" name="index" value="<?= $i; ?>" />
                                                                    <button type="submit" class="h-6 inline-block px-2 py-1 bg-blue-600 text-white font-medium text-xs leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out" name="add">
                                                                        Add
                                                                    </button>
                                                                </td>
                                                            </form>
                                                            </tr>
                                                    <?php $i++; } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
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
            anchor.href = 'http://localhost/trs/export/download.csv';
            anchor.download = 'download.csv';
            document.body.appendChild(anchor);
            anchor.click();
        </script>
    <?php } ?>

</body>

</html>