<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(error_reporting() & ~E_STRICT);

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(5);

@include_once('PEAR.php');
if (!class_exists('PEAR'))
{
  $t->skip('PEAR must be installed', 5); 
  return;
}

require_once dirname(__FILE__).'/sfPearDownloaderTest.class.php';
require_once dirname(__FILE__).'/sfPearRestTest.class.php';
require_once dirname(__FILE__).'/sfPluginTestHelper.class.php';

// setup
$temp = tempnam('/tmp/sf_plugin_test', 'tmp');
unlink($temp);
mkdir($temp, 0777, true);

define('SF_PLUGIN_TEST_DIR', $temp);

$options = array(
  'plugin_dir'            => $temp.'/plugins',
  'cache_dir'             => $temp.'/cache',
  'preferred_state'       => 'stable',
  'rest_base_class'       => 'sfPearRestTest',
  'downloader_base_class' => 'sfPearDownloaderTest',
);

$dispatcher = new sfEventDispatcher();
$environment = new sfPearEnvironment($dispatcher, $options);
$environment->registerChannel('pear.example.com', true);

$rest = $environment->getRest();

// ->getPluginVersions()
$t->diag('->getPluginVersions()');
$t->is($rest->getPluginVersions('sfTestPlugin'), array('1.1.3', '1.0.3', '1.0.0'), '->getPluginVersions() returns an array of stable versions for a plugin');
$t->is($rest->getPluginVersions('sfTestPlugin', 'stable'), array('1.1.3', '1.0.3', '1.0.0'), '->getPluginVersions() accepts stability as a second parameter and returns an array of versions for a plugin based on stability');
$t->is($rest->getPluginVersions('sfTestPlugin', 'beta'), array('1.0.4', '1.1.4', '1.1.3', '1.0.3', '1.0.0'), '->getPluginVersions() accepts stability as a second parameter and returns an array of versions for a plugin based on stability cascade (beta includes stable)');

// ->getPluginDependencies()
$t->diag('->getPluginDependencies()');
$dependencies = $rest->getPluginDependencies('sfTestPlugin', '1.1.4');
$t->is($dependencies['required']['package']['min'], '1.1.0', '->getPluginDependencies() returns an array of dependencies');

// ->getPluginDownloadURL()
$t->diag('->getPluginDownloadURL()');
$t->is($rest->getPluginDownloadURL('sfTestPlugin', '1.1.3', 'stable'), 'http://pear.example.com/get/sfTestPlugin/sfTestPlugin-1.1.3.tgz', '->getPluginDownloadURL() returns a plugin URL');

// teardown
sfToolkit::clearDirectory($temp);
rmdir($temp);
