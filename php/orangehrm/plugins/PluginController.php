<?php

define('ROOT_PATH', dirname(__FILE__).'/..');

class PluginController {
	
	public function __construct($route){
		$route = explode("/",$route);
		$pluginController = $this->getPluginController($route[0]);		
		$pluginController->routePlugin($_REQUEST);
	}
	
	public function getPluginController($name){
		switch ($name) {
			case 'CSVPluginController':
				return new CSVPluginController();
			break;
			
			default:
				;
			break;
		}		
	}
}

require_once 'PlugInFactory.php';
require_once 'AbstractPluginCommon.php';
require_once 'InterfacePluginCommon.php';

// these includes should be dynamicaly include from the xml
require_once  'csv/CSVPluginController.php';

$pluginController = new PluginController($_REQUEST['route']);
	


?>