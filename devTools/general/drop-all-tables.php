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

try {
    $dsn = "mysql:dbname={$c->dbname};host={$c->dbhost}";
    $pdo = new PDO($dsn, $c->dbuser, $c->dbpass);
    
    $result = $pdo->query('SHOW TABLES');
    
    $tables = $result->fetchAll(PDO::FETCH_COLUMN, 0);

    if (count($tables) > 0) {
        $pdo->exec("SET foreign_key_checks = 0");

        echo "Dropping tables:\n";
        foreach ($tables as $table) {

            echo "{$table}\n";
            $pdo->exec("DROP TABLE " . $table);
        }
        $pdo->exec("SET foreign_key_checks = 1");
    
    } else {
        echo "No tables found in DB " . $c->dbname . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

