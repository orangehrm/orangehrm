<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(34);

// __construct()
$t->diag('__construct()');
$option = new sfCommandOption('foo');
$t->is($option->getName(), 'foo', '__construct() takes a name as its first argument');
$option = new sfCommandOption('--foo');
$t->is($option->getName(), 'foo', '__construct() removes the leading -- of the option name');

// shortcut argument
$option = new sfCommandOption('foo', 'f');
$t->is($option->getShortcut(), 'f', '__construct() can take a shortcut as its second argument');
$option = new sfCommandOption('foo', '-f');
$t->is($option->getShortcut(), 'f', '__construct() removes the leading - of the shortcut');

// mode argument
$option = new sfCommandOption('foo', 'f');
$t->is($option->acceptParameter(), false, '__construct() gives a "sfCommandOption::PARAMETER_NONE" mode by default');
$t->is($option->isParameterRequired(), false, '__construct() gives a "sfCommandOption::PARAMETER_NONE" mode by default');
$t->is($option->isParameterOptional(), false, '__construct() gives a "sfCommandOption::PARAMETER_NONE" mode by default');

$option = new sfCommandOption('foo', 'f', null);
$t->is($option->acceptParameter(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');
$t->is($option->isParameterRequired(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');
$t->is($option->isParameterOptional(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');

$option = new sfCommandOption('foo', 'f', sfCommandOption::PARAMETER_NONE);
$t->is($option->acceptParameter(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');
$t->is($option->isParameterRequired(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');
$t->is($option->isParameterOptional(), false, '__construct() can take "sfCommandOption::PARAMETER_NONE" as its mode');

$option = new sfCommandOption('foo', 'f', sfCommandOption::PARAMETER_REQUIRED);
$t->is($option->acceptParameter(), true, '__construct() can take "sfCommandOption::PARAMETER_REQUIRED" as its mode');
$t->is($option->isParameterRequired(), true, '__construct() can take "sfCommandOption::PARAMETER_REQUIRED" as its mode');
$t->is($option->isParameterOptional(), false, '__construct() can take "sfCommandOption::PARAMETER_REQUIRED" as its mode');

$option = new sfCommandOption('foo', 'f', sfCommandOption::PARAMETER_OPTIONAL);
$t->is($option->acceptParameter(), true, '__construct() can take "sfCommandOption::PARAMETER_OPTIONAL" as its mode');
$t->is($option->isParameterRequired(), false, '__construct() can take "sfCommandOption::PARAMETER_OPTIONAL" as its mode');
$t->is($option->isParameterOptional(), true, '__construct() can take "sfCommandOption::PARAMETER_OPTIONAL" as its mode');

try
{
  $option = new sfCommandOption('foo', 'f', 'ANOTHER_ONE');
  $t->fail('__construct() throws an sfCommandException if the mode is not valid');
}
catch (sfCommandException $e)
{
  $t->pass('__construct() throws an sfCommandException if the mode is not valid');
}

// ->isArray()
$t->diag('->isArray()');
$option = new sfCommandOption('foo', null, sfCommandOption::IS_ARRAY);
$t->ok($option->isArray(), '->isArray() returns true if the option can be an array');
$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_NONE | sfCommandOption::IS_ARRAY);
$t->ok($option->isArray(), '->isArray() returns true if the option can be an array');
$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_NONE);
$t->ok(!$option->isArray(), '->isArray() returns false if the option can not be an array');

// ->getHelp()
$t->diag('->getHelp()');
$option = new sfCommandOption('foo', 'f', null, 'Some help');
$t->is($option->getHelp(), 'Some help', '->getHelp() returns the help message');

// ->getDefault()
$t->diag('->getDefault()');
$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_OPTIONAL, '', 'default');
$t->is($option->getDefault(), 'default', '->getDefault() returns the default value');

$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_REQUIRED, '', 'default');
$t->is($option->getDefault(), 'default', '->getDefault() returns the default value');

$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_REQUIRED);
$t->ok(is_null($option->getDefault()), '->getDefault() returns null if no default value is configured');

$option = new sfCommandOption('foo', null, sfCommandOption::IS_ARRAY);
$t->is($option->getDefault(), array(), '->getDefault() returns an empty array if option is an array');

$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_NONE);
$t->ok($option->getDefault() === false, '->getDefault() returns false if the option does not take a parameter');

// ->setDefault()
$t->diag('->setDefault()');
$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_REQUIRED, '', 'default');
$option->setDefault(null);
$t->ok(is_null($option->getDefault()), '->setDefault() can reset the default value by passing null');
$option->setDefault('another');
$t->is($option->getDefault(), 'another', '->setDefault() changes the default value');

$option = new sfCommandOption('foo', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY);
$option->setDefault(array(1, 2));
$t->is($option->getDefault(), array(1, 2), '->setDefault() changes the default value');

try
{
  $option = new sfCommandOption('foo', 'f', sfCommandOption::PARAMETER_NONE);
  $option->setDefault('default');
  $t->fail('->setDefault() throws an sfCommandException if you give a default value for a PARAMETER_NONE option');
}
catch (sfCommandException $e)
{
  $t->pass('->setDefault() throws an sfCommandException if you give a default value for a PARAMETER_NONE option');
}

try
{
  $option = new sfCommandOption('foo', 'f', sfCommandOption::IS_ARRAY);
  $option->setDefault('default');
  $t->fail('->setDefault() throws an sfCommandException if you give a default value which is not an array for a IS_ARRAY option');
}
catch (sfCommandException $e)
{
  $t->pass('->setDefault() throws an sfCommandException if you give a default value which is not an array for a IS_ARRAY option');
}
