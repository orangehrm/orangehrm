<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6, new lime_output_color());

// ->render()
$t->diag('->render()');

try
{
  new sfWidgetFormInputFileEditable();
  $t->fail('->render() throws an exception if you don\' pass a "file_src" option.');
}
catch (RuntimeException $e)
{
  $t->pass('->render() throws an exception if you don\' pass a "file_src" option.');
}

$w = new sfWidgetFormInputFileEditable(array(
  'file_src' => '-foo-',
));

$t->is($w->render('foo'), '-foo-<br /><input type="file" name="foo" id="foo" /><br /><input type="checkbox" name="foo_delete" id="foo_delete" /> <label for="foo_delete">remove the current file</label>', '->render() renders the widget as HTML');

$t->diag('with_delete option');
$w = new sfWidgetFormInputFileEditable(array(
  'file_src' => '-foo-',
  'with_delete' => false,
));
$t->is($w->render('foo'), '-foo-<br /><input type="file" name="foo" id="foo" /><br /> ', '->render() renders the widget as HTML');

$t->diag('delete_label option');
$w = new sfWidgetFormInputFileEditable(array(
  'file_src' => '-foo-',
  'delete_label' => 'delete',
));
$t->is($w->render('foo'), '-foo-<br /><input type="file" name="foo" id="foo" /><br /><input type="checkbox" name="foo_delete" id="foo_delete" /> <label for="foo_delete">delete</label>', '->render() renders the widget as HTML');

$t->diag('is_image option');
$w = new sfWidgetFormInputFileEditable(array(
  'file_src' => '-foo-',
  'is_image' => true,
));
$t->is($w->render('foo'), '<img src="-foo-" /><br /><input type="file" name="foo" id="foo" /><br /><input type="checkbox" name="foo_delete" id="foo_delete" /> <label for="foo_delete">remove the current file</label>', '->render() renders the widget as HTML');

$t->diag('template option');
$w = new sfWidgetFormInputFileEditable(array(
  'file_src' => '-foo-',
  'template' => '%input%',
));
$t->is($w->render('foo'), '<input type="file" name="foo" id="foo" />', '->render() renders the widget as HTML');
