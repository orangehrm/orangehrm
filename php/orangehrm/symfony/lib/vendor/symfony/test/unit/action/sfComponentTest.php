<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please component the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');

$t = new lime_test(8, new lime_output_color());

class myComponent extends sfComponent
{
  function execute($request) {}
}

$context = sfContext::getInstance(array(
  'routing' => 'sfNoRouting',
  'request' => 'sfWebRequest',
));

// ->initialize()
$t->diag('->initialize()');
$component = new myComponent($context, 'module', 'action');
$t->is($component->getContext(), $context, '->initialize() takes a sfContext object as its first argument');
$component->initialize($context, 'module', 'action');
$t->is($component->getContext(), $context, '->initialize() takes a sfContext object as its first argument');

// ->getContext()
$t->diag('->getContext()');
$component->initialize($context, 'module', 'action');
$t->is($component->getContext(), $context, '->getContext() returns the current context');

// ->getRequest()
$t->diag('->getRequest()');
$component->initialize($context, 'module', 'action');
$t->is($component->getRequest(), $context->getRequest(), '->getRequest() returns the current request');

// ->getResponse()
$t->diag('->getResponse()');
$component->initialize($context, 'module', 'action');
$t->is($component->getResponse(), $context->getResponse(), '->getResponse() returns the current response');

// __set()
$t->diag('__set()');
$component->foo = array();
$component->foo[] = 'bar';
$t->is($component->foo, array('bar'), '__set() populates component variables');

// new methods via sfEventDispatcher
require_once($_test_dir.'/unit/sfEventDispatcherTest.class.php');
$dispatcherTest = new sfEventDispatcherTest($t);
$dispatcherTest->launchTests($context->getEventDispatcher(), $component, 'component');
