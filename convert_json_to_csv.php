<?php
function flatten($items, $parentID = null, $level = 0) {
$flattened = [];

foreach ($items as $item) {
// Prepare a flat structure for the CSV row
$row = [
'activity' => $item['activity'],
'description' => $item['description'],
'responsible' => $item['responsible'],
'consulted' => $item['consulted'],
'startDate' => $item['startDate'],
'targetCompleteDate' => $item['targetCompleteDate'],
'duration' => $item['duration'],
'priority' => $item['priority'],
'estimatedCostMinimum' => $item['estimatedCostMinimum'],
'estimatedCostMaximum' => $item['estimatedCostMaximum'],
'expectedOutcomes' => $item['expectedOutcomes'],
'successMeasures' => $item['successMeasures'],
'dependencies' => $item['dependencies'],
'associatedRisks' => $item['associatedRisks'],
'metadata.id' => $item['metadata'][0]['id'],
'metadata.hasParent' => $item['metadata'][0]['hasParent'],
'metadata.parentID' => $item['metadata'][0]['parentID'],
'metadata.type' => $item['metadata'][0]['type'],
'metadata.level' => $item['metadata'][0]['level'],
'parentID' => $parentID,
'level' => $level,
];

$flattened[] = $row;

// If there are nested objects, recursively flatten them
if (!empty($item['objects'])) {
$nestedFlattened = flatten($item['objects'], $item['metadata'][0]['id'], $level + 1);
$flattened = array_merge($flattened, $nestedFlattened);
}
}

return $flattened;
}

// Get the JSON data from the POST request body
//$jsonData = file_get_contents($_POST['json']);
$data = json_decode($_POST['json'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid JSON provided']);
exit;
}

// Flatten the JSON data
$flattenedData = flatten([$data]);

// Create a CSV string
$output = fopen('php://output', 'w');
$filename = 'tmp/'.time().'.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');

// Add the headers to the CSV output
$headers = [
'Activity',
'Description',
'Responsible',
'Consulted',
'Start Date',
'Target Complete Date',
'Duration',
'Priority',
'Estimated Cost Minimum',
'Estimated Cost Maximum',
'Expected Outcomes',
'Success Measures',
'Dependencies',
'Associated Risks',
'Metadata ID',
'Metadata Has Parent',
'Metadata Parent ID',
'Metadata Type',
'Metadata Level',
'Parent ID',
'Level'
];

// Add headers to the CSV
fputcsv($output, $headers);

// Write the flattened data into the CSV
foreach ($flattenedData as $row) {
fputcsv($output, $row);
}

// Close the output stream and remove temporary file
fclose($output);
unlink($filename);
exit;