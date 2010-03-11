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

$t = new lime_test(11, new lime_output_color());

$context = sfContext::getInstance();

// ->initialize()
$t->diag('->initialize()');

try
{
  $v = new sfCallbackValidator($context);
  $t->fail('->initialize() takes a mandatory "callback" parameter');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() takes a mandatory "callback" parameter');
}

try
{
  $v = new sfCallbackValidator($context, array('callback' => 'arandomstring'));
  $t->fail('->initialize() takes a callable as a "callback" parameter');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() takes a callable as a "callback" parameter');
}

function callbackValidator($value)
{
  return (boolean) $value;
}

class callbackClassValidator
{
  public function callbackValidator($value)
  {
    return (boolean) $value;
  }

  static public function staticCallbackValidator($value)
  {
    return (boolean) $value;
  }
}

$t->ok(new sfCallbackValidator($context, array('callback' => 'callbackValidator')), '->initialize() can take a function as a callback');
$t->ok(new sfCallbackValidator($context, array('callback' => array(new callbackClassValidator(), 'callbackValidator'))), '->initialize() can take an instance method as a callback');
$t->ok(new sfCallbackValidator($context, array('callback' => array('callbackClassValidator', 'staticCallbackValidator'))), '->initialize() can take a static method as a callback');

$t->ok($v = new sfCallbackValidator($context, array('callback' => 'callbackValidator', 'invalid_error' => 'my error')), '->initialize() takes a custom message "invalid_error"');
$value = false;
$error = null;
$v->execute($value, $error);
$t->is($error, 'my error', '->execute() changes "$error" with a custom message');

// ->execute()
$t->diag('->execute()');
$c = new sfCallbackValidator($context, array('callback' => 'callbackValidator'));

$value = true;
$error = null;
$t->ok($v->execute($value, $error), '->execute() returns true if callback returns true');
$t->is($error, null, '->execute() doesn\'t change "$error" if it returns true');

$value = false;
$error = null;
$t->ok(!$v->execute($value, $error), '->execute() returns false if callback returns false');
$t->isnt($error, null, '->execute() changes "$error" with a default message if it returns false');
