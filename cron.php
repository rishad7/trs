<?php

$total_call_count = 0;
for($i = 1; $i <= 54; $i++) {
    $doc_name = "doc" . $i . ".json";
    $file = "./data/" . $doc_name;
    $json_array = json_decode(file_get_contents($file), true);
    if (is_array($json_array)) {
        $data = $json_array;
        foreach($data as $d) {
            if($d['status'] != '') {
                $total_call_count = $total_call_count + 1;
            }
        }
    }
}

$dashboard_info_file = "./data/dashboard_info.json";
$json_dashboard_info_array = json_decode(file_get_contents($dashboard_info_file), true);
if (is_array($json_dashboard_info_array)) {
    $json_dashboard_info_array['total_call_count'] = $total_call_count;
    $new_json_dashboard_info_string = json_encode($json_dashboard_info_array);
    file_put_contents($dashboard_info_file, $new_json_dashboard_info_string);
}

die('finished');

?>