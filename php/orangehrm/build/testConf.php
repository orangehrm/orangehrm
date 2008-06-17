<?php

	/*
	 * Create a file named myconf.php in the same
	 * directory and set the following to match
	 * your environment.
	 *
	 * $rootPath = "/var/www/orangehrm";
	 * $webPath = "http://localhost/orangehrm";
     * $mysqlRootPassword = 'secret';
	 */

	require 'myconf.php';

	if (!defined('ROOT_PATH')) {
	    define('ROOT_PATH', $rootPath);
	}

	if (!defined('WPATH')) {
	    define('WPATH', $webPath);
	}

	if (!defined('MYSQL_ROOT_PASSWORD')) {
        /* Default to empty mysql root password */
        if (!isset($mysqlRootPassword)) {
            $mysqlRootPassword = '';
        }
	    define('MYSQL_ROOT_PASSWORD', $mysqlRootPassword);
	}

?>
