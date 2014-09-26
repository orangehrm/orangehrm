<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// helper class to test parameter holder proxies
class sfParameterHolderProxyTest
{
  protected $t = null;

  public function __construct($testObject)
  {
    $this->t = $testObject;
  }

  public function launchTests($object, $methodName = 'parameter')
  {
    $t = $this->t;

    $hasMethod = 'has'.ucfirst($methodName);
    $getMethod = 'get'.ucfirst($methodName);
    $setMethod = 'set'.ucfirst($methodName);
    $holderMethod = 'get'.ucfirst($methodName).'Holder';

    $t->diag(ucfirst($methodName).' holder proxy');

    $namespaced = $object->$holderMethod() instanceof sfNamespacedParameterHolder ? true : false;

    $t->isa_ok($object->$holderMethod(), $namespaced ? 'sfNamespacedParameterHolder' : 'sfParameterHolder', "->$holderMethod() returns a parameter holder instance");
    $t->is($object->$hasMethod('foo'), false, "->$hasMethod() returns false if the $methodName does not exist");
    $t->is($object->$getMethod('foo', 'default'), 'default', "->$getMethod() returns the default value if $methodName does not exist");
    $object->$setMethod('foo', 'bar');
    $t->is($object->$hasMethod('foo'), true, "->$hasMethod() returns true if the $methodName exists");
    $t->is($object->$hasMethod('foo'), $object->$holderMethod()->has('foo'), "->$hasMethod() is a proxy method");
    $t->is($object->$getMethod('foo'), 'bar', "->$getMethod() returns the value of the $methodName");
    $t->is($object->$getMethod('foo'), $object->$holderMethod()->get('foo'), "->$getMethod() is a proxy method");
    $t->is($object->$getMethod('foo', 'default'), 'bar', "->$getMethod() does not return the default value if the $methodName exists");

    if ($namespaced)
    {
      $object->$setMethod('foo1', 'bar1', 'mynamespace');
      $t->is($object->$hasMethod('foo1'), false, "->$hasMethod() takes a namespace as its second parameter");
      $t->is($object->$hasMethod('foo1', 'mynamespace'), true, "->$hasMethod() takes a namespace as its second parameter");
      $t->is($object->$getMethod('foo1', 'default', 'mynamespace'), 'bar1', "->$getMethod() takes a namespace as its third parameter");
    }

    $object->$setMethod('foo2', 'bar2');
    $object->$holderMethod()->set('foo3', 'bar3');
    $t->is($object->$getMethod('foo2'), $object->$holderMethod()->get('foo2'), "->$setMethod() is a proxy method");
    $t->is($object->$getMethod('foo3'), $object->$holderMethod()->get('foo3'), "->$setMethod() is a proxy method");
  }
}
