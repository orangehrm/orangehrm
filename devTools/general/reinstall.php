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

mysql_connect($c->dbhost, $c->dbuser, $c->dbpass);

if (mysql_query("DROP DATABASE `{$c->dbname}`")) {
    
    echo "Existing '{$c->dbname}' database was deleted.<br>\n";
    
    if (mysql_query("CREATE DATABASE `{$c->dbname}`")) {
        
        echo "Created new '{$c->dbname}' database.<br>\n";
        mysql_select_db($c->dbname);
        executeDbQueries($rootPath);
        createDefaultUser();
        
    } else {
        echo "Couldn't create new '{$c->dbname}' database.<br>\n";
    }    
    
} else {
    echo "Couldn't delete existing database '{$c->dbname}'. Error details: ". mysql_error() ."<br>\n";
}

//===========================================================

function executeDbQueries($rootPath) {
    
    $dbscript1      = $rootPath . 'dbscript/dbscript-1.sql';
    $dbscript2      = $rootPath . 'dbscript/dbscript-2.sql';
    
    $queryList  = getQueries($dbscript1);
    $i          = 1;    

    foreach ($queryList as $q) {
        
        if (!mysql_query($q)) {
            echo "Error with create query $i: $q. Error details: " . mysql_error() . ".<br>\n";
            die;
        }
        
        $i++;
        
    }
    
    if (!mysql_error()) {
        echo "Data tables were created successfully.<br>\n";
    }
    
    $queryList = getQueries($dbscript2);
    
    foreach ($queryList as $q) {
        
        if (!mysql_query($q)) {
            echo "Error with insert query $i: $q. Error details: " . mysql_error() . ".<br>\n";
            die;
        }
        
        $i++;
        
    }
    
    if (!mysql_error()) {
        echo "Default data was inserted successfully.<br>\n";
    }    
    
}

function getQueries($path) {
    
    $queryString    = trim(file_get_contents($path));
    $rawQueryList   = preg_split('/;\s*$/m', $queryString);    
    $queryList      = array();
    
    foreach ($rawQueryList as $query) {
        
        $query = trim($query);
       
        if (!empty($query)) {
            $queryList[] = $query;
        }
        
    }
    return $queryList;
    
}

function createDefaultUser() {
    
    $q = "INSERT INTO `ohrm_user` ( `user_name`, `user_password`,`user_role_id`) VALUES ('admin','".md5('admin')."','1')";
    
    if (mysql_query($q)) {
        echo "Successfully created default Admin. Username: admin, Password: admin<br>\n";
    } else {
        echo "Error when creating default admin, query: $q. Error details: " . mysql_error() . ".<br>\n";
        die;
    }    
    
}

function displayQueries($queryList) {
    
    $i = 1;
    
    foreach ($queryList as $query) {
        
        echo "($i) $query <br><br>\n\n";
        $i++;
        
    }
    
}
