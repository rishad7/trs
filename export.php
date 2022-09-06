<?php

ini_set('memory_limit', -1);
error_reporting(E_ALL);
ini_set("display_errors", 1);

// include the autoloader, so we can use PhpSpreadsheet
require_once(__DIR__ . '/vendor/autoload.php');

# Create a new Xls Reader
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

// Tell the reader to only read the data. Ignore formatting etc.
$reader->setReadDataOnly(true);

// Read the spreadsheet file.
$spreadsheet = $reader->load('./CFT_data_Allocation.xlsx');

$sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
$data = $sheet->toArray();

// output the data to the console, so you can see what there is.
// die(print_r($data, true));

$users = [];
$i = 0;

foreach ($data as $key => $value) {

    if($value[1] == '') {
        continue;
    }

    if (!in_array($value[1], $users)) {
        array_push($users, $value[1]);
        
        $user_data[$i]['id'] = $i + 1;
        $user_data[$i]['username'] = strtolower(str_replace(' ', '', $value[1]));
        $user_data[$i]['password'] = "123456";
        $user_data[$i]['doc_name'] = "doc" . $i + 1 . ".json";


        $user_json_string = json_encode($user_data);
        file_put_contents('./data/users_new.json', $user_json_string);


        $i++;

        $j = 0;


    }

    // $doc_data[$i][$j]['user_id'] = $j + 1;
    // $doc_data[$i][$j]['username'] = $value[0];
    // $doc_data[$i][$j]['phone_number'] = $value[2];
    // $doc_data[$i][$j]['last_amount'] = "";
    // $doc_data[$i][$j]['last_used'] = "";
    // $doc_data[$i][$j]['status'] = "";
    // $doc_data[$i][$j]['comment'] = "";

    

    // $j++;








    // print_r($value[0]);
    // print_r($value[1]);
    // print_r($value[2]);
    // die();
}


// $k = 1;

// foreach($doc_data as $b) {


//     $doc_json_string = json_encode($b);
//     $doc_filename = "./data/doc" . $k . ".json";
//     file_put_contents($doc_filename, $doc_json_string);

//     $k++;


// }


echo "<pre>";
print_r($users);
echo "</pre>";

die();