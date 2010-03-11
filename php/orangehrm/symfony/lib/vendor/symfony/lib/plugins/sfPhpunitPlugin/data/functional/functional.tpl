<?php
// calculate sf_root_dir by hand, sfConfig isn't available yet
// we assume that this file is located in test/phpunit/bootstrap
define('SFPHPUNIT_F_ROOT', $sf_root = realpath(dirname(__FILE__).'/../../..'));

// autoloading does not exist yet
require_once SFPHPUNIT_F_ROOT.'/plugins/sfPhpunitPlugin/lib/test/sfBasePhpunitFunctionalTestCase.class.php';

// include phpunit
require_once 'PHPUnit/Framework.php';