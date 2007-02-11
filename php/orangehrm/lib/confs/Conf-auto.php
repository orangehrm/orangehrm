<?php
/*
 * This is Configuration-File Loader. In case of the hosted solution
 * this should be used to automaitcally select the appropriate configuration
 * file.
 * 
 * Requirements:
 * 
 * R1)
 * all instances of configuration files should reside within directory path 
 * given by CONF_PATH. In this path you should create a sub-directory named 
 * exacty as the instance name with the Configuration file named Conf.php
 * This configuration file remains unchanged from the format of Ordinary 
 * OrangeHRM Configuration files.
 * 
 * ex: for instance 'xyz' and main directory of configuration files (CONF_PATH)
 *     being /var/www/hosted/configurations then you create a sub-directory 
 *     called 'xyz' within it and create appropriate Conf.php within it.
 * 
 * R2)
 * when setup, when you take the complete URL it should be broken down into 
 * 3 regions. They are in sequence 1) prefix to instance name 
 * 2) Instance name 3) postfix to instance name
 * 
 * ex: for URL form http://xyz.orangehrm.com (then the instance name is 'xyz')
 *     the below defined constants should read as
 *   
 * 			define('URL_PREFIX_TO_INSTANCE_NAME',"http://");
 * 			define('URL_POSTFIX_TO_INSTANCE_NAME',".orangehrm.com");
 * 
 * R3)
 * Installation of OrangeHRM would mean simply creating the database with 
 * with appropriate database users, and modifying a standard OrangeHRM Conf.php
 * with all those details and keepin the file in appropriate director as discussed 
 * in (R1).
 * 
 * R4)
 * This file should be renamed from Conf-auto.php to Conf.php in the existing 
 * directory (<OrangeHRM-directory>/lib/confs)
 *   
 */

define('CONF_PATH', "/var/www/hosted/configurations");
define('URL_PREFIX_TO_INSTANCE_NAME',"http://");
define('URL_POSTFIX_TO_INSTANCE_NAME',".orangehrm.com");

function selfURL() { $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; }
function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }

$url = selfURL();

$startpoint = strlen(URL_PREFIX_TO_INSTANCE_NAME);
$endpoint = strpos($url, URL_POSTFIX_TO_INSTANCE_NAME, $startpoint);

$selectedInstance = substr ($url, $startpoint, $endpoint-$startpoint);

require_once CONF_PATH . "/" .$selectedInstance . "/Conf.php";
?>
