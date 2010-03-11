<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
sfToolkit::addIncludePath(sfConfig::get('sf_symfony_lib_dir').'/plugins/sfPropelPlugin/lib/vendor');

$t = new lime_test(1, new lime_output_color());

$configuration = array(
  'propel' => array(
    'datasources' => array(
      'propel' => array(
        'adapter' => 'mysql',
        'connection' => array(
          'dsn' => 'mysql:dbname=testdb;host=localhost',
          'user' => 'foo',
          'password' => 'bar',
          'classname' => 'PropelPDO',
          'options' => array(
            'ATTR_PERSISTENT' => true,
            'ATTR_AUTOCOMMIT' => false,
          ),
          'settings' => array(
            'charset' => array('value' => 'utf8'),
            'queries' => array(),
          ),
        ),
      ),
      'default' => 'propel',
    ),
  ),
);

$parametersTests = array(
  'dsn'        => 'mysql:dbname=testdb;host=localhost',
  'username'   => 'foo',
  'password'   => 'bar',
  'encoding'   => 'utf8',
  'persistent' => true,
  'options'    => array('ATTR_AUTOCOMMIT' => false)
);

$p = new sfPropelDatabase($parametersTests);
$t->is($p->getConfiguration(), $configuration, 'initialize() - creates a valid propel configuration from parameters');
