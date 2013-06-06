<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend';
$fixtures = 'fixtures';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$t = new lime_test(41);

$t->diag("Test that these models don't generate forms or filters classes");
$noFormsOrFilters = array('UserGroup', 'UserPermission', 'GroupPermission');
foreach ($noFormsOrFilters as $model)
{
  $t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/'.$model.'Form.class.php'), false);
  $t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/base/Base'.$model.'Form.class.php'), false);
  $t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/'.$model.'FormFilter.class.php'), false);
  $t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/base/Base'.$model.'FormFilter.class.php'), false);
}

$t->diag('FormGeneratorTest model should generate forms but not filters');
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/FormGeneratorTestForm.class.php'), true);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/base/BaseFormGeneratorTestForm.class.php'), true);

$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/FormGeneratorTestFormFilter.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/base/BaseFormGeneratorTestFormFilter.class.php'), false);

$t->diag('FormGeneratorTest2 model should generate filters but not forms');
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/FormGeneratorTest2Form.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/base/BaseFormGeneratorTest2Form.class.php'), false);

$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/FormGeneratorTest2FormFilter.class.php'), true);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/base/BaseFormGeneratorTest2FormFilter.class.php'), true);

$t->diag('FormGeneratorTest3 model should not generate forms or filters');
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/FormGeneratorTest3Form.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/base/BaseFormGeneratorTest3Form.class.php'), false);

$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/FormGeneratorTest3FormFilter.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/base/BaseFormGeneratorTest3FormFilter.class.php'), false);

$t->diag('FormGeneratorTest3Translation not generate forms or filters');
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/FormGeneratorTest3TranslationForm.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/form/doctrine/base/BaseFormGeneratorTest3TranslationForm.class.php'), false);

$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/FormGeneratorTest3TranslationFormFilter.class.php'), false);
$t->is(file_exists(sfConfig::get('sf_lib_dir').'/filter/doctrine/base/BaseFormGeneratorTest3TranslationFormFilter.class.php'), false);

$t->diag('Check form generator generates forms with correct inheritance');
$test = new AuthorInheritanceForm();
$t->is(is_subclass_of($test, 'AuthorForm'), true);

$test = new AuthorInheritanceFormFilter();
$t->is(is_subclass_of($test, 'AuthorFormFilter'), true);

$t->diag('Check form generator adds columns to concrete inheritance forms');
$test = new AuthorForm();
$t->ok(!isset($test['additional']));

$test = new AuthorInheritanceConcreteForm();
$t->ok(isset($test['additional']));

$test = new AuthorFormFilter();
$t->ok(!isset($test['additional']));
$t->ok(!array_key_exists('additional', $test->getFields()));

$test = new AuthorInheritanceConcreteFormFilter();
$t->ok(isset($test['additional']));
$t->ok(array_key_exists('additional', $test->getFields()));

$t->diag('Check form generator respects relations tweaked by inheritance');
$test = new BlogArticleForm();
$t->is($test->getWidget('author_id')->getOption('model'), 'BlogAuthor');
$t->is($test->getValidator('author_id')->getOption('model'), 'BlogAuthor');

$test = new BlogArticleFormFilter();
$t->is($test->getWidget('author_id')->getOption('model'), 'BlogAuthor');
$t->is($test->getValidator('author_id')->getOption('model'), 'BlogAuthor');

$t->diag('Check enum primary keys');
try
{
  $test = new ResourceTypeForm();
  $t->pass('enum primary key widgets work');
}
catch (InvalidArgumentException $e)
{
  $t->fail('enum primary key widgets work');
  $t->diag('    '.$e->getMessage());
}
