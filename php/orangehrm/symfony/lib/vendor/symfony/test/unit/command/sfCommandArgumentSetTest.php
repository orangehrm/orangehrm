<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(21, new lime_output_color());

$foo = new sfCommandArgument('foo');
$bar = new sfCommandArgument('bar');
$foo1 = new sfCommandArgument('foo');
$foo2 = new sfCommandArgument('foo2', sfCommandArgument::REQUIRED);

// __construct()
$t->diag('__construct()');
$argumentSet = new sfCommandArgumentSet();
$t->is($argumentSet->getArguments(), array(), '__construct() creates a new sfCommandArgumentSet object');

$argumentSet = new sfCommandArgumentSet(array($foo, $bar));
$t->is($argumentSet->getArguments(), array('foo' => $foo, 'bar' => $bar), '__construct() takes an array of sfCommandArgument objects as its first argument');

// ->setArguments()
$t->diag('->setArguments()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->setArguments(array($foo));
$t->is($argumentSet->getArguments(), array('foo' => $foo), '->setArguments() sets the array of sfCommandArgument objects');
$argumentSet->setArguments(array($bar));

$t->is($argumentSet->getArguments(), array('bar' => $bar), '->setArguments() clears all sfCommandArgument objects');

// ->addArguments()
$t->diag('->addArguments()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArguments(array($foo));
$t->is($argumentSet->getArguments(), array('foo' => $foo), '->addArguments() adds an array of sfCommandArgument objects');
$argumentSet->addArguments(array($bar));
$t->is($argumentSet->getArguments(), array('foo' => $foo, 'bar' => $bar), '->addArguments() does not clear existing sfCommandArgument objects');

// ->addArgument()
$t->diag('->addArgument()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArgument($foo);
$t->is($argumentSet->getArguments(), array('foo' => $foo), '->addArgument() adds a sfCommandArgument object');
$argumentSet->addArgument($bar);
$t->is($argumentSet->getArguments(), array('foo' => $foo, 'bar' => $bar), '->addArgument() adds a sfCommandArgument object');

// arguments must have different names
try
{
  $argumentSet->addArgument($foo1);
  $t->fail('->addArgument() throws a sfCommandException if another argument is already registered with the same name');
}
catch (sfCommandException $e)
{
  $t->pass('->addArgument() throws a sfCommandException if another argument is already registered with the same name');
}

// cannot add a parameter after an array parameter
$argumentSet->addArgument(new sfCommandArgument('fooarray', sfCommandArgument::IS_ARRAY));
try
{
  $argumentSet->addArgument(new sfCommandArgument('anotherbar'));
  $t->fail('->addArgument() throws a sfCommandException if there is an array parameter already registered');
}
catch (sfCommandException $e)
{
  $t->pass('->addArgument() throws a sfCommandException if there is an array parameter already registered');
}

// cannot add a required argument after an optional one
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArgument($foo);
try
{
  $argumentSet->addArgument($foo2);
  $t->fail('->addArgument() throws an exception if you try to add a required argument after an optional one');
}
catch (sfCommandException $e)
{
  $t->pass('->addArgument() throws an exception if you try to add a required argument after an optional one');
}

// ->getArgument()
$t->diag('->getArgument()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArguments(array($foo));
$t->is($argumentSet->getArgument('foo'), $foo, '->getArgument() returns a sfCommandArgument by its name');
try
{
  $argumentSet->getArgument('bar');
  $t->fail('->getArgument() throws an exception if the Argument name does not exist');
}
catch (sfCommandException $e)
{
  $t->pass('->getArgument() throws an exception if the Argument name does not exist');
}

// ->hasArgument()
$t->diag('->hasArgument()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArguments(array($foo));
$t->is($argumentSet->hasArgument('foo'), true, '->hasArgument() returns true if a sfCommandArgument exists for the given name');
$t->is($argumentSet->hasArgument('bar'), false, '->hasArgument() returns false if a sfCommandArgument exists for the given name');

// ->getArgumentRequiredCount()
$t->diag('->getArgumentRequiredCount()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArgument($foo2);
$t->is($argumentSet->getArgumentRequiredCount(), 1, '->getArgumentRequiredCount() returns the number of required arguments');
$argumentSet->addArgument($foo);
$t->is($argumentSet->getArgumentRequiredCount(), 1, '->getArgumentRequiredCount() returns the number of required arguments');

// ->getArgumentCount()
$t->diag('->getArgumentCount()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArgument($foo2);
$t->is($argumentSet->getArgumentCount(), 1, '->getArgumentCount() returns the number of arguments');
$argumentSet->addArgument($foo);
$t->is($argumentSet->getArgumentCount(), 2, '->getArgumentCount() returns the number of arguments');

// ->getDefaults()
$t->diag('->getDefaults()');
$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArguments(array(
  new sfCommandArgument('foo1', sfCommandArgument::OPTIONAL),
  new sfCommandArgument('foo2', sfCommandArgument::OPTIONAL, '', 'default'),
  new sfCommandArgument('foo3', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY),
//  new sfCommandArgument('foo4', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, '', array(1, 2)),
));
$t->is($argumentSet->getDefaults(), array('foo1' => null, 'foo2' => 'default', 'foo3' => array()), '->getDefaults() return the default values for each argument');

$argumentSet = new sfCommandArgumentSet();
$argumentSet->addArguments(array(
  new sfCommandArgument('foo4', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, '', array(1, 2)),
));
$t->is($argumentSet->getDefaults(), array('foo4' => array(1, 2)), '->getDefaults() return the default values for each argument');
