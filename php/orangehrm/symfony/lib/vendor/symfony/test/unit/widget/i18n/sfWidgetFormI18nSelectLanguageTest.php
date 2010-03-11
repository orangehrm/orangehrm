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
  new sfWidgetFormI18nSelectLanguage(array('culture' => 'en', 'languages' => array('xx')));
  $t->fail('->configure() throws an InvalidArgumentException if a language does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->configure() throws an InvalidArgumentException if a language does not exist');
}

$v = new sfWidgetFormI18nSelectLanguage(array('culture' => 'en', 'languages' => array('fr', 'en')));
$t->is(array_keys($v->getOption('choices')), array('en', 'fr'), '->configure() can restrict the number of languages with the languages option');

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormI18nSelectLanguage(array('culture' => 'fr'));
$dom->loadHTML($w->render('language', 'en'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#language option[value="en"]')->getValue(), 'anglais', '->render() renders all languages as option tags');
$t->is(count($css->matchAll('#language option[value="en"][selected="selected"]')->getNodes()), 1, '->render() renders all languages as option tags');

// add_empty
$t->diag('add_empty');
$w = new sfWidgetFormI18nSelectLanguage(array('culture' => 'fr', 'add_empty' => true));
$dom->loadHTML($w->render('language', 'FR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#language option[value=""]')->getValue(), '', '->render() renders an empty option if add_empty is true');

$w = new sfWidgetFormI18nSelectLanguage(array('culture' => 'fr', 'add_empty' => 'foo'));
$dom->loadHTML($w->render('language', 'FR'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#language option[value=""]')->getValue(), 'foo', '->render() renders an empty option if add_empty is true');
