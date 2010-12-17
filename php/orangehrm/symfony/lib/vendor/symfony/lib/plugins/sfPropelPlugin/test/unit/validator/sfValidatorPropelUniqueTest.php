<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
include dirname(__FILE__).'/../../bootstrap/functional.php';
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

$t = new lime_test(2);

// ->clean()
$t->diag('->clean()');

$validator = new sfValidatorPropelUnique(array('model' => 'Author', 'column' => 'name'));

$author = new Author();
$author->setName('==NAME==');
$author->save();

try
{
  $validator->clean(array('name' => '==NAME=='));
  $t->fail('->clean() throws an error on the column');
}
catch (sfValidatorErrorSchema $errors)
{
  $t->is(isset($errors['name']), true, '->clean() throws an error on the column');
}
catch (Exception $e)
{
  $t->fail('->clean() throws an error on the column');
  $t->diag('    '.$e->getMessage());
}

$validator->setOption('field', 'author_name');

try
{
  $validator->clean(array('author_name' => '==NAME=='));
  $t->fail('->clean() throws an error on the field');
}
catch (sfValidatorErrorSchema $errors)
{
  $t->is(isset($errors['author_name']), true, '->clean() throws an error on the field');
}
catch (Exception $e)
{
  $t->fail('->clean() throws an error on the field');
  $t->diag('    '.$e->getMessage());
}

$author->delete();
