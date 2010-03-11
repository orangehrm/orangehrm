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
  $root_dir = realpath(dirname(__FILE__).sprintf('/../%s/fixtures/project', isset($type) ? $type : 'functional'));
}

require_once $root_dir.'/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);

sfContext::createInstance($configuration);

// remove all cache
sf_functional_test_shutdown();

register_shutdown_function('sf_functional_test_shutdown');

function sf_functional_test_shutdown()
{
  sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));
  sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));

  $sf_root_dir = sfToolkit::getTmpDir().'/sf_test_project';
  if(is_dir($sf_root_dir))
  {
    sfToolkit::clearDirectory($sf_root_dir);
    @rmdir($sf_root_dir);
  }

  $sessions = glob(sfToolkit::getTmpDir().'/sessions*');
  $tmp_files = glob(sfToolkit::getTmpDir().'/sf*');
  $files = array_merge(empty($sessions) ? array() : $sessions, empty($tmp_files) ? array() : $tmp_files);
  foreach ($files as $file)
  {
    if(is_dir($file))
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

return true;
