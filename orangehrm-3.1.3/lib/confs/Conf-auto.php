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


/**
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
 * with all those details and keepin the file in appropriate directory as discussed
 * in (R1).
 *
 * R4)
 * This file should be renamed from Conf-auto.php to Conf.php in the existing
 * directory (<OrangeHRM-directory>/lib/confs)
 *
 */

ob_start();

define('CONF_PATH', "/var/www/hosted/configurations");
//define('URL_PREFIX_TO_INSTANCE_NAME',"http://");
define('URL_POSTFIX_TO_INSTANCE_NAME',".orangehrm.com");


$selectedInstance = preg_replace("/".URL_POSTFIX_TO_INSTANCE_NAME."$/", "", $_SERVER['SERVER_NAME']);

if(is_file(CONF_PATH . "/" .$selectedInstance . "/Conf.php")) {
	require_once CONF_PATH . "/" .$selectedInstance . "/Conf.php";
} else {
	header("Location: ./expired/");		//expired
	exit(0);
}

?>