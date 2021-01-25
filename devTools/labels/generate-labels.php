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

require_once __DIR__ . "/../vendor/autoload.php";
require_once "LabelGenerator.php";
$climate = new \League\CLImate\CLImate;

$currentDirectory = dirname(__FILE__);
$rootPath = $currentDirectory . "/../../";

$jsFileName = "labelHelper.js";
$jsFileCopiedName = "labelHelper-copied.js";
$angularLabelsFileName = "angular_labels.json";
$nonAngularLabelsFileName = "symfony_labels.json";

$angularAppHelpPath = $rootPath. "symfony/web/client/app/help";
$helpTaskDirectory = $rootPath."symfony/plugins/orangehrmHelpPlugin/lib/task";
$helpTask = $helpTaskDirectory.'/orangehrmAppRoutesTask.class.php';

$climate->border("==",68);
$climate->backgroundGreen()->black()->out("OrangeHRM Generate Labels");
$climate->border("==",68);

$generateDiff = false;
$keepTempFiles = false;

while (($param = array_shift($argv)) !== NULL) {
    switch ($param) {
        case '--generate-diff':
            $generateDiff = (array_shift($argv) == 'true') ? true : false;
            break;
        case '--keep-new-files':
            $keepTempFiles = (array_shift($argv) == 'true') ? true : false;
            break;
        default:
            break;
    }
}

echo "Deleting Previously Created Temporary Files\n";
if (file_exists($currentDirectory."/".$angularLabelsFileName)) {
    unlink($currentDirectory."/".$angularLabelsFileName);
}
if (file_exists($currentDirectory."/".$nonAngularLabelsFileName)) {
    unlink($currentDirectory."/".$nonAngularLabelsFileName);
}

$taskDirectoryExist = true;
$taskExist = true;
echo "Copying symfony task\n";
if (!file_exists($helpTaskDirectory)) {
    $taskDirectoryExist = false;
    $directoryCreated = mkdir($helpTaskDirectory, 0775, true);
    if(!$directoryCreated) {
        $climate->to('error')->red("Cannot create $helpTaskDirectory !!!");
        exit();
    }
}

if (!file_exists($helpTask)) {
    $taskExist = false;
    $taskCopied = copy('orangehrmAppRoutesTask.class.php', $helpTask);
    if (!$taskCopied) {
        $climate->to('error')->red("Cannot create $helpTask !!!");
        if(!$taskDirectoryExist) {
            rmdir($helpTaskDirectory);
        }
        exit();
    }
}


chdir($rootPath . 'symfony');
echo "Running symfony cc\n";
exec("php symfony cc", $symfonyCcResponse);

if (count($symfonyCcResponse) == 0) {
    $climate->to('error')->red('Symfony cc Execution Failed !!!');
    exit();
}

echo "Running symfony task\n";
exec("php symfony orangehrm:routes-with-labels > $currentDirectory/$nonAngularLabelsFileName", $symfonyTaskResponse);
if(!file_exists($currentDirectory."/".$nonAngularLabelsFileName)){
    $climate->to('error')->red('Task Execution Failed !!!');
    exit();
}

echo "Deleting copied symfony task\n";
if (!$taskExist && file_exists($helpTask)) {
    unlink($helpTask);
}

if (!$taskDirectoryExist && file_exists($helpTaskDirectory)) {
    rmdir($helpTaskDirectory);
}

echo "Running symfony cc again\n";
exec("php symfony cc", $symfonyCcResponse2);
if (count($symfonyCcResponse2) == 0) {
    $climate->to('error')->red('Symfony cc Execution Failed !!!');
}

if ($generateDiff) {
    $labelGenerator = new LabelGenerator();
    $labelGenerator->getDifferences();
}

if (!$keepTempFiles) {
    echo "Deleting Temporary Files\n";
    if (file_exists($currentDirectory."/".$angularLabelsFileName)) {
        unlink($currentDirectory."/".$angularLabelsFileName);
    }
    if (file_exists($currentDirectory."/".$nonAngularLabelsFileName)) {
        unlink($currentDirectory."/".$nonAngularLabelsFileName);
    }
}
