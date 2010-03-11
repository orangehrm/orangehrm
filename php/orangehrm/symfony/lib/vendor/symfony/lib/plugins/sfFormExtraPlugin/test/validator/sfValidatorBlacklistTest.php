<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../bootstrap.php';
require_once dirname(__FILE__).'/../../lib/validator/sfValidatorBlacklist.class.php';

$t = new lime_test(5, new lime_output_color());

// __construct()
$t->diag('__construct()');
try
{
  new sfValidatorBlacklist();
}
catch (RuntimeException $e)
{
  $t->pass('__construct() expects a "forbidden_values" option');
}

$v = new sfValidatorBlacklist(array('forbidden_values' => array('foo', 'bar', 'baz')));

// ->clean()
$t->diag('->clean()');
try
{
  $v->clean('foo'); // "foo" is a forbidden value
  $t->fail('->clean() does not throw a sfValidatorError when the submitted value is invalid');
  $t->skip();
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() does not throw a sfValidatorError when the submitted value is invalid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a "invalid" error');
}

try
{
  $v->clean('gonzagues'); // "foo" is not a forbidden value
  $t->pass('->clean() does not throw a sfValidatorError when the submitted value is valid');
}
catch (sfValidatorError $e)
{
  $t->fail('->clean() does not throw a sfValidatorError when the submitted value is valid');
  $t->skip();
}


$v = new sfValidatorBlacklist(array('forbidden_values' => array('Foo', 'bAr', 'baZ'), 
                                    'case_sensitive'   => false));
try
{
  $v->clean('baz'); // "baz" is a forbidden value in a case sensitive context
  $t->fail('->clean() throws a sfValidatorError when the submitted value is invalid in a case sensitive context');
  $t->skip();
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError when the submitted value is invalid in a case sensitive context');
}
