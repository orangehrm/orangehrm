<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(50);

// ->clear()
$t->diag('->clear()');
$ph = new sfNamespacedParameterHolder();
$ph->clear();
$t->is($ph->getAll(), null, '->clear() clears all parameters');

$ph->set('foo', 'bar');
$ph->clear();
$t->is($ph->getAll(), null, '->clear() clears all parameters');

// ->get()
$t->diag('->get()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$t->is($ph->get('foo'), 'bar', '->get() returns the parameter value for the given key');
$t->is($ph->get('bar'), null, '->get() returns null if the key does not exist');

$ph = new sfNamespacedParameterHolder();
$t->is('default_value', $ph->get('foo1', 'default_value'), '->get() takes the default value as its second argument');

$ph = new sfNamespacedParameterHolder();
$ph->set('myfoo', 'bar', 'symfony/mynamespace');
$t->is('bar', $ph->get('myfoo', null, 'symfony/mynamespace'), '->get() takes an optional namespace as its third argument');
$t->is(null, $ph->get('myfoo'), '->get() can have the same key for several namespaces');

// ->getNames()
$t->diag('->getNames()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('yourfoo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');

$t->is($ph->getNames(), array('foo', 'yourfoo'), '->getNames() returns all key names for the default namespace');
$t->is($ph->getNames('symfony/mynamespace'), array('myfoo'), '->getNames() takes a namepace as its first argument');

// ->getNamespaces()
$t->diag('->getNamespaces()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('yourfoo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');

$t->is($ph->getNamespaces(), array($ph->getDefaultNamespace(), 'symfony/mynamespace'), '->getNamespaces() returns all non empty namespaces');

// ->setDefaultNamespace()
$t->diag('->setDefaultNamespace()');
$ph = new sfNamespacedParameterHolder('symfony/mynamespace');
$ph->setDefaultNamespace('othernamespace');

$t->is($ph->getDefaultNamespace(), 'othernamespace', '->setDefaultNamespace() sets the default namespace');

$ph->set('foo', 'bar');
$ph->setDefaultNamespace('foonamespace');

$t->is($ph->get('foo'), 'bar', '->setDefaultNamespace() moves values from the old namespace to the new');
$t->is($ph->get('foo', null, 'othernamespace'), null, '->setDefaultNamespace() moves values from the old namespace to the new');

$ph->set('foo', 'bar');
$ph->setDefaultNamespace('barnamespace', false);

$t->is($ph->get('foo'), null, '->setDefaultNamespace() does not move old values to the new namespace if the second argument is false');
$t->is($ph->get('foo', null, 'foonamespace'), 'bar', '->setDefaultNamespace() does not move old values to the new namespace if the second argument is false');

// ->getAll()
$t->diag('->getAll()');
$parameters = array('foo' => 'bar', 'myfoo' => 'bar');
$ph = new sfNamespacedParameterHolder();
$ph->add($parameters);
$ph->set('myfoo', 'bar', 'symfony/mynamespace');
$t->is($ph->getAll(), $parameters, '->getAll() returns all parameters from the default namespace');

// ->has()
$t->diag('->has()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');
$t->is($ph->has('foo'), true, '->has() returns true if the key exists');
$t->is($ph->has('bar'), false, '->has() returns false if the key does not exist');
$t->is($ph->has('myfoo'), false, '->has() returns false if the key exists but in another namespace');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), true, '->has() returns true if the key exists in the namespace given as its second argument');

// ->hasNamespace()
$t->diag('->hasNamespace()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');
$t->is($ph->hasNamespace($ph->getDefaultNamespace()), true, '->hasNamespace() returns true for the default namespace');
$t->is($ph->hasNamespace('symfony/mynamespace'), true, '->hasNamespace() returns true if the namespace exists');
$t->is($ph->hasNamespace('symfony/nonexistant'), false, '->hasNamespace() returns false if the namespace does not exist');

// ->remove()
$t->diag('->remove()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');

$ph->remove('foo');
$t->is($ph->has('foo'), false, '->remove() removes the key from parameters');

$ph->remove('myfoo');
$t->is($ph->has('myfoo'), false, '->remove() removes the key from parameters');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), true, '->remove() removes the key from parameters for a given namespace');

