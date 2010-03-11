<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/../../../../test/unit/sfContextMock.class.php');

$t = new lime_test(9, new lime_output_color());

$context = sfContext::getInstance();

// ->initialize()
$t->diag('->initialize()');

try
{
  $v = new sfRegexValidator($context);
  $t->fail('->initialize() takes a mandatory "pattern" parameter');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() takes a mandatory "pattern" parameter');
}

$t->ok(new sfRegexValidator($context, array('pattern' => '/\d+/')), '->initialize() takes a "pattern" as a parameter');

$v = new sfRegexValidator($context, array('pattern' => '/\d+/', 'match_error' => 'my error message'));
$value = 'a string';
$error = null;
$v->execute($value, $error);
$t->is($error, 'my error message', '->initialize() changes "$error" with a custom message if it returns false');

// ->execute()
$t->diag('->execute()');
$v->initialize($context, array('pattern' => '/\d+/'));

$value = 12;
$error = null;
$t->ok($v->execute($value, $error), '->execute() returns true if value match the pattern');
$t->is($error, null, '->execute() doesn\'t change "$error" if it returns true');

$value = 'a string';
$error = null;
$t->ok(!$v->execute($value, $error), '->execute() returns false if value does not match the pattern');
$t->isnt($error, null, '->execute() changes "$error" with a default message if it returns false');

// match parameter
$v->initialize($context, array('pattern' => '/\d+/', 'match' => false));

$value = 12;
$error = null;
$t->ok(!$v->execute($value, $error), '->execute() returns false if value match the pattern and "match" parameter is false');

$value = 'a string';
$error = null;
$t->ok($v->execute($value, $error), '->execute() returns true if value does not match the pattern and "match" parameter is false');
