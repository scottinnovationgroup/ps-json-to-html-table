<?php
function csvToHtmlTable($csvString, $selectedColumns) {
    // Split the string into lines
    $lines = explode("\n", trim($csvString));

    // Initialize an array to store headers
    $headers = [];

    // Start the HTML table
    $html = '<table class="pure-table pure-table-bordered">';

    foreach ($lines as $index => $line) {
        // Split each line by the comma
        $cells = str_getcsv($line);

        // If it's the first line, set headers
        if ($index === 0) {
            $headers = $cells;
            // Create header row
            $html .= '<thead><tr>';
            foreach ($headers as $header) {
                // Display only columns that match selected columns (case-insensitive)
                if (in_array($header, $selectedColumns)) {
                    $html .= '<th>' . htmlspecialchars($header) . '</th>';
                } elseif (key_exists($header, $selectedColumns)) {
                    $html .= '<th>' . htmlspecialchars($selectedColumns[$header]) . '</th>';
                }
            }
            $html .= '</tr></thead><tbody>';
            continue; // Skip processing for header row
        }

        // Begin a new row
        $html .= '<tr class="'.$cells[17].'">';

        foreach ($cells as $key => $cell) {
            if($headers[$key] == 'Estimated Cost Minimum' || $headers[$key] == 'Estimated Cost Maximum') {
                //$cell = '$'.number_format($cell, 0, '.', ',');
            }
            if($headers[$key] == 'Start Date' || $headers[$key] == 'Target Complete Date') {
                //$cell = strtotime($cell);
                //$cell = date('m/d/Y', $cell);
            }

            // Check if the header corresponds to the selected columns (case-insensitive)
            if (in_array($headers[$key], $selectedColumns)) {
                // Escape cell content to prevent XSS
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            } elseif (key_exists($headers[$key], $selectedColumns)) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
        }

        // End the row
        $html .= '</tr>';
    }

    // End the HTML table
    $html .= '</tbody></table>';

    return $html;
}


// Example usage
$csvString = $_POST['data'];
$selectedColumns = ['Activity','Description','Responsible','Start Date','Target Complete Date'=>'Complete Date','Estimated Cost Minimum'=>'Min. Cost','Estimated Cost Maximum'=>'Max. Cost']; // Specify which columns to include
$htmlTable = csvToHtmlTable($csvString, $selectedColumns);
print $htmlTable;