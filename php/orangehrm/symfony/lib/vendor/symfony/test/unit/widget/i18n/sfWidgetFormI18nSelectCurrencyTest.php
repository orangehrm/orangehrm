<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(6, new lime_output_color());

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->configure()
$t->diag('->configure()');
try
{
  new sfWidgetFormI18nSelectCurrency(array('culture' => 'en', 'currencies' => array('xx')));
  $t->fail('->configure() throws an InvalidArgumentException if a currency does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->configure() throws an InvalidArgumentException if a currency does not exist');
}

$v = new sfWidgetFormI18nSelectCurrency(array('culture' => 'en', 'currencies' => array('EUR', 'USD')));
$t->is(array_keys($v->getOption('choices')), array('USD', 'EUR'), '->configure() can restrict the number of currencies with the currencies option');

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormI18nSelectCurrency(array('culture' => 'fr'));
$dom->loadHTML($w->render('currency', 'EUR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#currency option[value="EUR"]')->getValue(), 'euro', '->render() renders all currencies as option tags');
$t->is(count($css->matchAll('#currency option[value="EUR"][selected="selected"]')->getNodes()), 1, '->render() renders all currencies as option tags');

// add_empty
$t->diag('add_empty');
$w = new sfWidgetFormI18nSelectCurrency(array('culture' => 'fr', 'add_empty' => true));
$dom->loadHTML($w->render('currency', 'EUR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#currency option[value=""]')->getValue(), '', '->render() renders an empty option if add_empty is true');

$w = new sfWidgetFormI18nSelectCurrency(array('culture' => 'fr', 'add_empty' => 'foo'));
$dom->loadHTML($w->render('currency', 'EUR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#currency option[value=""]')->getValue(), 'foo', '->render() renders an empty option if add_empty is true');
