<?php

define('RESTRICTED', true);

require('header.php');

if (isset($_GET['doc'])) {
    $postfix = $_GET['doc'];
} else {
    $postfix = 1;
}

$file = "./data/doc" . $postfix . ".json";

if (!file_exists($file)) {
    header('Location: home.php');
    exit();
}

$json_array = json_decode(file_get_contents($file), true);

$data = array();

if (is_array($json_array)) {
    $data = $json_array;
}

$server = $_SERVER['SERVER_NAME'];
$uri = $_SERVER['PHP_SELF'];
$current_url = "http://" . $server . $uri;
$next_doc = $current_url . "?doc=" . $postfix + 1;

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
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">List Heading</h3>
                                        <span class="text-base font-normal text-gray-500">List short discription</span>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= $next_doc ?>" class="text-sm font-medium text-cyan-600 hover:bg-gray-100 rounded-lg p-2">Next doc</a>
                                    </div>
                                </div>



                                <div class="w-full">
                                    <div class="p-8 border-b border-gray-200 shadow">
                                        <table class="divide-y divide-gray-300" id="dataTable">
                                            <thead class="bg-black">
                                                <tr>
                                                    <th class="px-6 py-2 text-xs text-white">
                                                        #
                                                    </th>
                                                    <th class="px-6 py-2 text-xs text-white">
                                                        User Id
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
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-300">
                                                <?php $i = 1;
                                                foreach ($data as $d) { ?>
                                                    <tr class="text-center whitespace-nowrap">
                                                        <td class="px-6 py-4 text-sm text-gray-500">
                                                            <?= $i++ ?>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="text-sm text-gray-900">
                                                                <?= $d['user_id'] ?>
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
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                <!-- <div class="flex flex-col mt-8">
                                    <div class="overflow-x-auto rounded-lg">
                                        <div class="align-middle inline-block min-w-full">
                                            <div class="shadow overflow-hidden sm:rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Transaction
                                                            </th>
                                                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Date & Time
                                                            </th>
                                                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Amount
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        <tr>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                                                Payment from <span class="font-semibold">Bonnie Green</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 23 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $2300
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-gray-50">
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900 rounded-lg rounded-left">
                                                                Payment refund to <span class="font-semibold">#00910</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 23 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                -$670
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                                                Payment failed from <span class="font-semibold">#087651</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 18 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $234
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-gray-50">
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900 rounded-lg rounded-left">
                                                                Payment from <span class="font-semibold">Lana Byrd</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 15 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $5000
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                                                Payment from <span class="font-semibold">Jese Leos</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 15 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $2300
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-gray-50">
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900 rounded-lg rounded-left">
                                                                Payment from <span class="font-semibold">THEMESBERG LLC</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 11 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $560
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                                                Payment from <span class="font-semibold">Lana Lysle</span>
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                                Apr 6 ,2021
                                                            </td>
                                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                                $1437
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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

</body>

</html>