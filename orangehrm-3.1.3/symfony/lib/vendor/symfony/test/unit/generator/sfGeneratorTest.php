<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please generator the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(0);

class myGenerator extends sfGenerator
{
  public function generate($params = array()) {}
}

class ProjectConfiguration extends sfProjectConfiguration
{
}

$manager = new sfGeneratorManager(new ProjectConfiguration());
$generator = new myGenerator($manager);
