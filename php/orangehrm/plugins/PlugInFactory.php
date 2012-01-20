<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/plugins/Plugin.php';
require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
class PlugInFactory
{
	private function __construct(){
	}
	
	/**
	 * Get plugin object
	 *
	 * @return new plugin object
	 */
    public static function factory($plugInName){
		//Acces databse and get xmlpath
		$xmlPath = Plugin::fetchPlugin($plugInName);
		if($xmlPath){
			return PlugInFactory::readXMl($xmlPath);
		}else{
			throw new PlugInFactoryException(PlugInFactoryException::PLUGIN_INSTALL_ERROR);		
		}
		
		/* $temppluginObj = new Plugin();
		$temppluginObj->setPluginName(trim($plugInName));
		$tempPluginObj = $temppluginObj->fetchPlugin();
		
		if(is_object($tempPluginObj)){
			return PlugInFactory::readXMl($tempPluginObj);
		}else{
			throw new PlugInFactoryException(PlugInFactoryException::PLUGIN_INSTALL_ERROR);		
		} */
	}

	
	public function executePluginAction(){
		
	}
	
	/**
	 * Read plugins's install xml file
	 *
	 * @return new plugin object
	 */
	
	private static function readXMl($xmlPath){
		
		//Loading xml file through SimpleXML libarey
		if(is_file(ROOT_PATH .  "/" . $xmlPath)){
			$xmlObj = simplexml_load_file(ROOT_PATH .  "/" .  $xmlPath);
			if(is_file(ROOT_PATH . trim($xmlObj->initFile))){
					require_once(ROOT_PATH . trim($xmlObj->initFile));
					$pluginClassName = trim($xmlObj->initClass);
					$pluginClassNameNewObj = new $pluginClassName(); 
					foreach($xmlObj->authorizedRoles ->children() as $user){
			 	 		$authorizedRoles[trim($user)] = true;
			 		}
			 		$pluginClassNameNewObj->setAuthorizedRoles($authorizedRoles);
					foreach($xmlObj->authorizeModules ->children() as $module){							
		 	 			$authorizeModules[trim($module)] = true;
			 		}
			 		$pluginClassNameNewObj->setAuthorizeModules($authorizeModules);
					return  $pluginClassNameNewObj;
			}else{
				throw new PlugInFactoryException(PlugInFactoryException::PLUGIN_INSTALL_ERROR);
			}
		}else{
			return FALSE ;
		}
	}
}
?>