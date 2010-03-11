<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(28, new lime_output_color());

// ->clear()
$t->diag('->clear()');
$ph = new sfParameterHolder();
$ph->clear();
$t->is($ph->getAll(), null, '->clear() clears all parameters');

$ph->set('foo', 'bar');
$ph->clear();
$t->is($ph->getAll(), null, '->clear() clears all parameters');

// ->get()
$t->diag('->get()');
$ph = new sfParameterHolder();
$ph->set('foo', 'bar');
$t->is($ph->get('foo'), 'bar', '->get() returns the parameter value for the given key');
$t->is($ph->get('bar'), null, '->get() returns null if the key does not exist');

// checks that get returns reference
$ref = 'foobar';
$ph->set('ref', $ref);
$ref2 = null;
$ref2 &= $ph->get('ref'); // obtain the very same reference and modify it
$ref2 &= 'barfoo';
$t->is($ref2 , $ref, '->get() returns a reference for the given key');

$ph = new sfParameterHolder();
$t->is('default_value', $ph->get('foo1', 'default_value'), '->get() takes the default value as its second argument');

// ->getNames()
$t->diag('->getNames()');
$ph = new sfParameterHolder();
$ph->set('foo', 'bar');
$ph->set('yourfoo', 'bar');

$t->is($ph->getNames(), array('foo', 'yourfoo'), '->getNames() returns all key names');

// ->getAll()
$t->diag('->getAll()');
$parameters = array('foo' => 'bar', 'myfoo' => 'bar');
$ph = new sfParameterHolder();
$ph->add($parameters);
$t->is($ph->getAll(), $parameters, '->getAll() returns all parameters');

// ->has()
$t->diag('->has()');
$ph = new sfParameterHolder();
$ph->set('foo', 'bar');
$t->is($ph->has('foo'), true, '->has() returns true if the key exists');
$t->is($ph->has('bar'), false, '->has() returns false if the key does not exist');
$ph->set('bar', null);
$t->is($ph->has('bar'), true, '->has() returns true if the key exist, even if the value is null');

// ->remove()
$t->diag('->remove()');
$ph = new sfParameterHolder();
$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar');

$ph->remove('foo');
$t->is($ph->has('foo'), false, '->remove() removes the key from parameters');

$ph->remove('myfoo');
$t->is($ph->has('myfoo'), false, '->remove() removes the key from parameters');

$t->is($ph->remove('nonexistant', 'foobar'), 'foobar', '->remove() takes a default value as its second argument');

$t->is($ph->getAll(), null, '->remove() removes the key from parameters');

// ->set()
$t->diag('->set()');
$foo = 'bar';

$ph = new sfParameterHolder();
$ph->set('foo', $foo);
$t->is($ph->get('foo'), $foo, '->set() sets the value for a key');

$foo = 'foo';
$t->is($ph->get('foo'), 'bar', '->set() sets the value for a key, not a reference');

// ->setByRef()
$t->diag('->setByRef()');
$foo = 'bar';

$ph = new sfParameterHolder();
$ph->setByRef('foo', $foo);
$t->is($ph->get('foo'), $foo, '->setByRef() sets the value for a key');

$foo = 'foo';
$t->is($ph->get('foo'), $foo, '->setByRef() sets the value for a key as a reference');

// ->add()
$t->diag('->add()');
$foo = 'bar';
$parameters = array('foo' => $foo, 'bar' => 'bar');
$myparameters = array('myfoo' => 'bar', 'mybar' => 'bar');

$ph = new sfParameterHolder();
$ph->add($parameters);

$t->is($ph->getAll(), $parameters, '->add() adds an array of parameters');

$foo = 'mybar';
$t->is($ph->getAll(), $parameters, '->add() adds an array of parameters, not a reference');

// ->addByRef()
$t->diag('->addByRef()');
$foo = 'bar';
$parameters = array('foo' => &$foo, 'bar' => 'bar');
$myparameters = array('myfoo' => 'bar', 'mybar' => 'bar');

$ph = new sfParameterHolder();
$ph->addByRef($parameters);

$t->is($parameters, $ph->getAll(), '->add() adds an array of parameters');

$foo = 'mybar';
$t->is($parameters, $ph->getAll(), '->add() adds a reference of an array of parameters');

// ->serialize() ->unserialize()
$t->diag('->serialize() ->unserialize()');
$t->ok($ph == unserialize(serialize($ph)), 'sfParameterHolder implements the Serializable interface');

// Array path as a key
$t->diag('Array path as a key');
$ph = new sfParameterHolder();
$ph->add(array('foo' => array('bar' => 'foo')));
$t->is($ph->has('foo[bar]'), true, '->has() can takes a multi-array key');
$t->is($ph->get('foo[bar]'), 'foo', '->has() can takes a multi-array key');
$t->is($ph->remove('foo[bar]'), 'foo', '->remove() can takes a multi-array key');
$t->is($ph->getAll(), array('foo' => array()), '->remove() can takes a multi-array key');
