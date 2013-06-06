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

$t = new lime_test(40);

@include_once('PEAR.php');
if (!class_exists('PEAR'))
{
  $t->skip('PEAR must be installed', 40); 
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

class myPluginManager extends sfPluginManager
{
  protected
    $mainPackageVersion = '1.0.0';

  public function setMainPackageVersion($version)
  {
    $this->mainPackageVersion = $version;
    $this->configure();
  }

  public function configure()
  {
    $this->environment->registerChannel('pear.example.com', true);

    $mainPackage = new PEAR_PackageFile_v2_rw();
    $mainPackage->setPackage('sfMainPackage');
    $mainPackage->setChannel('pear.example.com');
    $mainPackage->setConfig($this->environment->getConfig());
    $mainPackage->setPackageType('php');
    $mainPackage->setAPIVersion('1.0.0');
    $mainPackage->setAPIStability('stable');
    $mainPackage->setReleaseVersion($this->mainPackageVersion);
    $mainPackage->setReleaseStability('stable');
    $mainPackage->setDate(date('Y-m-d'));
    $mainPackage->setDescription('sfMainPackage');
    $mainPackage->setSummary('sfMainPackage');
    $mainPackage->setLicense('MIT License');
    $mainPackage->clearContents();
    $mainPackage->resetFilelist();
    $mainPackage->addMaintainer('lead', 'fabpot', 'Fabien Potencier', 'fabien.potencier@symfony-project.com');
    $mainPackage->setNotes('-');
    $mainPackage->setPearinstallerDep('1.4.3');
    $mainPackage->setPhpDep('5.1.0');

    $this->environment->getRegistry()->deletePackage('sfMainPackage', 'pear.example.com');
    if (!$this->environment->getRegistry()->addPackage2($mainPackage))
    {
      throw new sfException('Unable to register our sfMainPackage');
    }
  }

  protected function isPluginCompatibleWithDependency($dependency)
  {
    if (isset($dependency['channel']) && 'sfMainPackage' == $dependency['name'] && 'pear.example.com' == $dependency['channel'])
    {
      return $this->checkDependency($dependency);
    }

    return true;
  }
}

// ->initialize()
$t->diag('->initialize()');
$environment = new sfPearEnvironment($dispatcher, $options);
$pluginManager = new myPluginManager($dispatcher, $environment);
$t->is($pluginManager->getEnvironment(), $environment, '->initialize() takes a sfPearEnvironment as its second argument');

// ->installPlugin() ->uninstallPlugin()
$t->diag('->installPlugin() ->uninstallPlugin');
$pluginManager->installPlugin('sfTestPlugin');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.0.3', '->installPlugin() installs the latest stable version');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$pluginManager->installPlugin('sfTestPlugin', array('stability' => 'beta'));
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.0.4', '->installPlugin() can take a stability option');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$pluginManager->installPlugin('sfTestPlugin', array('version' => '1.0.0'));
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.0.0', '->installPlugin() can take a version option');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$t->diag('Try to install a version that won\'t work with our main package');

try
{
  $pluginManager->installPlugin('sfTestPlugin', array('version' => '1.1.3'));

  $t->fail('->installPlugin() throws an exception if you try to install a version that is not compatible with our main package');
}
catch (sfPluginDependencyException $e)
{
  $t->pass('->installPlugin() throws an exception if you try to install a version that is not compatible with our main package');
}

$t->diag('Upgrade our main package to 1.1.0');
$pluginManager->setMainPackageVersion('1.1.0');

$pluginManager->installPlugin('sfTestPlugin');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.3', '->installPlugin() installs the latest stable version');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$pluginManager->installPlugin('sfTestPlugin', array('stability' => 'beta'));
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.4', '->installPlugin() takes a stability as its 4th argument');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$t->diag('try to uninstall a non installed plugin');
$t->ok(!$pluginManager->uninstallPlugin('sfFooPlugin'), '->uninstallPlugin() returns false if the plugin is not installed');

$t->diag('try to install a non existant plugin');
try
{
  $pluginManager->installPlugin('sfBarPlugin');

  $t->fail('->installPlugin() throws an exception if the plugin does not exist');
}
catch (sfPluginException $e)
{
  $t->pass('->installPlugin() throws an exception if the plugin does not exist');
}

$pluginManager->installPlugin('http://pear.example.com/get/sfTestPlugin/sfTestPlugin-1.1.4.tgz');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.4', '->installPlugin() can install a PEAR package hosted on a website');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

$pluginManager->installPlugin(dirname(__FILE__).'/fixtures/http/pear.example.com/get/sfTestPlugin/sfTestPlugin-1.1.4.tgz');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.4', '->installPlugin() can install a local PEAR package');

$t->ok($pluginManager->uninstallPlugin('sfTestPlugin'), '->uninstallPlugin() returns true if the plugin is properly uninstalled');
$t->ok(!is_file($temp.'/plugins/sfTestPlugin/VERSION'), '->uninstallPlugin() uninstalls a plugin');

