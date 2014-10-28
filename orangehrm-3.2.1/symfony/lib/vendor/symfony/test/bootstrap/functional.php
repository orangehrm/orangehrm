<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// setup expected test environment (per check_configuration.php)
ini_set('magic_quotes_runtime', 'off');
ini_set('session.auto_start', 'off');
ini_set('arg_separator.output', '&amp;');
ini_set('allow_url_fopen', 'on');

if (!isset($root_dir))
{
  $root_dir = realpath(dirname(__FILE__).sprintf('/../%s/fixtures', isset($type) ? $type : 'functional'));
}

require_once $root_dir.'/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);

// remove all cache
sf_functional_test_shutdown();
register_shutdown_function('sf_functional_test_shutdown');

sfContext::createInstance($configuration);

function sf_functional_test_shutdown_cleanup()
{
  sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));
  sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));

  $sf_root_dir = sys_get_temp_dir().'/sf_test_project';
  if (is_dir($sf_root_dir))
  {
    sfToolkit::clearDirectory($sf_root_dir);
    @rmdir($sf_root_dir);
  }

  $sessions = glob(sys_get_temp_dir().'/sessions*');
  $tmp_files = glob(sys_get_temp_dir().'/sf*');
  $files = array_merge(empty($sessions) ? array() : $sessions, empty($tmp_files) ? array() : $tmp_files);
  foreach ($files as $file)
  {
    if (is_dir($file))
    {
      sfToolkit::clearDirectory($file);
      @rmdir($file);
    }
    else
    {
      @unlink($file);
    }
  }
}

function sf_functional_test_shutdown()
{
  // try/catch needed due to http://bugs.php.net/bug.php?id=33598
  try
  {
    sf_functional_test_shutdown_cleanup();
  }
  catch (Exception $e)
  {
    echo $e.PHP_EOL;
  }
}

return true;
