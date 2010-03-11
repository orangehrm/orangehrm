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
  new sfWidgetFormI18nSelectCountry(array('culture' => 'en', 'countries' => array('EN')));
  $t->fail('->configure() throws an InvalidArgumentException if a country does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->configure() throws an InvalidArgumentException if a country does not exist');
}

$v = new sfWidgetFormI18nSelectCountry(array('culture' => 'en', 'countries' => array('FR', 'GB')));
$t->is(array_keys($v->getOption('choices')), array('FR', 'GB'), '->configure() can restrict the number of countries with the countries option');

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormI18nSelectCountry(array('culture' => 'fr'));
$dom->loadHTML($w->render('country', 'FR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#country option[value="FR"]')->getValue(), 'France', '->render() renders all countries as option tags');
$t->is(count($css->matchAll('#country option[value="FR"][selected="selected"]')->getNodes()), 1, '->render() renders all countries as option tags');

// add_empty
$t->diag('add_empty');
$w = new sfWidgetFormI18nSelectCountry(array('culture' => 'fr', 'add_empty' => true));
$dom->loadHTML($w->render('country', 'FR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#country option[value=""]')->getValue(), '', '->render() renders an empty option if add_empty is true');

$w = new sfWidgetFormI18nSelectCountry(array('culture' => 'fr', 'add_empty' => 'foo'));
$dom->loadHTML($w->render('country', 'FR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#country option[value=""]')->getValue(), 'foo', '->render() renders an empty option if add_empty is true');
