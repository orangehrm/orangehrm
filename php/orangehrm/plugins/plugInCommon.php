<?php
abstract class plugInCommon {
	
	private $plugInName = NULL;
	private $plugInVersion = NULL;
	private $authorizedRoles = NULL;
	private $authorizeModules = NULL;
	
	/**
	 * Get Authorized Roles for this plugin
	 *
	 * @return array
	 */
	
	public function getAuthorizedRoles() {
		return $this->authorizedRoles;
	}
	
	/**
	 * Get Authorize Modules for this plugin
	 *
	 * @return array
	 */
	
	public function getAuthorizeModules() {
		return $this->authorizeModules;
	}
	
	/**
	 * Get PlugInName for this plugin
	 *
	  * @return Name for this plugin
	 */
	
	public function getPlugInName() {
		return $this->plugInName;
	}
	
	/**
	 * Get PlugInVersion for this plugin
	 *
	  * @return PlugIn Version for this plugin
	 */
	
	public function getPlugInVersion() {
		return $this->plugInVersion;
	}
	
	/**
	 * Set Authorized Roles for this plugin
	 *
	 */
	
	public function setAuthorizedRoles($authorizedRoles) {
		$this->authorizedRoles = $authorizedRoles;
	}
	
	/**
	 * Set Authorize Modules for this plugin
	 *
	 */
	
	public function setAuthorizeModules($authorizeModules) {
		$this->authorizeModules = $authorizeModules;
	}
	 
	/**
	 * Set PlugInName for this plugin
	 *
	 */
	
	public function setPlugInName($plugInName) {
		$this->plugInName = $plugInName;
	}
	
	/**
	 * Set PlugIn Version for this plugin
	 *
	 */
	
	public function setPlugInVersion($plugInVersion) {
		$this->plugInVersion = $plugInVersion;
	}
	protected function __construct() {
    }
	
	
	/**
	 * Set Authorize Login User for this plugin
	 *
	 * @return boolean
	 */
    
	public function checkAuthorizeLoginUser($currentUser){
		if(array_key_exists($currentUser , $this->getAuthorizedRoles())){
		 	return true;
		 }else{
		 	return false;
		 }
	}
	
	/**
	 * Set Authorize Module User for this plugin
	 *
	 * @return boolean
	 */
	
	public function checkAuthorizeModule($currentModule){
		 if(array_key_exists($currentModule , $this->getAuthorizeModules())){
		 	return true;
		 }else{
		 	return false;
		 }
    }
    
    /**
	 * Install plugin
	 * @return boolean
	 */
    
	public function pluginInstall(){
    }
    
    /**
	 * Remove plugin
	 * @return boolean
	 */ 
    
	public function pluginRemove(){
    }
    
	private function readXMl(){
	}
}
?>