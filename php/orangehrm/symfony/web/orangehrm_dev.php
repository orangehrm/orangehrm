<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
{
  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

/* Added for compatibility with current orangehrm code 
 * OrangeHRM Root directory 
 */
define('ROOT_PATH', dirname(__FILE__) . '/../../');
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
define('WPATH', $scriptPath . "/../../");

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
