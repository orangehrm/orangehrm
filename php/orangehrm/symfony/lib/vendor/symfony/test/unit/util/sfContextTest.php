<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

// FIXME
$t = new lime_test(0, new lime_output_color());

class myContext extends sfContext
{
  public function initialize(sfApplicationConfiguration $configuration)
  {
  }
}

class ProjectConfiguration extends sfProjectConfiguration
{
}

class frontendConfiguration extends sfApplicationConfiguration
{
}
/*
// ::getInstance()
$t->diag('::getInstance()');
$t->isa_ok(sfContext::getInstance('default', 'myContext'), 'myContext', '::getInstance() takes a sfContext class name as its second argument');

$context1 = sfContext::getInstance('context1', 'myContext');
$context2 = sfContext::getInstance('context2', 'myContext');

$t->is(sfContext::getInstance('context1'), $context1, '::getInstance() returns the named context if it already exists');

// ::switchTo();
$t->diag('::switchTo()');
sfContext::switchTo('context1');
$t->is(sfContext::getInstance(), $context1, '::switchTo() changes the default context instance returned by ::getInstance()');
sfContext::switchTo('context2');
$t->is(sfContext::getInstance(), $context2, '::switchTo() changes the default context instance returned by ::getInstance()');

// ->get() ->set() ->has()
$t->diag('->get() ->set() ->has()');
$t->is($context1->has('object'), false, '->has() returns false if no object of the given name exist');
$object = new stdClass();
$context1->set('object', $object, '->set() stores an object in the current context instance');
$t->is($context1->has('object'), true, '->has() returns true if an object is stored for the given name');
$t->is($context1->get('object'), $object, '->get() returns the object associated with the given name');
try
{
  $context1->get('object1');
  $t->fail('->get() throws an sfException if no object is stored for the given name');
}
catch (sfException $e)
{
  $t->pass('->get() throws an sfException if no object is stored for the given name');
}
*/