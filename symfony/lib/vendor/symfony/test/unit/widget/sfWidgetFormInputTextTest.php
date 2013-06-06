<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(5);

$w = new sfWidgetFormInputText();

// ->render()
$t->diag('->render()');
$t->is($w->render('foo'), '<input type="text" name="foo" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', 'bar'), '<input type="text" name="foo" value="bar" id="foo" />', '->render() can take a value for the input');
$t->is($w->render('foo', '', array('type' => 'password', 'class' => 'foobar')), '<input type="password" name="foo" value="" class="foobar" id="foo" />', '->render() can take HTML attributes as its third argument');

$w = new sfWidgetFormInputText(array(), array('class' => 'foobar'));
$t->is($w->render('foo'), '<input class="foobar" type="text" name="foo" id="foo" />', '__construct() can take default HTML attributes');
$t->is($w->render('foo', null, array('class' => 'barfoo')), '<input class="barfoo" type="text" name="foo" id="foo" />', '->render() can override default attributes');
