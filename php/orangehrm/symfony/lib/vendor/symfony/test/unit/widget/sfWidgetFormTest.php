<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(34);

class MyWidgetForm extends sfWidgetForm
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->renderTag('input', array_merge(array('name' => $name), $attributes)).$this->renderContentTag('textarea', null, array_merge(array('name' => $name), $attributes));
  }

  public function generateId($name, $value = null)
  {
    return parent::generateId($name, $value);
  }
}

// __construct()
$t->diag('__construct()');
$w = new MyWidgetForm(array('id_format' => '%s'));
$t->is($w->render('foo'), '<input name="foo" id="foo" /><textarea name="foo" id="foo"></textarea>', '__construct() takes a id_format argument');
$t->is($w->render('foo', null, array('id' => 'id_foo')), '<input name="foo" id="id_foo" /><textarea name="foo" id="id_foo"></textarea>', '->render() id attributes takes precedence over auto generated ids');

$w = new MyWidgetForm(array('id_format' => false));
$t->is($w->render('foo'), '<input name="foo" /><textarea name="foo"></textarea>', '__construct() can disable id generation');

// ->getLabel() ->setLabel()
$t->diag('->getLabel() ->setLabel()');
$w = new MyWidgetForm();
$t->is($w->getLabel(), null, '->getLabel() returns null if no label has been defined');
$w = new MyWidgetForm(array('label' => 'foo'));
$t->is($w->getLabel(), 'foo', '->getLabel() returns the label');
$w->setLabel('bar');
$t->is($w->getLabel(), 'bar', '->setLabel() changes the label');

// ->getDefault() ->setDefault()
$t->diag('->getDefault() ->setDefault()');
$w = new MyWidgetForm();
$t->is($w->getDefault(), null, '->getDefault() returns null if no default value has been defined');
$w = new MyWidgetForm(array('default' => 'foo'));
$t->is($w->getDefault(), 'foo', '->getDefault() returns the default value');
$w->setDefault('bar');
$t->is($w->getDefault(), 'bar', '->setDefault() changes the default value for the widget');

// ->getParent() ->setParent()
$t->diag('->getParent() ->setParent()');
$w = new MyWidgetForm();
$t->is($w->getParent(), null, '->getParent() returns null if no widget schema has been defined');
$w->setParent($ws = new sfWidgetFormSchema());
$t->is($w->getParent(), $ws, '->setParent() associates a widget schema to the widget');

// ->getIdFormat() ->setIdFormat()
$t->diag('->getIdFormat() ->setIdFormat()');
$w = new MyWidgetForm();
$w->setIdFormat('id_%s');
$t->is($w->getIdFormat(), 'id_%s', '->setIdFormat() sets the format for the generated id attribute');

// ->isHidden()
$t->diag('->isHidden()');
$t->is($w->isHidden(), false, '->isHidden() returns false if a widget is not hidden');
$w->setHidden(true);
$t->is($w->isHidden(), true, '->isHidden() returns true if a widget is hidden');

// ->needsMultipartForm()
$t->diag('->needsMultipartForm()');
$t->is($w->needsMultipartForm(), false, '->needsMultipartForm() returns false if the widget does not need a multipart form');
$w = new MyWidgetForm(array('needs_multipart' => true));
$t->is($w->needsMultipartForm(), true, '->needsMultipartForm() returns false if the widget needs a multipart form');

// ->renderTag()
$t->diag('->renderTag()');
$w = new MyWidgetForm();
$t->is($w->renderTag('input'), '<input />', '->renderTag() does not add an id if no name is given');
$t->is($w->renderTag('input', array('id' => 'foo')), '<input id="foo" />', '->renderTag() does not add an id if one is given');
$t->is($w->renderTag('input', array('name' => 'foo')), '<input name="foo" id="foo" />', '->renderTag() adds an id if none is given and a name is given');
$w->setIdFormat('id_%s');
$t->is($w->renderTag('input', array('name' => 'foo')), '<input name="foo" id="id_foo" />', '->renderTag() uses the id_format to generate an id');
sfWidget::setXhtml(false);
$t->is($w->renderTag('input'), '<input>', '->renderTag() does not close tag if not in XHTML mode');
sfWidget::setXhtml(true);

// ->renderContentTag()
$t->diag('->renderContentTag()');
$w = new MyWidgetForm();
$t->is($w->renderContentTag('textarea'), '<textarea></textarea>', '->renderContentTag() does not add an id if no name is given');
$t->is($w->renderContentTag('textarea', '', array('id' => 'foo')), '<textarea id="foo"></textarea>', '->renderContentTag() does not add an id if one is given');
$t->is($w->renderContentTag('textarea', '', array('name' => 'foo')), '<textarea name="foo" id="foo"></textarea>', '->renderContentTag() adds an id if none is given and a name is given');
$w->setIdFormat('id_%s');
$t->is($w->renderContentTag('textarea', '', array('name' => 'foo')), '<textarea name="foo" id="id_foo"></textarea>', '->renderContentTag() uses the id_format to generate an id');

// ->generateId()
$t->diag('->generateId()');
$w = new MyWidgetForm();
$w->setIdFormat('id_for_%s_works');
$t->is($w->generateId('foo'), 'id_for_foo_works', '->setIdFormat() sets the format of the widget id');
$t->is($w->generateId('foo[]'), 'id_for_foo_works', '->generateId() removes the [] from the name');
$t->is($w->generateId('foo[bar][]'), 'id_for_foo_bar_works', '->generateId() replaces [] with _');
$t->is($w->generateId('foo[bar][]', 'test'), 'id_for_foo_bar_test_works', '->generateId() takes the value into account if provided');
$t->is($w->generateId('_foo[bar][]', 'test'), 'id_for__foo_bar_test_works', '->generateId() leaves valid ids'); 

$w->setIdFormat('id');
$t->is($w->generateId('foo[bar][]', 'test'), 'foo_bar_test', '->generateId() returns the name if the id format does not contain %s');

$w->setIdFormat('%s');
$t->is($w->generateId('_foo[bar][]', 'test'), 'foo_bar_test', '->generateId() removes invalid characters'); 
$t->is($w->generateId('_foo@bar'), 'foo_bar', '->generateId() removes invalid characters'); 
$t->is($w->generateId('_____foo@bar'), 'foo_bar', '->generateId() removes invalid characters'); 