// ->getPluginVersion()
$t->diag('->getPluginVersion()');
$pluginManager->setMainPackageVersion('1.0.0');
$t->is($pluginManager->getPluginVersion('sfTestPlugin'), '1.0.3', '->getPluginVersion() returns the latest version available for the plugin');
$t->is($pluginManager->getPluginVersion('sfTestPlugin', 'beta'), '1.0.4', '->getPluginVersion() takes a stability as its second argument');
$pluginManager->setMainPackageVersion('1.1.0');
$t->is($pluginManager->getPluginVersion('sfTestPlugin'), '1.1.3', '->getPluginVersion() returns the latest version available for the plugin');
$t->is($pluginManager->getPluginVersion('sfTestPlugin', 'beta'), '1.1.4', '->getPluginVersion() takes a stability as its second argument');
$t->is($pluginManager->getPluginVersion('sfTestPlugin', 'alpha'), '1.1.4', '->getPluginVersion() takes a stability as its second argument');

// ->getInstalledPlugins()
$t->diag('->getInstalledPlugins()');
$pluginManager->installPlugin('sfTestPlugin');
$installed = $pluginManager->getInstalledPlugins();
$a = array($installed[0]->getName(), $installed[1]->getName());
$b = array('sfTestPlugin', 'sfMainPackage');
sort($a);
sort($b);
$t->is($a, $b, '->getInstalledPlugin() returns an array of installed packages');
$t->is(count($installed), 2, '->getInstalledPlugin() returns an array of installed packages');
$pluginManager->uninstallPlugin('sfTestPlugin');

$t->diag('install a plugin with a dependency must fail');
try
{
  $pluginManager->installPlugin('sfFooPlugin');
  $t->fail('->installPlugin() throws an exception if the plugin needs a dependency to be installed');
}
catch (sfPluginDependencyException $e)
{
  $t->pass('->installPlugin() throws an exception if the plugin needs a dependency to be installed');
}

$t->diag('install a plugin with a dependency and force installation of all dependencies');
$pluginManager->installPlugin('sfFooPlugin', array('install_deps' => true));
$t->is(file_get_contents($temp.'/plugins/sfFooPlugin/VERSION'), '1.0.0', '->installPlugin() can take a install_deps option');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.3', '->installPlugin() can take a install_deps option');
$pluginManager->uninstallPlugin('sfFooPlugin');
$pluginManager->uninstallPlugin('sfTestPlugin');

$pluginManager->installPlugin('sfTestPlugin', array('version' => '1.1.4'));
$pluginManager->installPlugin('sfFooPlugin');
$t->is(file_get_contents($temp.'/plugins/sfFooPlugin/VERSION'), '1.0.0', '->installPlugin() installs a plugin if all dependencies are installed');
$t->is(file_get_contents($temp.'/plugins/sfTestPlugin/VERSION'), '1.1.4', '->installPlugin() installs a plugin if all dependencies are installed');
$pluginManager->uninstallPlugin('sfFooPlugin');
$pluginManager->uninstallPlugin('sfTestPlugin');

$t->diag('try to uninstall a plugin with a depedency must fail');
$pluginManager->installPlugin('sfTestPlugin', array('version' => '1.1.4'));
$pluginManager->installPlugin('sfFooPlugin');
try
{
  $pluginManager->uninstallPlugin('sfTestPlugin');
  $t->fail('->uninstallPlugin() throws an exception if you try to uninstall a plugin that is needed for another one');
}
catch (sfPluginException $e)
{
  $t->pass('->uninstallPlugin() throws an exception if you try to uninstall a plugin that is needed for another one');
}
$pluginManager->uninstallPlugin('sfFooPlugin');
$pluginManager->uninstallPlugin('sfTestPlugin');

$t->diag('install a plugin with a dependency which is installed by with a too old version');
$pluginManager->setMainPackageVersion('1.0.0');
$pluginManager->installPlugin('sfTestPlugin', array('version' => '1.0.4'));
$pluginManager->setMainPackageVersion('1.1.0');
try
{
  $pluginManager->installPlugin('sfFooPlugin');
  $t->fail('->installPlugin() throws an exception if you try to install a plugin with a dependency that is installed but not in the right version');
}
catch (sfPluginDependencyException $e)
{
  $t->pass('->installPlugin() throws an exception if you try to install a plugin with a dependency that is installed but not in the right version');
}
$pluginManager->uninstallPlugin('sfTestPlugin');

$t->diag('install a plugin with a dependency which is installed with a too old version and you want automatic upgrade');
$pluginManager->setMainPackageVersion('1.0.0');
$pluginManager->installPlugin('sfTestPlugin', array('version' => '1.0.4'));
$pluginManager->setMainPackageVersion('1.1.0');
$pluginManager->installPlugin('sfFooPlugin', array('install_deps' => true));
$t->is(file_get_contents($temp.'/plugins/sfFooPlugin/VERSION'), '1.0.0', '->installPlugin() installs a plugin if all dependencies are installed');
$pluginManager->uninstallPlugin('sfFooPlugin');
$pluginManager->uninstallPlugin('sfTestPlugin');

// teardown
sfToolkit::clearDirectory($temp);
rmdir($temp);
