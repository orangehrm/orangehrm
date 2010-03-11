<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(41, new lime_output_color());

// __construct()
$t->diag('__construct()');
$argumentSet = new sfCommandArgumentSet();
$optionSet = new sfCommandOptionSet();

$manager = new sfCommandManager();
$t->isa_ok($manager->getArgumentSet(), 'sfCommandArgumentSet', '__construct() creates a new sfCommandArgumentsSet if none given');
$t->isa_ok($manager->getOptionSet(), 'sfCommandOptionSet', '__construct() creates a new sfCommandOptionSet if none given');

$manager = new sfCommandManager($argumentSet);
$t->is($manager->getArgumentSet(), $argumentSet, '__construct() takes a sfCommandArgumentSet as its first argument');
$t->isa_ok($manager->getOptionSet(), 'sfCommandOptionSet', '__construct() takes a sfCommandArgumentSet as its first argument');

$manager = new sfCommandManager($argumentSet, $optionSet);
$t->is($manager->getOptionSet(), $optionSet, '__construct() can take a sfCommandOptionSet as its second argument');

// ->setArgumentSet() ->getArgumentSet()
$t->diag('->setArgumentSet() ->getArgumentSet()');
$manager = new sfCommandManager(new sfCommandArgumentSet());
$argumentSet = new sfCommandArgumentSet();
$manager->setArgumentSet($argumentSet);
$t->is($manager->getArgumentSet(), $argumentSet, '->setArgumentSet() sets the manager argument set');

// ->setOptionSet() ->getOptionSet()
$t->diag('->setOptionSet() ->getOptionSet()');
$manager = new sfCommandManager(new sfCommandArgumentSet());
$optionSet = new sfCommandOptionSet();
$manager->setOptionSet($optionSet);
$t->is($manager->getOptionSet(), $optionSet, '->setOptionSet() sets the manager option set');

// ->process()
$t->diag('->process()');
$argumentSet = new sfCommandArgumentSet(array(
  new sfCommandArgument('foo1', sfCommandArgument::REQUIRED),
  new sfCommandArgument('foo2', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY),
));
$optionSet = new sfCommandOptionSet(array(
  new sfCommandOption('foo1', null, sfCommandOption::PARAMETER_NONE),
  new sfCommandOption('foo2', 'f', sfCommandOption::PARAMETER_NONE),
  new sfCommandOption('foo3', null, sfCommandOption::PARAMETER_OPTIONAL, '', 'default3'),
  new sfCommandOption('foo4', null, sfCommandOption::PARAMETER_OPTIONAL, '', 'default4'),
  new sfCommandOption('foo5', null, sfCommandOption::PARAMETER_OPTIONAL, '', 'default5'),
  new sfCommandOption('foo6', 'r', sfCommandOption::PARAMETER_REQUIRED, '', 'default5'),
  new sfCommandOption('foo7', 't', sfCommandOption::PARAMETER_REQUIRED, '', 'default7'),
  new sfCommandOption('foo8', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY),
  new sfCommandOption('foo9', 's', sfCommandOption::PARAMETER_OPTIONAL, '', 'default9'),
  new sfCommandOption('foo10', 'u', sfCommandOption::PARAMETER_OPTIONAL, '', 'default10'),
  new sfCommandOption('foo11', 'v', sfCommandOption::PARAMETER_OPTIONAL, '', 'default11'),
));
$manager = new sfCommandManager($argumentSet, $optionSet);
$manager->process('--foo1 -f --foo3 --foo4="foo4" --foo5=foo5 -r"foo6 foo6" -t foo7 --foo8="foo" --foo8=bar -s -u foo10 -vfoo11 foo1 foo2 foo3 foo4');
$options = array(
  'foo1' => true,
  'foo2' => true,
  'foo3' => 'default3',
  'foo4' => 'foo4',
  'foo5' => 'foo5',
  'foo6' => 'foo6 foo6',
  'foo7' => 'foo7',
  'foo8' => array('foo', 'bar'),
  'foo9' => 'default9',
  'foo10' => 'foo10',
  'foo11' => 'foo11',
);
$arguments = array(
  'foo1' => 'foo1',
  'foo2' => array('foo2', 'foo3', 'foo4')
);
$t->ok($manager->isValid(), '->process() processes CLI options');
$t->is($manager->getOptionValues(), $options, '->process() processes CLI options');
$t->is($manager->getArgumentValues(), $arguments, '->process() processes CLI options');

// ->getOptionValue()
$t->diag('->getOptionValue()');
foreach ($options as $name => $value)
{
  $t->is($manager->getOptionValue($name), $value, '->getOptionValue() returns the value for the given option name');
}

try
{
  $manager->getOptionValue('nonexistant');
  $t->fail('->getOptionValue() throws a sfCommandException if the option name does not exist');
}
catch (sfCommandException $e)
{
  $t->pass('->getOptionValue() throws a sfCommandException if the option name does not exist');
}

// ->getArgumentValue()
$t->diag('->getArgumentValue()');
foreach ($arguments as $name => $value)
{
  $t->is($manager->getArgumentValue($name), $value, '->getArgumentValue() returns the value for the given argument name');
}

try
{
  $manager->getArgumentValue('nonexistant');
  $t->fail('->getArgumentValue() throws a sfCommandException if the argument name does not exist');
}
catch (sfCommandException $e)
{
  $t->pass('->getArgumentValue() throws a sfCommandException if the argument name does not exist');
}

// ->isValid() ->getErrors()
$t->diag('->isValid() ->getErrors()');
$argumentSet = new sfCommandArgumentSet();
$manager = new sfCommandManager($argumentSet);
$manager->process('foo');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$argumentSet = new sfCommandArgumentSet(array(new sfCommandArgument('foo', sfCommandArgument::REQUIRED)));
$manager = new sfCommandManager($argumentSet);
$manager->process('');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$optionSet = new sfCommandOptionSet(array(new sfCommandOption('foo', null, sfCommandOption::PARAMETER_REQUIRED)));
$manager = new sfCommandManager(null, $optionSet);
$manager->process('--foo');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$optionSet = new sfCommandOptionSet(array(new sfCommandOption('foo', 'f', sfCommandOption::PARAMETER_REQUIRED)));
$manager = new sfCommandManager(null, $optionSet);
$manager->process('-f');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$optionSet = new sfCommandOptionSet(array(new sfCommandOption('foo', null, sfCommandOption::PARAMETER_NONE)));
$manager = new sfCommandManager(null, $optionSet);
$manager->process('--foo="bar"');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$manager = new sfCommandManager();
$manager->process('--bar');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$manager = new sfCommandManager();
$manager->process('-b');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');

$manager = new sfCommandManager();
$manager->process('--bar="foo"');
$t->ok(!$manager->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($manager->getErrors()), 1, '->getErrors() returns an array of errors');