$ph->remove('myfoo', null, 'symfony/mynamespace');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), false, '->remove() takes a namespace as its third argument');

$t->is($ph->remove('nonexistant', 'foobar', 'symfony/mynamespace'), 'foobar', '->remove() takes a default value as its second argument');

$t->is($ph->getAll(), null, '->remove() removes the key from parameters');

// ->removeNamespace()
$t->diag('->removeNamespace()');
$ph = new sfNamespacedParameterHolder();
$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');

$ph->removeNamespace($ph->getDefaultNamespace());
$t->is($ph->has('foo'), false, '->removeNamespace() removes all keys and values from a namespace');
$t->is($ph->has('myfoo'), false, '->removeNamespace() removes all keys and values from a namespace');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), true, '->removeNamespace() does not remove keys in other namepaces');

$ph->set('foo', 'bar');
$ph->set('myfoo', 'bar');
$ph->set('myfoo', 'bar', 'symfony/mynamespace');

$ph->removeNamespace();
$t->is($ph->has('foo'), false, '->removeNamespace() removes all keys and values from the default namespace by default');
$t->is($ph->has('myfoo'), false, '->removeNamespace() removes all keys and values from the default namespace by default');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), true, '->removeNamespace() does not remove keys in other namepaces');

$ph->removeNamespace('symfony/mynamespace');
$t->is($ph->has('myfoo', 'symfony/mynamespace'), false, '->removeNamespace() takes a namespace as its first parameter');

$t->is(null, $ph->getAll(), '->removeNamespace() removes all the keys from parameters');

// ->set()
$t->diag('->set()');
$foo = 'bar';

$ph = new sfNamespacedParameterHolder();
$ph->set('foo', $foo);
$t->is($ph->get('foo'), $foo, '->set() sets the value for a key');

$foo = 'foo';
$t->is($ph->get('foo'), 'bar', '->set() sets the value for a key, not a reference');

$ph->set('myfoo', 'bar', 'symfony/mynamespace');
$t->is($ph->get('myfoo', null, 'symfony/mynamespace'), 'bar', '->set() takes a namespace as its third parameter');

// ->setByRef()
$t->diag('->setByRef()');
$foo = 'bar';

$ph = new sfNamespacedParameterHolder();
$ph->setByRef('foo', $foo);
$t->is($ph->get('foo'), $foo, '->setByRef() sets the value for a key');

$foo = 'foo';
$t->is($ph->get('foo'), $foo, '->setByRef() sets the value for a key as a reference');

$myfoo = 'bar';
$ph->setByRef('myfoo', $myfoo, 'symfony/mynamespace');
$t->is($ph->get('myfoo', null, 'symfony/mynamespace'), $myfoo, '->setByRef() takes a namespace as its third parameter');

// ->add()
$t->diag('->add()');
$foo = 'bar';
$parameters = array('foo' => $foo, 'bar' => 'bar');
$myparameters = array('myfoo' => 'bar', 'mybar' => 'bar');

$ph = new sfNamespacedParameterHolder();
$ph->add($parameters);
$ph->add($myparameters, 'symfony/mynamespace');

$t->is($ph->getAll(), $parameters, '->add() adds an array of parameters');
$t->is($ph->getAll('symfony/mynamespace'), $myparameters, '->add() takes a namespace as its second argument');

$foo = 'mybar';
$t->is($ph->getAll(), $parameters, '->add() adds an array of parameters, not a reference');

// ->addByRef()
$t->diag('->addByRef()');
$foo = 'bar';
$parameters = array('foo' => &$foo, 'bar' => 'bar');
$myparameters = array('myfoo' => 'bar', 'mybar' => 'bar');

$ph = new sfNamespacedParameterHolder();
$ph->addByRef($parameters);
$ph->addByRef($myparameters, 'symfony/mynamespace');

$t->is($parameters, $ph->getAll(), '->add() adds an array of parameters');
$t->is($myparameters, $ph->getAll('symfony/mynamespace'), '->add() takes a namespace as its second argument');

$foo = 'mybar';
$t->is($parameters, $ph->getAll(), '->add() adds a reference of an array of parameters');

// ->serialize() ->unserialize()
$t->diag('->serialize() ->unserialize()');
$t->ok($ph == unserialize(serialize($ph)), 'sfNamespacedParameterHolder implements the Serializable interface');
