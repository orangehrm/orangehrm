<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(11);

// widgets
$authorSchema = new sfWidgetFormSchema(array(
  'name' => $nameWidget = new sfWidgetFormInputText(),
));
$authorSchema->setNameFormat('article[author][%s]');

$schema = new sfWidgetFormSchema(array(
  'title'  => $titleWidget = new sfWidgetFormInputText(),
  'author' => $authorSchema,
));
$schema->setNameFormat('article[%s]');

// errors
$authorErrorSchema = new sfValidatorErrorSchema(new sfValidatorString());
$authorErrorSchema->addError(new sfValidatorError(new sfValidatorString(), 'name error'), 'name');

$articleErrorSchema = new sfValidatorErrorSchema(new sfValidatorString());
$articleErrorSchema->addError($titleError = new sfValidatorError(new sfValidatorString(), 'title error'), 'title');
$articleErrorSchema->addError($authorErrorSchema, 'author');

$parent = new sfFormFieldSchema($schema, null, 'article', array('title' => 'symfony', 'author' => array('name' => 'Fabien')), $articleErrorSchema);
$f = $parent['title'];
$child = $parent['author'];

// ArrayAccess interface
$t->diag('ArrayAccess interface');
$t->is(isset($parent['title']), true, 'sfFormField implements the ArrayAccess interface');
$t->is(isset($parent['title1']), false, 'sfFormField implements the ArrayAccess interface');
$t->is($parent['title'], $f, 'sfFormField implements the ArrayAccess interface');
try
{
  unset($parent['title']);
  $t->fail('sfFormField implements the ArrayAccess interface but in read-only mode');
}
catch (LogicException $e)
{
  $t->pass('sfFormField implements the ArrayAccess interface but in read-only mode');
}

try
{
  $parent['title'] = null;
  $t->fail('sfFormField implements the ArrayAccess interface but in read-only mode');
}
catch (LogicException $e)
{
  $t->pass('sfFormField implements the ArrayAccess interface but in read-only mode');
}

try
{
  $parent['title1'];
  $t->fail('sfFormField implements the ArrayAccess interface but in read-only mode');
}
catch (LogicException $e)
{
  $t->pass('sfFormField implements the ArrayAccess interface but in read-only mode');
}

// implements Countable
$t->diag('implements Countable');
$widgetSchema = new sfWidgetFormSchema(array(
  'w1' => $w1 = new sfWidgetFormInputText(),
  'w2' => $w2 = new sfWidgetFormInputText(),
));
$f = new sfFormFieldSchema($widgetSchema, null, 'article', array());
$t->is(count($f), 2, 'sfFormFieldSchema implements the Countable interface');

// implements Iterator
$t->diag('implements Iterator');
$f = new sfFormFieldSchema($widgetSchema, null, 'article', array());

$values = array();
foreach ($f as $name => $value)
{
  $values[$name] = $value;
}
$t->is(isset($values['w1']), true, 'sfFormFieldSchema implements the Iterator interface');
$t->is(isset($values['w2']), true, 'sfFormFieldSchema implements the Iterator interface');
$t->is(count($values), 2, 'sfFormFieldSchema implements the Iterator interface');

$t->diag('implements Iterator respecting the order of fields');
$widgetSchema->moveField('w2', 'first');
$f = new sfFormFieldSchema($widgetSchema, null, 'article', array());

$values = array();
foreach ($f as $name => $value)
{
  $values[$name] = $value;
}
$t->is(array_keys($values), array('w2', 'w1'), 'sfFormFieldSchema keeps the order');
