<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(7);

$w = new sfWidgetFormInputCheckbox();

// ->render()
$t->diag('->render()');
$t->is($w->render('foo', 1), '<input type="checkbox" name="foo" checked="checked" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', null), '<input type="checkbox" name="foo" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', false), '<input type="checkbox" name="foo" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', 0, array('value' => '0')), '<input type="checkbox" name="foo" value="0" checked="checked" id="foo" />', '->render() renders the widget as HTML');

$w = new sfWidgetFormInputCheckbox(array(), array('value' => 'bar'));
$t->is($w->render('foo', null), '<input value="bar" type="checkbox" name="foo" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', null, array('value' => 'baz')), '<input value="baz" type="checkbox" name="foo" id="foo" />', '->render() renders the widget as HTML');
$t->is($w->render('foo', 'bar'), '<input value="bar" type="checkbox" name="foo" checked="checked" id="foo" />', '->render() renders the widget as HTML');

