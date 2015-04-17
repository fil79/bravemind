<?php

//USE PHP CLI TO SEE LIVE IMPORT

require_once("inc/Csv.Class.php");

$csv = new Csv();
$csv->setdb();
$csv->importCsv("sales.csv");

?>