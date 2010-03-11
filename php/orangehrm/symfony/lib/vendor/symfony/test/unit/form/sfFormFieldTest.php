<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(28, new lime_output_color());

// widgets
$authorSchema = new sfWidgetFormSchema(array(
  'id'   => new sfWidgetFormInputHidden(),
  'name' => $nameWidget = new sfWidgetFormInput(),
));
$authorSchema->setNameFormat('article[author][%s]');

$schema = new sfWidgetFormSchema(array(
  'title'  => $titleWidget = new sfWidgetFormInput(),
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

// ->getValue() ->getWidget() ->getParent() ->getError() ->hasError()
$t->diag('->getValue() ->getName() ->getWidget() ->getParent() ->getError() ->hasError()');
$t->ok($f->getWidget() == $titleWidget, '->getWidget() returns the form field widget');
$t->is($f->getName(), 'title', '->getName() returns the form field name');
$t->is($f->getValue(), 'symfony', '->getValue() returns the form field value');
$t->is($f->getParent(), $parent, '->getParent() returns the form field parent');
$t->is($f->getError(), $titleError, '->getError() returns the form field error');
$t->is($f->hasError(), true, '->hasError() returns true if the form field has some error');

$errorSchema1 = new sfValidatorErrorSchema(new sfValidatorString());
$errorSchema1->addError(new sfValidatorError(new sfValidatorString(), 'error'), 'title1');
$parent1 = new sfFormFieldSchema($schema, null, 'article', array('title' => 'symfony'), $errorSchema1);
$f1 = $parent1['title'];
$t->is($f1->hasError(), false, '->hasError() returns false if the form field has no error');

// __toString()
$t->diag('__toString()');
$t->is($f->__toString(), '<input type="text" name="article[title]" value="symfony" id="article_title" />', '__toString() renders the form field with default HTML attributes');

// ->render()
$t->diag('->render()');
$t->is($f->render(array('class' => 'foo')), '<input type="text" name="article[title]" value="symfony" class="foo" id="article_title" />', '->render() renders the form field');

// ->renderRow()
$t->diag('->renderRow()');
$output = <<<EOF
<tr>
  <th><label for="article_title">Title</label></th>
  <td>  <ul class="error_list">
    <li>title error</li>
  </ul>
<input type="text" name="article[title]" value="symfony" id="article_title" /></td>
</tr>

EOF;
$t->is($f->renderRow(), $output, '->renderRow() renders a row');

$output = <<<EOF
<tr>
  <th><label for="article_title">Title</label></th>
  <td>  <ul class="error_list">
    <li>title error</li>
  </ul>
<input type="password" name="article[title]" value="symfony" class="foo" id="title" /></td>
</tr>

EOF;
$t->is($f->renderRow(array('class' => 'foo', 'type' => 'password', 'id' => 'title')), $output, '->renderRow() can take an array of HTML attributes as its first argument');

$output = <<<EOF
<tr>
  <th><label for="article_title">My title</label></th>
  <td>  <ul class="error_list">
    <li>title error</li>
  </ul>
<input type="text" name="article[title]" value="symfony" id="article_title" /></td>
</tr>

EOF;
$t->is($f->renderRow(array(), 'My title'), $output, '->renderRow() can take a label name as its second argument');

$output = <<<EOF
<tr>
  <th><label for="article_title">Title</label></th>
  <td>  <ul class="error_list">
    <li>title error</li>
  </ul>
<input type="text" name="article[title]" value="symfony" id="article_title" /><br />help</td>
</tr>

EOF;
$t->is($f->renderRow(array(), null, 'help'), $output, '->renderRow() can take a help message as its third argument');

$output = <<<EOF
<tr>
  <th><label for="article_author">Author</label></th>
  <td><tr>
  <th><label for="article_author_name">Name</label></th>
  <td>  <ul class="error_list">
    <li>name error</li>
  </ul>
<input type="text" name="article[author][name]" value="Fabien" id="article_author_name" /><input type="hidden" name="article[author][id]" id="article_author_id" /></td>
</tr>
</td>
</tr>

EOF;
$t->is($child->renderRow(), $output, '->renderRow() renders a row when the widget has a parent');

try
{
  $parent->renderRow();
  $t->fail('->renderRow() throws an LogicException if the form field has no parent');
}
catch (LogicException $e)
{
  $t->pass('->renderRow() throws an LogicException if the form field has no parent');
}

// ->renderLabel()
$t->diag('->renderLabel()');
$t->is($f->renderLabel(), '<label for="article_title">Title</label>', '->renderLabel() renders the label as HTML');
$t->is($f->renderLabel(null, array('class' => 'foo')), '<label class="foo" for="article_title">Title</label>', '->renderLabel() renders optional HTML attributes');
try
{
  $parent->renderLabel();
  $t->fail('->renderLabel() throws an LogicException if the form field has no parent');
}
catch (LogicException $e)
{
  $t->pass('->renderLabel() throws an LogicException if the form field has no parent');
}

// ->renderLabelName()
$t->diag('->renderLabelName()');
$t->is($f->renderLabelName(), 'Title', '->renderLabelName() renders the label name');
try
{
  $parent->renderLabelName();
  $t->fail('->renderLabelName() throws an LogicException if the form field has no parent');
}
catch (LogicException $e)
{
  $t->pass('->renderLabelName() throws an LogicException if the form field has no parent');
}

// ->renderId()
$t->diag('->renderId()');
$t->is($f->renderId(), 'article_title', '->renderId() renders the id attribute of the field');
$t->is($child['name']->renderId(), 'article_author_name', '->renderId() renders the id attribute of the field');

// ->isHidden()
$t->diag('->isHidden()');
$t->is($f->isHidden(), false, '->isHidden() is a proxy method to the isHidden() method of the widget');

// ->renderError();
$t->diag('->renderError()');
$output = <<<EOF
  <ul class="error_list">
    <li>title error</li>
  </ul>

EOF;
$t->is($f->renderError(), $output, '->renderError() renders errors as HTML');
$t->is($child->renderError(), '', '->renderRow() renders errors as HTML when the widget has a parent');
$output = <<<EOF
  <ul class="error_list">
    <li>name error</li>
  </ul>

EOF;
$t->is($child['name']->renderError(), $output, '->renderRow() renders errors as HTML when the widget has a parent');

try
{
  $parent->renderError();
  $t->fail('->renderError() throws an LogicException if the form field has no parent');
}
catch (LogicException $e)
{
  $t->pass('->renderError() throws an LogicException if the form field has no parent');
}

// global errors
$authorErrorSchema = new sfValidatorErrorSchema(new sfValidatorString());
$authorErrorSchema->addError(new sfValidatorError(new sfValidatorString(), 'name error'), 'name');
$authorErrorSchema->addError(new sfValidatorError(new sfValidatorString(), 'non existent field error'), 'non_existent_field');
$authorErrorSchema->addError(new sfValidatorError(new sfValidatorString(), 'hidden field error'), 'id');

$articleErrorSchema = new sfValidatorErrorSchema(new sfValidatorString());
$articleErrorSchema->addError($titleError = new sfValidatorError(new sfValidatorString(), 'title error'), 'title');
$articleErrorSchema->addError($authorErrorSchema, 'author');

$parent = new sfFormFieldSchema($schema, null, 'article', array('title' => 'symfony', 'author' => array('name' => 'Fabien')), $articleErrorSchema);
$child = $parent['author'];
$output = <<<EOF
  <ul class="error_list">
    <li>non existent field error</li>
    <li>Id: hidden field error</li>
  </ul>

EOF;
$t->is($child->renderError(), $output, '->renderError() renders global errors as expected (global errors, hidden field errors, non existent field errors)');
