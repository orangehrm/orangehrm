<?php
session_start();
abstract class AbstractPluginCommon {
	
	public function  routePlugin ($request) {	
		$route = explode("/", $request['route']);
		$class = $route [0];
		$method = $route [1];
		
		$class = new $class ();		
		return $class->$method ($request, $_SESSION);		
	}
}

?>