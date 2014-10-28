<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(4);

class ProjectConfiguration extends sfProjectConfiguration
{
}

$configuration = new ProjectConfiguration(dirname(__FILE__).'/../../lib', new sfEventDispatcher());

$parameters = array(
  'name'        => 'doctrine',
  'dsn'         => 'sqlite::memory',
  'attributes'  => array(
    'use_native_enum'   => true,
    'validate'          => 'all',
    'tblname_format'    => 'test_%s',
  ),
);

$p = new sfDoctrineDatabase($parameters);
$t->is($p->getDoctrineConnection()->getName(), 'doctrine', 'initialize() - creates a valid doctrine configuration from parameters');
$t->is($p->getDoctrineConnection()->getAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM), true, 'initialize() - setups doctrine attributes - attribute value is not a string');
$t->is($p->getDoctrineConnection()->getAttribute(Doctrine_Core::ATTR_VALIDATE), Doctrine_Core::VALIDATE_ALL, 'initialize() - setups doctrine attributes - attribute value is a string and constant exists');
$t->is($p->getDoctrineConnection()->getAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT), $parameters['attributes']['tblname_format'], 'initialize() - setups doctrine attributes - attribute value is a string and constant not exists');


