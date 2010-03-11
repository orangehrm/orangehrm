<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../lib/vendor/lime/lime.php');

class lime_symfony extends lime_harness
{
  protected function get_relative_file($file)
  {
    $file = str_replace(DIRECTORY_SEPARATOR, '/', str_replace(array(
      realpath($this->base_dir).DIRECTORY_SEPARATOR,
      realpath($this->base_dir.'/../lib/plugins').DIRECTORY_SEPARATOR,
      $this->extension,
    ), '', $file));

    return preg_replace('#^(.*?)Plugin/test/(unit|functional)/#', '[$1] $2/', $file);
  }
}

require_once(dirname(__FILE__).'/../../lib/util/sfToolkit.class.php');
if($files = glob(sfToolkit::getTmpDir().DIRECTORY_SEPARATOR.'/sf_autoload_unit_*'))
{
  foreach ($files as $file)
  {
    unlink($file);
  }
}

// update sfCoreAutoload
require_once(dirname(__FILE__).'/../../lib/autoload/sfCoreAutoload.class.php');
sfCoreAutoload::make();

$h = new lime_symfony(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__).'/..');

$h->register(sfFinder::type('file')->prune('fixtures')->name('*Test.php')->in(array_merge(
  // unit tests
  array($h->base_dir.'/unit'),
  glob($h->base_dir.'/../lib/plugins/*/test/unit'),

  // functional tests
  array($h->base_dir.'/functional'),
  glob($h->base_dir.'/../lib/plugins/*/test/functional'),

  // other tests
  array($h->base_dir.'/other')
)));

exit($h->run() ? 0 : 1);
