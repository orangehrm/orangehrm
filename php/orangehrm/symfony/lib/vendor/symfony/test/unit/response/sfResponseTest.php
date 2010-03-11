<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

class myResponse extends sfResponse
{
  function serialize() {}
  function unserialize($serialized) {}
}

class fakeResponse
{
}

$t = new lime_test(7, new lime_output_color());

$dispatcher = new sfEventDispatcher();

// ->initialize()
$t->diag('->initialize()');
$response = new myResponse($dispatcher, array('foo' => 'bar'));
$options = $response->getOptions();
$t->is($options['foo'], 'bar', '->initialize() takes an array of options as its second argument');

// ->getContent() ->setContent()
$t->diag('->getContent() ->setContent()');
$t->is($response->getContent(), null, '->getContent() returns the current response content which is null by default');
$response->setContent('test');
$t->is($response->getContent(), 'test', '->setContent() sets the response content');

// ->sendContent()
$t->diag('->sendContent()');
ob_start();
$response->sendContent();
$content = ob_get_clean();
$t->is($content, 'test', '->sendContent() output the current response content');

// ->serialize() ->unserialize()
$t->diag('->serialize() ->unserialize()');
$t->ok(new myResponse($dispatcher) instanceof Serializable, 'sfResponse implements the Serializable interface');

// new methods via sfEventDispatcher
require_once($_test_dir.'/unit/sfEventDispatcherTest.class.php');
$dispatcherTest = new sfEventDispatcherTest($t);
$dispatcherTest->launchTests($dispatcher, $response, 'response');
