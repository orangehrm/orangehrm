<?php
/*
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 * This is Configuration-File Loader. In case of the hosted solution
 * this should be used with all the configuration files defined in 
 * CONF_PATH constant   
 */

define('CONF_PATH', "C:\temp\trunk\php\orangehrm\lib\confs");
define('URL_PREFIX_TO_INSTANCE_NAME',"http://localhost/");
define('URL_POSTFIX_TO_INSTANCE_NAME',"/");

function selfURL() { $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; }
function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }

//$url = selfURL();

$startpoint = strlen(URL_PREFIX_TO_INSTANCE_NAME);
$endpoint = strpos($url, URL_POSTFIX_TO_INSTANCE_NAME, $startpoint);

$selectedInstance = substr ($url, $startpoint, $endpoint-$startpoint);

require_once CONF_PATH . "/" .$selectedInstance . "/Conf.php";
?>
