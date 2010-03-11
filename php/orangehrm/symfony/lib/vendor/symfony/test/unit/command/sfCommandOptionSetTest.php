<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(20, new lime_output_color());

$foo = new sfCommandOption('foo', 'f');
$bar = new sfCommandOption('bar', 'b');
$foo1 = new sfCommandOption('fooBis', 'f');
$foo2 = new sfCommandOption('foo', 'p');

// __construct()
$t->diag('__construct()');
$optionSet = new sfCommandOptionSet();
$t->is($optionSet->getOptions(), array(), '__construct() creates a new sfCommandOptionSet object');

$optionSet = new sfCommandOptionSet(array($foo, $bar));
$t->is($optionSet->getOptions(), array('foo' => $foo, 'bar' => $bar), '__construct() takes an array of sfCommandOption objects as its first argument');

// ->setOptions()
$t->diag('->setOptions()');
$optionSet = new sfCommandOptionSet();
$optionSet->setOptions(array($foo));
$t->is($optionSet->getOptions(), array('foo' => $foo), '->setOptions() sets the array of sfCommandOption objects');
$optionSet->setOptions(array($bar));
$t->is($optionSet->getOptions(), array('bar' => $bar), '->setOptions() clears all sfCommandOption objects');
try
{
  $optionSet->getOptionForShortcut('f');
  $t->fail('->setOptions() clears all sfCommandOption objects');
}
catch (sfCommandException $e)
{
  $t->pass('->setOptions() clears all sfCommandOption objects');
}

// ->addOptions()
$t->diag('->addOptions()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array($foo));
$t->is($optionSet->getOptions(), array('foo' => $foo), '->addOptions() adds an array of sfCommandOption objects');
$optionSet->addOptions(array($bar));
$t->is($optionSet->getOptions(), array('foo' => $foo, 'bar' => $bar), '->addOptions() does not clear existing sfCommandOption objects');

// ->addOption()
$t->diag('->addOption()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOption($foo);
$t->is($optionSet->getOptions(), array('foo' => $foo), '->addOption() adds a sfCommandOption object');
$optionSet->addOption($bar);
$t->is($optionSet->getOptions(), array('foo' => $foo, 'bar' => $bar), '->addOption() adds a sfCommandOption object');
try
{
  $optionSet->addOption($foo2);
  $t->fail('->addOption() throws a sfCommandException if the another option is already registered with the same name');
}
catch (sfCommandException $e)
{
  $t->pass('->addOption() throws a sfCommandException if the another option is already registered with the same name');
}
try
{
  $optionSet->addOption($foo1);
  $t->fail('->addOption() throws a sfCommandException if the another option is already registered with the same shortcut');
}
catch (sfCommandException $e)
{
  $t->pass('->addOption() throws a sfCommandException if the another option is already registered with the same shortcut');
}

// ->getOption()
$t->diag('->getOption()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array($foo));
$t->is($optionSet->getOption('foo'), $foo, '->getOption() returns a sfCommandOption by its name');
try
{
  $optionSet->getOption('bar');
  $t->fail('->getOption() throws an exception if the option name does not exist');
}
catch (sfCommandException $e)
{
  $t->pass('->getOption() throws an exception if the option name does not exist');
}

// ->hasOption()
$t->diag('->hasOption()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array($foo));
$t->is($optionSet->hasOption('foo'), true, '->hasOption() returns true if a sfCommandOption exists for the given name');
$t->is($optionSet->hasOption('bar'), false, '->hasOption() returns false if a sfCommandOption exists for the given name');

// ->hasShortcut()
$t->diag('->hasShortcut()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array($foo));
$t->is($optionSet->hasShortcut('f'), true, '->hasShortcut() returns true if a sfCommandOption exists for the given shortcut');
$t->is($optionSet->hasShortcut('b'), false, '->hasShortcut() returns false if a sfCommandOption exists for the given shortcut');

// ->getOptionForShortcut()
$t->diag('->getOptionForShortcut()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array($foo));
$t->is($optionSet->getOptionForShortcut('f'), $foo, '->getOptionForShortcut() returns a sfCommandOption by its shortcut');
try
{
  $optionSet->getOptionForShortcut('l');
  $t->fail('->getOption() throws an exception if the shortcut does not exist');
}
catch (sfCommandException $e)
{
  $t->pass('->getOption() throws an exception if the shortcut does not exist');
}

// ->getDefaults()
$t->diag('->getDefaults()');
$optionSet = new sfCommandOptionSet();
$optionSet->addOptions(array(
  new sfCommandOption('foo1', null, sfCommandOption::PARAMETER_NONE),
  new sfCommandOption('foo2', null, sfCommandOption::PARAMETER_REQUIRED),
  new sfCommandOption('foo3', null, sfCommandOption::PARAMETER_REQUIRED, '', 'default'),
  new sfCommandOption('foo4', null, sfCommandOption::PARAMETER_OPTIONAL),
  new sfCommandOption('foo5', null, sfCommandOption::PARAMETER_OPTIONAL, '', 'default'),
  new sfCommandOption('foo6', null, sfCommandOption::PARAMETER_OPTIONAL | sfCommandOption::IS_ARRAY),
  new sfCommandOption('foo7', null, sfCommandOption::PARAMETER_OPTIONAL | sfCommandOption::IS_ARRAY, '', array(1, 2)),
));
$defaults = array(
  'foo1' => null,
  'foo2' => null,
  'foo3' => 'default',
  'foo4' => null,
  'foo5' => 'default',
  'foo6' => array(),
  'foo7' => array(1, 2),
);
$t->is($optionSet->getDefaults(), $defaults, '->getDefaults() returns the default values for all options');
