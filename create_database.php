<?php

//PHP CLI

require_once("inc/Csv.Class.php");

$csv = new Csv();
$csv->createdb();
$csv->setdb();
$csv->createTables();

?>