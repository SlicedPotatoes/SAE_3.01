<?php
require_once "src/Model/Absence.php";

echo "<pre>";

$tests = [
    "Test 1 : No filter" => [null, null, null, false, false, null],
    "Test 2 : No Student" => [999999, null, null, false, false, null],
    "Test 3 : Student good" => [null, null, null, false, false, null],
    "Test 4 : Unversed Date" => [null, '2025-10-12', '2025-10-01', false, false, null],
    "Test 5 : endDate only" => [null, null, '2025-10-01', false, false, null],
    "Test 6 : startDate only" => [null, '2025-10-10', null, false, false, null],
    "Test 7 : same Date" => [null, '2025-10-10', '2025-10-10', false, false, null],
    "Test 8 : Normal Date" => [null, '2025-10-10', '2025-10-12', false, false, null],
    "Test 9 : Examen" => [null, null, null, true, false, null],
    "Test 10 : No justification" => [null, null, null, false, true, null],
    "Test 11 : Validated" => [null, null, null, false, false, 'Validated'],
    "Test 12 : Refused" => [null, null, null, false, false, 'Refused'],
    "Test 13 : NotJustified" => [null, null, null, false, false, 'NotJustified'],
    "Test 14 : Pending" => [null, null, null, false, false, 'Pending'],
];


foreach ($tests as $label => [$studentId, $startDate, $endDate, $exam, $allowed, $state]) {
    echo "$label \n";
    $result = Absence::getAbsencesStudentFiltered($studentId, $startDate, $endDate, $exam, $allowed, $state);
    echo "Résultat : " . count($result) . " absences trouvées\n";
    if (!empty($result)) {
        print_r($result);
    }
    echo "\n";
}

echo "</pre>";