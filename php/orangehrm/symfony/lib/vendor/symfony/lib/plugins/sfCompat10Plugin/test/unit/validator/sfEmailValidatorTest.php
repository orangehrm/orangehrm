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

$t = new lime_test(28, new lime_output_color());

$context = sfContext::getInstance();
$v = new sfEmailValidator($context);

// ->execute()
$t->diag('->execute()');

$validEmails = array(
  'fabien.potencier@symfony-project.com',
  'example@example.co.uk',
  'fabien_potencier@example.fr',
);

$invalidEmails = array(
  'example',
  'example@',
  'example@localhost',
  'example@example.com@example.com',
);

$validEmailsNotStrict = array(
  'fabien.potencier@symfony-project.com',
  'example@example.co.uk',
  'fabien_potencier@example.fr',
  'example@localhost',
);

$invalidEmailsNotStrict = array(
  'example',
  'example@',
  'example@example.com@example.com',
);

$v->initialize($context);
foreach ($validEmails as $value)
{
  $error = null;
  $t->ok($v->execute($value, $error), sprintf('->execute() returns true for a valid email "%s"', $value));
  $t->is($error, null, '->execute() doesn\'t change "$error" if it returns true');
}

foreach ($invalidEmails as $value)
{
  $error = null;
  $t->ok(!$v->execute($value, $error), sprintf('->execute() returns false for an invalid email "%s"', $value));
  $t->isnt($error, null, '->execute() changes "$error" with a default message if it returns false');
}

// strict parameter
$t->diag('->execute() - strict parameter');
$v->initialize($context, array('strict' => false));
foreach ($validEmailsNotStrict as $value)
{
  $error = null;
  $t->ok($v->execute($value, $error), sprintf('->execute() returns true for a valid email "%s"', $value));
  $t->is($error, null, '->execute() doesn\'t change "$error" if it returns true');
}

foreach ($invalidEmailsNotStrict as $value)
{
  $error = null;
  $t->ok(!$v->execute($value, $error), sprintf('->execute() returns false for an invalid email "%s"', $value));
  $t->isnt($error, null, '->execute() changes "$error" with a default message if it returns false');
}
