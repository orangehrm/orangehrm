<?php
include 'schemaIncrementTasks/SchemaIncrementTask20.php';
include 'schemaIncrementTasks/SchemaIncrementTask19.php';
include 'schemaIncrementTasks/SchemaIncrementTask18.php';

$task[0] = new SchemaIncrementTask18();
//$task[1] = new SchemaIncrementTask20();

$id = $_GET['task'];
$task[$id]->execute();
$progeress = $task[$id]->getProgress();
$arr = array('progress' => $progeress);

echo json_encode($arr);