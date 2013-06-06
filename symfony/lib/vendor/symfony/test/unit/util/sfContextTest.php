<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(17);

class myContext extends sfContext
{
  public function initialize(sfApplicationConfiguration $configuration)
  {
  }
}

/*
// unit testing sfContext requires mock configuration / app
// this test requires the functional project configurations

class ProjectConfiguration extends sfProjectConfiguration
{
}

class frontendConfiguration extends sfApplicationConfiguration
{
}
*/

// use functional project configruration
require_once realpath(dirname(__FILE__).'/../../functional/fixtures/config/ProjectConfiguration.class.php');

$frontend_context = sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
$i18n_context = sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('i18n', 'test', true));
$cache_context = sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('cache', 'test', true));

// ::getInstance()
$t->diag('::getInstance()');
$t->isa_ok($frontend_context, 'sfContext', '::createInstance() takes an application configuration and returns application context instance');
$t->isa_ok(sfContext::getInstance('frontend'), 'sfContext', '::createInstance() creates application name context instance');

$context = sfContext::getInstance('frontend');
$context1 = sfContext::getInstance('i18n');
$context2 = sfContext::getInstance('cache');
$t->is(sfContext::getInstance('i18n'), $context1, '::getInstance() returns the named context if it already exists');

// ::switchTo();
$t->diag('::switchTo()');
sfContext::switchTo('i18n');
$t->is(sfContext::getInstance(), $context1, '::switchTo() changes the default context instance returned by ::getInstance()');
sfContext::switchTo('cache');
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

$context['foo'] = $frontend_context;
$t->diag('Array access for context objects');
$t->is(isset($context['foo']), true, '->offsetExists() returns true if context object exists');
$t->is(isset($context['foo2']), false, '->offsetExists() returns false if context object does not exist');
$t->isa_ok($context['foo'], 'sfContext', '->offsetGet() returns attribute by name');

$context['foo2'] = $i18n_context;
$t->isa_ok($context['foo2'], 'sfContext', '->offsetSet() sets object by name');

unset($context['foo2']);
$t->is(isset($context['foo2']), false, '->offsetUnset() unsets object by name');

$t->diag('->__call()');

$context->setFoo4($i18n_context);
$t->is($context->has('foo4'), true, '->__call() sets context objects by name using setName()');
$t->isa_ok($context->getFoo4(), 'sfContext', '->__call() returns context objects by name using getName()');

try
{
  $context->unknown();
  $t->fail('->__call() throws an sfException if factory / method does not exist');
}
catch (sfException $e)
{
  $t->pass('->__call() throws an sfException if factory / method does not exist');
}
