<?php
$dir = realpath(dirname(__FILE__).'/../..');
require_once($dir.'/config/ProjectConfiguration.class.php');

// Core autoload
new ProjectConfiguration($dir);

// Test lib
require_once($dir . '/lib/test/myUnitTestCase.php');
require_once($dir . '/lib/test/myFunctionalTestCase.php');
