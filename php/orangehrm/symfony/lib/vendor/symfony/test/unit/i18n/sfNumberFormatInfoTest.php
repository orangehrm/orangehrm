<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(75);

// __construct()
$t->diag('__construct()');
try
{
  $c = new sfNumberFormatInfo();
  $t->fail('__construct() takes a mandatory ICU array as its first argument');
}
catch (sfException $e)
{
  $t->pass('__construct() takes a mandatory ICU array as its first argument');
}

// ::getInstance()
$t->diag('::getInstance()');
$t->isa_ok(sfNumberFormatInfo::getInstance(), 'sfNumberFormatInfo', '::getInstance() returns an sfNumberFormatInfo instance');
$c = sfCultureInfo::getInstance();
$t->is(sfNumberFormatInfo::getInstance($c), $c->getNumberFormat(), '::getInstance() can take a sfCultureInfo instance as its first argument');
$t->isa_ok(sfNumberFormatInfo::getInstance('fr'), 'sfNumberFormatInfo', '::getInstance() can take a culture as its first argument');
$n = sfNumberFormatInfo::getInstance();
$n->setPattern(sfNumberFormatInfo::PERCENTAGE);
$t->is(sfNumberFormatInfo::getInstance(null, sfNumberFormatInfo::PERCENTAGE)->getPattern(), $n->getPattern(), '::getInstance() can take a formatting type as its second argument');

// ->getPattern() ->setPattern()
$t->diag('->getPattern() ->setPattern()');
$n = sfNumberFormatInfo::getInstance();
$n1 = sfNumberFormatInfo::getInstance();
$n->setPattern(sfNumberFormatInfo::CURRENCY);
$pattern = $n->getPattern();
$n1->setPattern(sfNumberFormatInfo::PERCENTAGE);
$pattern1 = $n1->getPattern();
$t->isnt($pattern, $pattern1, '->getPattern() ->setPattern() changes the current pattern');

$n = sfNumberFormatInfo::getInstance();
$n1 = sfNumberFormatInfo::getInstance();
$n->Pattern = sfNumberFormatInfo::CURRENCY;
$n1->setPattern(sfNumberFormatInfo::CURRENCY);
$t->is($n->getPattern(), $n1->getPattern(), '->setPattern() is equivalent to ->Pattern = ');
$t->is($n->getPattern(), $n->Pattern, '->getPattern() is equivalent to ->Pattern');

// ::getCurrencyInstance()
$t->diag('::getCurrencyInstance()');
$t->is(sfNumberFormatInfo::getCurrencyInstance()->getPattern(), sfNumberFormatInfo::getInstance(null, sfNumberFormatInfo::CURRENCY)->getPattern(), '::getCurrencyInstance() is a shortcut for ::getInstance() and type sfNumberFormatInfo::CURRENCY');

// ::getPercentageInstance()
$t->diag('::getPercentageInstance()');
$t->is(sfNumberFormatInfo::getPercentageInstance()->getPattern(), sfNumberFormatInfo::getInstance(null, sfNumberFormatInfo::PERCENTAGE)->getPattern(), '::getPercentageInstance() is a shortcut for ::getInstance() and type sfNumberFormatInfo::PERCENTAGE');

// ::getScientificInstance()
$t->diag('::getScientificInstance()');
$t->is(sfNumberFormatInfo::getScientificInstance()->getPattern(), sfNumberFormatInfo::getInstance(null, sfNumberFormatInfo::SCIENTIFIC)->getPattern(), '::getScientificInstance() is a shortcut for ::getInstance() and type sfNumberFormatInfo::SCIENTIFIC');

$tests = array(
  'fr' => array(
    'DecimalDigits'          => -1,
    'DecimalSeparator'       => ',',
    'GroupSeparator'         => ' ',
    'CurrencySymbol'         => '$US',
    'NegativeInfinitySymbol' => '-∞',
    'PositiveInfinitySymbol' => '+∞',
    'NegativeSign'           => '-',
    'PositiveSign'           => '+',
    'NaNSymbol'              => 'NaN',
    'PercentSymbol'          => '%',
    'PerMilleSymbol'         => '‰',
  ),
  'en' => array(
    'DecimalDigits'          => -1,
    'DecimalSeparator'       => '.',
    'GroupSeparator'         => ',',
    'CurrencySymbol'         => '$',
    'NegativeInfinitySymbol' => '-∞',
    'PositiveInfinitySymbol' => '+∞',
    'NegativeSign'           => '-',
    'PositiveSign'           => '+',
    'NaNSymbol'              => 'NaN',
    'PercentSymbol'          => '%',
    'PerMilleSymbol'         => '‰',
  ),
);

foreach ($tests as $culture => $fixtures)
{
  $n = sfNumberFormatInfo::getInstance($culture);

  foreach ($fixtures as $method => $result)
  {
    $getter = 'get'.$method;
    $t->is($n->$getter(), $result, sprintf('->%s() returns "%s" for culture "%s"', $getter, $result, $culture));
  }
}

// setters/getters
foreach (array(
  'DecimalDigits', 'DecimalSeparator', 'GroupSeparator', 
  'CurrencySymbol', 'NegativeInfinitySymbol', 'PositiveInfinitySymbol',
  'NegativeSign', 'PositiveSign', 'NaNSymbol', 'PercentSymbol', 'PerMilleSymbol',
) as $method)
{
  $t->diag(sprintf('->get%s() ->set%s()', $method, $method));
  $n = sfNumberFormatInfo::getInstance();
  $setter = 'set'.$method;
  $getter = 'get'.$method;
  $n->$setter('foo');
  $t->is($n->$getter(), 'foo', sprintf('->%s() sets the current decimal digits', $setter));
  $t->is($n->$method, $n->$getter(), sprintf('->%s() is equivalent to ->%s', $getter, $method));
  $n->$method = 'bar';
  $t->is($n->$getter(), 'bar', sprintf('->%s() is equivalent to ->%s = ', $setter, $method));
}

foreach (array('GroupSizes', 'NegativePattern', 'PositivePattern') as $method)
{
  $t->diag(sprintf('->get%s() ->set%s()', $method, $method));
  $n = sfNumberFormatInfo::getInstance();
  $setter = 'set'.$method;
  $getter = 'get'.$method;
  $n->$setter(array('foo', 'foo'));
  $t->is($n->$getter(), array('foo', 'foo'), sprintf('->%s() sets the current decimal digits', $setter));
  $t->is($n->$method, $n->$getter(), sprintf('->%s() is equivalent to ->%s', $getter, $method));
  $n->$method = array('bar', 'bar');
  $t->is($n->$getter(), array('bar', 'bar'), sprintf('->%s() is equivalent to ->%s = ', $setter, $method));
}
