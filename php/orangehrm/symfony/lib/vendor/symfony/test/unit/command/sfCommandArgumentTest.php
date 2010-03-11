<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(16, new lime_output_color());

// __construct()
$t->diag('__construct()');
$argument = new sfCommandArgument('foo');
$t->is($argument->getName(), 'foo', '__construct() takes a name as its first argument');

// mode argument
$argument = new sfCommandArgument('foo');
$t->is($argument->isRequired(), false, '__construct() gives a "sfCommandArgument::OPTIONAL" mode by default');

$argument = new sfCommandArgument('foo', null);
$t->is($argument->isRequired(), false, '__construct() can take "sfCommandArgument::OPTIONAL" as its mode');

$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL);
$t->is($argument->isRequired(), false, '__construct() can take "sfCommandArgument::PARAMETER_OPTIONAL" as its mode');

$argument = new sfCommandArgument('foo', sfCommandArgument::REQUIRED);
$t->is($argument->isRequired(), true, '__construct() can take "sfCommandArgument::PARAMETER_REQUIRED" as its mode');

try
{
  $argument = new sfCommandArgument('foo', 'ANOTHER_ONE');
  $t->fail('__construct() throws an sfCommandException if the mode is not valid');
}
catch (sfCommandException $e)
{
  $t->pass('__construct() throws an sfCommandException if the mode is not valid');
}

// ->isArray()
$t->diag('->isArray()');
$argument = new sfCommandArgument('foo', sfCommandArgument::IS_ARRAY);
$t->ok($argument->isArray(), '->isArray() returns true if the argument can be an array');
$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY);
$t->ok($argument->isArray(), '->isArray() returns true if the argument can be an array');
$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL);
$t->ok(!$argument->isArray(), '->isArray() returns false if the argument can not be an array');

// ->getHelp()
$t->diag('->getHelp()');
$argument = new sfCommandArgument('foo', null, 'Some help');
$t->is($argument->getHelp(), 'Some help', '->getHelp() return the message help');

// ->getDefault()
$t->diag('->getDefault()');
$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL, '', 'default');
$t->is($argument->getDefault(), 'default', '->getDefault() return the default value');

// ->setDefault()
$t->diag('->setDefault()');
$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL, '', 'default');
$argument->setDefault(null);
$t->ok(is_null($argument->getDefault()), '->setDefault() can reset the default value by passing null');
$argument->setDefault('another');
$t->is($argument->getDefault(), 'another', '->setDefault() changes the default value');

$argument = new sfCommandArgument('foo', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY);
$argument->setDefault(array(1, 2));
$t->is($argument->getDefault(), array(1, 2), '->setDefault() changes the default value');

try
{
  $argument = new sfCommandArgument('foo', sfCommandArgument::REQUIRED);
  $argument->setDefault('default');
  $t->fail('->setDefault() throws an sfCommandException if you give a default value for a required argument');
}
catch (sfCommandException $e)
{
  $t->pass('->setDefault() throws an sfCommandException if you give a default value for a required argument');
}

try
{
  $argument = new sfCommandArgument('foo', sfCommandArgument::IS_ARRAY);
  $argument->setDefault('default');
  $t->fail('->setDefault() throws an sfCommandException if you give a default value which is not an array for a IS_ARRAY option');
}
catch (sfCommandException $e)
{
  $t->pass('->setDefault() throws an sfCommandException if you give a default value which is not an array for a IS_ARRAY option');
}
