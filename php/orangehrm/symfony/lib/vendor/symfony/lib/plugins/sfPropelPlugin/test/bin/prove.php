<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../../../../lib/vendor/lime/lime.php';
require_once dirname(__FILE__).'/../../../../../lib/util/sfToolkit.class.php';
require_once dirname(__FILE__).'/../../../../../lib/util/sfFinder.class.php';

if ($files = glob(sfToolkit::getTmpDir().DIRECTORY_SEPARATOR.'/sf_autoload_unit_*'))
{
  foreach ($files as $file)
  {
    unlink($file);
  }
}

$h = new lime_harness(new lime_output_color);
$h->base_dir = realpath(dirname(__FILE__).'/..');

$h->register(sfFinder::type('file')->prune('fixtures')->name('*Test.php')->in(array(
  $h->base_dir.'/unit',
  $h->base_dir.'/functional',
)));

exit($h->run() ? 0 : 1);
