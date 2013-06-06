<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../lib/vendor/lime/lime.php');
require_once(dirname(__FILE__).'/../../../lib/escaper/sfOutputEscaper.class.php');
require_once(dirname(__FILE__).'/../../../lib/escaper/sfOutputEscaperGetterDecorator.class.php');
require_once(dirname(__FILE__).'/../../../lib/escaper/sfOutputEscaperArrayDecorator.class.php');
require_once(dirname(__FILE__).'/../../../lib/escaper/sfOutputEscaperObjectDecorator.class.php');
require_once(dirname(__FILE__).'/../../../lib/escaper/sfOutputEscaperIteratorDecorator.class.php');

require_once(dirname(__FILE__).'/../../../lib/helper/EscapingHelper.php');
require_once(dirname(__FILE__).'/../../../lib/config/sfConfig.class.php');

class sfException extends Exception
{
}

sfConfig::set('sf_charset', 'UTF-8');

$t = new lime_test(8);

class OutputEscaperTest
{
  public function __toString()
  {
    return $this->getTitle();
  }

  public function getTitle()
  {
    return '<strong>escaped!</strong>';
  }

  public function getTitles()
  {
    return array(1, 2, '<strong>escaped!</strong>');
  }
}

$object = new OutputEscaperTest();
$escaped = sfOutputEscaper::escape('esc_entities', $object);

$t->is($escaped->getTitle(), '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');

$array = $escaped->getTitles();
$t->is($array[2], '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');

// __toString()
$t->diag('__toString()');

$t->is($escaped->__toString(), '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');

if (class_exists('SimpleXMLElement'))
{
  $element = new SimpleXMLElement('<foo>bar</foo>');
  $escaped = sfOutputEscaper::escape('esc_entities', $element);
  $t->is((string) $escaped, (string) $element, '->__toString() is compatible with SimpleXMLElement');
}
else
{
  $t->skip('->__toString() is compatible with SimpleXMLElement');
}

class Foo
{
}

class FooCountable implements Countable
{
  public function count()
  {
    return 2;
  }
}

// implements Countable
$t->diag('implements Countable');
$foo = sfOutputEscaper::escape('esc_entities', new Foo());
$fooc = sfOutputEscaper::escape('esc_entities', new FooCountable());
$t->is(count($foo), 1, '->count() returns 1 if the embedded object does not implement the Countable interface');
$t->is(count($fooc), 2, '->count() returns the count() for the embedded object');

// ->__isset()
$t->diag('->__isset()');

$raw = new stdClass();
$raw->foo = 'bar';
$esc = sfOutputEscaper::escape('esc_entities', $raw);
$t->ok(isset($esc->foo), '->__isset() asks the wrapped object whether a property is set');
unset($raw->foo);
$t->ok(!isset($esc->foo), '->__isset() asks the wrapped object whether a property is set');
