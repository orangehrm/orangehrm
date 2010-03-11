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

$_test_dir = realpath(dirname(__FILE__).'/..');
require_once($_test_dir.'/../lib/vendor/lime/lime.php');
require_once($_test_dir.'/../lib/config/sfConfig.class.php');
sfConfig::set('sf_symfony_lib_dir', realpath($_test_dir.'/../lib'));

require_once(dirname(__FILE__).'/../../lib/autoload/sfCoreAutoload.class.php');
sfCoreAutoload::register();

require_once(dirname(__FILE__).'/../../lib/util/sfToolkit.class.php');
sfConfig::set('sf_test_cache_dir', sfToolkit::getTmpDir().'/sf_test_project');

// remove all test cache
sf_unit_test_shutdown();

// create test cache dir
$sf_root_dir = sfToolkit::getTmpDir().'/sf_test_project';
@mkdir($sf_root_dir, 0777, true);

register_shutdown_function('sf_unit_test_shutdown');

function sf_unit_test_shutdown()
{
  $sf_root_dir = sfToolkit::getTmpDir().'/sf_test_project';
  if(is_dir($sf_root_dir))
  {
    sfToolkit::clearDirectory($sf_root_dir);
    @rmdir($sf_root_dir);
  }

  $sessions = glob(sfToolkit::getTmpDir().'/sessions*');
  $tmp_files = glob(sfToolkit::getTmpDir().'/sf*');

  $files = array_merge((empty($sessions) ? array() : $sessions), (empty($tmp_files) ? array() : $tmp_files));
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
