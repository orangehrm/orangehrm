<?php
	
	define('ROOT_PATH', dirname($_SERVER['DOCUMENT_ROOT'].'/OpenSourceEIM'));
	$_SESSION['path']=ROOT_PATH;
	
	require_once ROOT_PATH . '/lib/datastructures/dataStructTree.php';
	
	$treeCompStruct = new dataStructTree();		
	
	$state=$treeCompStruct->addNode($_POST['rgt'], ($_POST['txtTitle']." ".$_POST['txtType']), $_POST['txtDesc'],  $_POST['txtParnt'], $_POST['cmbLocation']);

	//echo $_POST['rgt'];
	header("Location: ".$_SERVER['HTTP_REFERER']); 
?>

