<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(1, new lime_output_color());

$parameters = array(
    'name'       => 'doctrine',
    'dsn'        => 'sqlite::memory');

class ProjectConfiguration extends sfProjectConfiguration
{
}

$configuration = new ProjectConfiguration(dirname(__FILE__).'/../../lib', new sfEventDispatcher());

$p = new sfDoctrineDatabase($parameters);
$t->is($p->getDoctrineConnection()->getName(), 'doctrine', 'initialize() - creates a valid doctrine configuration from parameters');