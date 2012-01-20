<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

class myRequest extends sfRequest
{
  public function getEventDispatcher()
  {
    return $this->dispatcher;
  }
}

class fakeRequest
{
}

$t = new lime_test(36);

$dispatcher = new sfEventDispatcher();

// ->initialize()
$t->diag('->initialize()');
$request = new myRequest($dispatcher);
$t->is($dispatcher, $request->getEventDispatcher(), '->initialize() takes a sfEventDispatcher object as its first argument');
$request->initialize($dispatcher, array('foo' => 'bar'));
$t->is($request->getParameter('foo'), 'bar', '->initialize() takes an array of parameters as its second argument');

$options = $request->getOptions();
$t->is($options['logging'], false, '->getOptions() returns options for request instance');

// ->getMethod() ->setMethod()
$t->diag('->getMethod() ->setMethod()');
$request->setMethod(sfRequest::GET);
$t->is($request->getMethod(), sfRequest::GET, '->getMethod() returns the current request method');

try
{
  $request->setMethod('foo');
  $t->fail('->setMethod() throws a sfException if the method is not valid');
}
catch (sfException $e)
{
  $t->pass('->setMethod() throws a sfException if the method is not valid');
}

// ->extractParameters()
$t->diag('->extractParameters()');
$request->initialize($dispatcher, array('foo' => 'foo', 'bar' => 'bar'));
$t->is($request->extractParameters(array()), array(), '->extractParameters() returns parameters');
$t->is($request->extractParameters(array('foo')), array('foo' => 'foo'), '->extractParameters() returns parameters for keys in its first parameter');
$t->is($request->extractParameters(array('bar')), array('bar' => 'bar'), '->extractParameters() returns parameters for keys in its first parameter');

// array access for request parameters
$t->diag('Array access for request parameters');
$t->is(isset($request['foo']), true, '->offsetExists() returns true if request parameter exists');
$t->is(isset($request['foo2']), false, '->offsetExists() returns false if request parameter does not exist');
$t->is($request['foo3'], false, '->offsetGet() returns false if parameter does not exist');
$t->is($request['foo'], 'foo', '->offsetGet() returns parameter by name');

$request['foo2'] = 'foo2';
$t->is($request['foo2'], 'foo2', '->offsetSet() sets parameter by name');

unset($request['foo2']);
$t->is(isset($request['foo2']), false, '->offsetUnset() unsets parameter by name');


$request = new myRequest($dispatcher);

// parameter holder proxy
require_once($_test_dir.'/unit/sfParameterHolderTest.class.php');
$pht = new sfParameterHolderProxyTest($t);
$pht->launchTests($request, 'parameter');

// attribute holder proxy
$pht = new sfParameterHolderProxyTest($t);
$pht->launchTests($request, 'attribute');

// new methods via sfEventDispatcher
require_once($_test_dir.'/unit/sfEventDispatcherTest.class.php');
$dispatcherTest = new sfEventDispatcherTest($t);
$dispatcherTest->launchTests($dispatcher, $request, 'request');
