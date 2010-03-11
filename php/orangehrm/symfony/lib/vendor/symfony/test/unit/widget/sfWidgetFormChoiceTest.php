<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(14, new lime_output_color());

// ->getRenderer()
$t->diag('->getRenderer()');
$w = new sfWidgetFormChoice(array('choices' => array()));
$t->is(get_class($w->getRenderer()), 'sfWidgetFormSelect', '->getRenderer() guesses the renderer class to use');
$w->setOption('multiple', true);
$t->is(get_class($w->getRenderer()), 'sfWidgetFormSelect', '->getRenderer() guesses the renderer class to use');
$w->setOption('expanded', true);
$t->is(get_class($w->getRenderer()), 'sfWidgetFormSelectCheckbox', '->getRenderer() guesses the renderer class to use');
$w->setOption('multiple', false);
$t->is(get_class($w->getRenderer()), 'sfWidgetFormSelectRadio', '->getRenderer() guesses the renderer class to use');

class MyWidget extends sfWidgetFormChoice
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return null;
  }

  public function getJavaScripts()
  {
    return array('/path/to/a/file.js');
  }

  public function getStylesheets()
  {
    return array('/path/to/a/file.css' => 'all');
  }
}

$w->setOption('renderer_class', 'MyWidget');
$t->is(get_class($w->getRenderer()), 'MyWidget', '->getRenderer() uses the renderer_class as the widget class if provided');

$w->setOption('renderer_class', null);
$w->setOption('renderer', new MyWidget(array('choices' => array())));
$t->is(get_class($w->getRenderer()), 'MyWidget', '->getRenderer() uses the renderer as the widget if provided');

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormChoice(array('choices' => array()));
$t->like($w->render('foo'), '/<select name="foo" id="foo">/', '->render() renders a select tag by default');
$w->setOption('multiple', true);
$t->like($w->render('foo'), '/<select name="foo\[\]" multiple="multiple" id="foo">/', '->render() adds a multiple attribute for multiple selects');
$w->setOption('expanded', true);
$t->like($w->render('foo'), '/<ul class="checkbox_list">/', '->render() uses a checkbox list when expanded and multiple are true');
$w->setOption('multiple', false);
$t->like($w->render('foo'), '/<ul class="radio_list">/', '->render() uses a checkbox list when expanded is true and multiple is false');

// ->getJavaScripts() ->getStylesheets()
$t->diag('->getJavaScripts() ->getStylesheets()');
$w = new sfWidgetFormChoice(array('choices' => array()));
$w->setOption('renderer_class', 'MyWidget');
$t->is($w->getJavaScripts(), array('/path/to/a/file.js'), '->getJavaScripts() returns the stylesheets of the renderer widget');
$t->is($w->getStylesheets(), array('/path/to/a/file.css' => 'all'), '->getStylesheets() returns the JavaScripts of the renderer widget');

// __clone()
$t->diag('__clone()');
$w = new sfWidgetFormChoice(array('choices' => new sfCallable(array($w, 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($w1), '__clone() changes the choices is a callable and the object is an instance of the current object');

$w = new sfWidgetFormChoice(array('choices' => new sfCallable(array($a = new stdClass(), 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($a), '__clone() changes nothing if the choices is a callable and the object is not an instance of the current object');
