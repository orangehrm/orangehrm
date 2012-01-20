<?php
define('ROOT_PATH', dirname(__FILE__) . '/..');
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

echo "Running UniqueIDGenerator\n";
$conn = new DMLFunctions();
UniqueIDGenerator::getInstance()->initTable();
echo "Finished fixing ID's\n";

?>
