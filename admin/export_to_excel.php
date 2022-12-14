<?php

$export_data = $_SESSION['report_data'];
$report_name = $_SESSION['report_name'];
$report_type = $_SESSION['report_type'];

require_once('../vendor/autoload.php');

$mySpreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

// delete the default active sheet
$mySpreadsheet->removeSheetByIndex(0);

// Create "Sheet 1" tab as the first worksheet.
// https://phpspreadsheet.readthedocs.io/en/latest/topics/worksheets/adding-a-new-worksheet
$worksheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Sheet 1");
$mySpreadsheet->addSheet($worksheet1, 0);

// sheet 1 contains the birthdays of famous people.
$sheet1Data = convertDataToChartForm($export_data, $report_type);

$worksheet1->fromArray($sheet1Data);


// Change the widths of the columns to be appropriately large for the content in them.
// https://stackoverflow.com/questions/62203260/php-spreadsheet-cant-find-the-function-to-auto-size-column-width
$worksheets = [$worksheet1];

foreach ($worksheets as $worksheet)
{
    foreach ($worksheet->getColumnIterator() as $column)
    {
        $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
    }
}

// Save to file.
$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($mySpreadsheet);
$report_file_name = "../export/$report_name.xlsx";
$writer->save($report_file_name);

function convertDataToChartForm($data, $report_type) {
    $newData = array();
    $firstLine = true;

    foreach ($data as $dataRow)
    {
        if ($firstLine)
        {
            if($report_type == 'all') {
                $excel_header = ['User Id', 'Username', 'Last Amount', 'Last Used', 'Promotion', 'Status', 'Comment', 'Agent'];
            } else {
                $excel_header = ['User Id', 'Username', 'Last Amount', 'Last Used', 'Promotion', 'Status', 'Comment'];
            }
            $newData[] = $excel_header;
            $firstLine = false;
        }

        unset($dataRow['phone_number']);

        $newData[] = array_values($dataRow);
    }

    return $newData;
}