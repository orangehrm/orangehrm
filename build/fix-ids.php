<?php
define('ROOT_PATH', dirname(__FILE__) . '/..');
require_once ROOT_PATH . '/installer/utils/DMLFunctions.php';
require_once ROOT_PATH . '/installer/utils/UniqueIDGenerator.php';

echo "Running UniqueIDGenerator\n";
$conn = new DMLFunctions();
UniqueIDGenerator::getInstance()->initTable();
echo "Finished fixing ID's\n";

?>
