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
 */

$rootPath = dirname(__FILE__) . "/../../";

$files = [
    'build/build.xml',
    'src/lib/config/Config.php',
    'src/plugins/orangehrmCorePlugin/test/fixtures/testcases/AboutOrganizationTestCase.yaml',
];

if ($argc != 3) {
    echo "Usage: php update-version.php [old-version] [new-version]\n\n";
    echo "Example:\n\n";
    echo "php update-version.php 2.6-alpha.3 2.6-alpha.4\n";
    exit();
}

$oldVersion = $argv[1];
$newVersion = $argv[2];

foreach ($files as $file) {
    $fileName = $rootPath . $file;
    $contents = file_get_contents($fileName);
    $contents = str_replace($oldVersion, $newVersion, $contents);
    file_put_contents($fileName, $contents);
}
