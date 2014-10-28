<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';

$t = new lime_test(3);

$table = Doctrine_Core::getTable('Setting');

// columns
$t->diag('columns');

$t->is_deeply($table->getColumnDefinition('name'), array(
  'type'    => 'string',
  'length'  => 255,
  'notnull' => true,
), 'the short "type" syntax is expanded');

$t->is_deeply($table->getColumnDefinition('weight'), array(
  'type'   => 'float',
  'length' => 4,
  'scale'  => 4,
), 'the short "type(length, scale)" syntax is expanded');

// actAs
$t->diag('actAs');

$options = $table->getTemplate('Timestampable')->getOptions();
$t->is($options['updated']['disabled'], true, 'the short "actAs" syntax is expanded');
