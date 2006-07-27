<?php
	 
		
	define('ROOT_PATH', dirname($_SERVER['DOCUMENT_ROOT'].'/OpenSourceEIM'));
	$_SESSION['path']=ROOT_PATH;
	
	require_once ROOT_PATH . '/lib/datastructures/dataStructTree.php';
	
	$treeCompStruct = new dataStructTree();
	
	$state=$treeCompStruct->removeNode($_GET['lft'], $_GET['rgt']);
	
	header("Location: ".$_SERVER['HTTP_REFERER']); 
?>

