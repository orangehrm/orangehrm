<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__).'/../../../../../test/bootstrap/unit.php');

if (!isset($root_dir))
{
  $root_dir = realpath(dirname(__FILE__).sprintf('/../%s/fixtures', isset($type) ? $type : 'functional'));
}

include $root_dir.'/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

function sf_functional_test_shutdown()
{
  sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));
  sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));
  $databases = glob(sfConfig::get('sf_data_dir') . '/*.sqlite');
  foreach ($databases as $database)
  {
    unlink($database);
  }
}

// remove all cache
sf_functional_test_shutdown();

$configuration->initializeDoctrine();
if (isset($fixtures))
{
  $configuration->loadFixtures($fixtures);
}

register_shutdown_function('sf_functional_test_shutdown');

return true;