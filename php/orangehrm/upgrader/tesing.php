<?php

$con = mysql_connect("localhost", "root", "beyondm");

require_once('backup.php');
require_once('restore.php');


$dump = new Backup();
$dump->setConnection($con);
$dump->setDatabase("hsenidco_hsenid");
$filecontent=$dump->dumpDatabase(true);
$result = mysql_query("TRUNCATE TABLE `raffle`", $con);
$result = mysql_query("TRUNCATE TABLE `data_sheet_info`", $con);
$result = mysql_query("TRUNCATE TABLE `product_datasheet`", $con);

$restore = new Restore();
$restore->setConnection($con);
$restore->setDatabase("hsenidco_hsenid");
$restore->setfileSource($filecontent);
//print_r($restore->getfileSource());
$temp= $restore->fillDatabase();
echo $temp;

?>