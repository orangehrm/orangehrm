<?php
/*
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

$rootPath = dirname(__FILE__) . "/../../";
$confPath = $rootPath . "lib/confs/Conf.php";

require_once $confPath;

$c = new Conf();

$testDb = 'test_' . $c->dbname;
$dbUser = $c->dbuser;
$dbHost = $c->dbhost;

$tempFile = tempnam(sys_get_temp_dir(), 'ohrmtestdb');
 

if ($argc > 1) {
	$mysqlRootPwd = $argv[1];
} else {
	$mysqlRootPwd = "";
	echo "Please enter mysql root password when prompted.\n";
}


$createdbStatement = "DROP DATABASE IF EXISTS `{$testDb}`; CREATE DATABASE `{$testDb}`;USE `{$testDb}`;" .
                     "GRANT ALL on `{$testDb}`.* to \"{$dbUser}\"@\"{$dbHost}\";\n";

file_put_contents($tempFile, $createdbStatement);

$cmd = "mysqldump -u root -p{$mysqlRootPwd} --add-drop-table --routines --skip-triggers {$c->dbname} >> {$tempFile}";

$output = '';
$returnStatus = '';

exec($cmd, $output, $returnStatus);

if ($returnStatus !== 0) {
    echo "mysqldump failed.<br>\n";
    exit(1);
}


$cmd = "mysql -u root -p{$mysqlRootPwd}  < {$tempFile}";

exec($cmd, $output, $returnStatus);

if ($returnStatus !== 0) {
    echo "mysql data insert failed.<br>\n";
    exit(2);
}

unlink($tempFile);

echo "test db {$testDb} created.\n";

