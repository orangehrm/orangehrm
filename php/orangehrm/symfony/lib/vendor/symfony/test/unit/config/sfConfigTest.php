<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(9, new lime_output_color());

// ::get() ::set()
$t->diag('::get() ::set()');
sfConfig::clear();

sfConfig::set('foo', 'bar');
$t->is(sfConfig::get('foo'), 'bar', '::get() returns the value of key config');
$t->is(sfConfig::get('foo1', 'default_value'), 'default_value', '::get() takes a default value as its second argument');

// ::has()
$t->diag('::has()');
sfConfig::clear();
$t->is(sfConfig::has('foo'), false, '::has() returns false if the key config does not exist');
sfConfig::set('foo', 'bar');
$t->is(sfConfig::has('foo'), true, '::has() returns true if the key config exists');

// ::add()
$t->diag('::add()');
sfConfig::clear();

sfConfig::set('foo', 'bar');
sfConfig::set('foo1', 'foo1');
sfConfig::add(array('foo' => 'foo', 'bar' => 'bar'));

$t->is(sfConfig::get('foo'), 'foo', '::add() adds an array of config parameters');
$t->is(sfConfig::get('bar'), 'bar', '::add() adds an array of config parameters');
$t->is(sfConfig::get('foo1'), 'foo1', '::add() adds an array of config parameters');

// ::getAll()
$t->diag('::getAll()');
sfConfig::clear();
sfConfig::set('foo', 'bar');
sfConfig::set('foo1', 'foo1');

$t->is(sfConfig::getAll(), array('foo' => 'bar', 'foo1' => 'foo1'), '::getAll() returns all config parameters');

// ::clear()
$t->diag('::clear()');
sfConfig::clear();
$t->is(sfConfig::get('foo1'), null, '::clear() removes all config parameters');
