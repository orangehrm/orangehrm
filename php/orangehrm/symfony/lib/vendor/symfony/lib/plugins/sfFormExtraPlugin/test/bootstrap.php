<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!isset($_SERVER['SYMFONY']))
{
  die("You must set the \"SYMFONY\" environment variable to the symfony lib dir (export SYMFONY=/path/to/symfony/lib/).\n");
}

require_once $_SERVER['SYMFONY'].'/vendor/lime/lime.php';
require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
