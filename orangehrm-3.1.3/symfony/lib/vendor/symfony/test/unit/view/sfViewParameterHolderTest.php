<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');

$t = new lime_test(21);

define('ESC_SPECIALCHARS', 'esc_specialchars');
function esc_specialchars($value)
{
  return "-ESCAPED-$value-ESCAPED-";
}

define('ESC_RAW', 'esc_raw');
function esc_raw($value)
{
  return $value;
}

class myRequest
{
  public function getParameterHolder()
  {
    return new sfParameterHolder();
  }
}

$context = sfContext::getInstance();
$dispatcher = $context->dispatcher;

// ->initialize()
$t->diag('->initialize()');
$p = new sfViewParameterHolder($dispatcher);
$t->is($p->getAll(), array(), '->initialize() initializes the parameters as an empty array');

$p->initialize($dispatcher, array('foo' => 'bar'));
$t->is($p->get('foo'), 'bar', '->initialize() takes an array of default parameters as its second argument');

$p->initialize($dispatcher, array(), array('escaping_strategy' => 'on', 'escaping_method' => 'ESC_RAW'));
$t->is($p->getEscaping(), 'on', '->initialize() takes an array of options as its third argument');
$t->is($p->getEscapingMethod(), ESC_RAW, '->initialize() takes an array of options as its third argument');

// ->isEscaped()
$t->diag('->isEscaped()');
$p->setEscaping('on');
$t->is($p->isEscaped(), true, '->isEscaped() returns true if data will be escaped');
$p->setEscaping('off');
$t->is($p->isEscaped(), false, '->isEscaped() returns false if data won\'t be escaped');

// ->getEscaping() ->setEscaping()
$t->diag('->getEscaping() ->setEscaping()');
$p->initialize($dispatcher);
$p->setEscaping('on');
$t->is($p->getEscaping(), 'on', '->setEscaping() changes the escaping strategy');

// ->getEscapingMethod() ->setEscapingMethod()
$t->diag('->getEscapingMethod() ->setEscapingMethod()');
$p->setEscapingMethod('ESC_RAW');
$t->is($p->getEscapingMethod(), ESC_RAW, '->setEscapingMethod() changes the escaping method');

$p->setEscapingMethod('');
$t->is($p->getEscapingMethod(), '', '->getEscapingMethod() returns an empty value if the method is empty');

try
{
  $p->setEscapingMethod('nonexistant');
  $p->getEscapingMethod();
  $t->fail('->getEscapingMethod() throws an InvalidArgumentException if the escaping method does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->getEscapingMethod() throws an InvalidArgumentException if the escaping method does not exist');
}

// ->toArray()
$t->diag('->toArray()');
$p->initialize($dispatcher, array('foo' => 'bar'));
$a = $p->toArray();
$t->is($a['foo'], 'bar', '->toArray() returns an array representation of the parameter holder');

// escaping strategies
$p = new sfViewParameterHolder(new sfEventDispatcher(), array('foo' => 'bar'));

try
{
  $p->setEscaping('null');
  $p->toArray();
  $t->fail('->toArray() throws an InvalidArgumentException if the escaping strategy does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->toArray() throws an InvalidArgumentException if the escaping strategy does not exist');
}

$t->diag('Escaping strategy to on');
$p->setEscaping('on');
$values = $p->toArray();
$t->is(count($values), 2, '->toArray() knows about the "on" strategy');
$t->is(count($values['sf_data']), 1, '->toArray() knows about the "on" strategy');
$t->is($values['foo'], '-ESCAPED-bar-ESCAPED-', '->toArray() knows about the "on" strategy');
$t->is($values['sf_data']['foo'], '-ESCAPED-bar-ESCAPED-', '->toArray() knows about the "on" strategy');

$t->diag('Escaping strategy to off');
$p->setEscaping('off');
$values = $p->toArray();
$t->is(count($values), 2, '->toArray() knows about the "off" strategy');
$t->is(count($values['sf_data']), 1, '->toArray() knows about the "on" strategy');
$t->is($values['foo'], 'bar', '->toArray() knows about the "off" strategy');
$t->is($values['sf_data']['foo'], 'bar', '->toArray() knows about the "off" strategy');

// ->serialize() / ->unserialize()
$t->diag('->serialize() / ->unserialize()');
$p->initialize($dispatcher, array('foo' => 'bar'));
$unserialized = unserialize(serialize($p));
$t->is($p->toArray(), $unserialized->toArray(), 'sfViewParameterHolder implements the Serializable interface');
