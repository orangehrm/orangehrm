<?php
// This constant could be used for including some custom classes, if autoloading is not active.
// e.g: require_once SFPHPUNIT_ROOT.'/lib/myClass.class.php';
define('SFPHPUNIT_ROOT', $sf_root = realpath(dirname(__FILE__).'/../../..'));

require_once($sf_root.'/config/ProjectConfiguration.class.php');
$configuration = new ProjectConfiguration($sf_root);
$sf_symfony_lib_dir = $configuration->getSymfonyLibDir();

require_once 'PHPUnit/Framework.php';