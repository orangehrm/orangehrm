<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(40, new lime_output_color());

$w = new sfWidgetFormDate();

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->render()
$t->diag('->render()');

foreach (array(
  '2005-10-15' => array('year' => 2005, 'month' => 10, 'day' => 15),
  time() => array('year' => date('Y'), 'month' => date('m'), 'day' => date('d')),
  'tomorrow' => array('year' => date('Y', time() + 86400), 'month' => date('m', time() + 86400), 'day' => date('d', time() + 86400)),
) as $date => $values)
{
  $dom->loadHTML($w->render('foo', $date));
  $css = new sfDomCssSelector($dom);

  // selected date
  $t->is($css->matchSingle('#foo_year option[value="'.$values['year'].'"][selected="selected"]')->getValue(), $values['year'], '->render() renders a select tag for the year');
  $t->is($css->matchSingle('#foo_month option[value="'.$values['month'].'"][selected="selected"]')->getValue(), $values['month'], '->render() renders a select tag for the month');
  $t->is($css->matchSingle('#foo_day option[value="'.$values['day'].'"][selected="selected"]')->getValue(), $values['day'], '->render() renders a select tag for the day');
}

// pre-epoch date
$t->diag('pre-epoch date');
$years = range(1901, 1920);
$w1 = new sfWidgetFormDate(array('years' => array_combine($years, $years)));
$dom->loadHTML($w1->render('foo', '1910-01-15'));
$css = new sfDomCssSelector($dom);

$t->is($css->matchSingle('#foo_year option[value="1910"][selected="selected"]')->getValue(), 1910, '->render() renders a select tag for the year');
$t->is($css->matchSingle('#foo_month option[value="01"][selected="selected"]')->getValue(), 01, '->render() renders a select tag for the month');
$t->is($css->matchSingle('#foo_day option[value="15"][selected="selected"]')->getValue(), 15, '->render() renders a select tag for the day');

// date as an array
$t->diag('date as an array');
$values = array('year' => 2005, 'month' => 10, 'day' => 15);
$dom->loadHTML($w->render('foo', $values));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_year option[value="'.$values['year'].'"][selected="selected"]')->getValue(), $values['year'], '->render() renders a select tag for the year');
$t->is($css->matchSingle('#foo_month option[value="'.$values['month'].'"][selected="selected"]')->getValue(), $values['month'], '->render() renders a select tag for the month');
$t->is($css->matchSingle('#foo_day option[value="'.$values['day'].'"][selected="selected"]')->getValue(), $values['day'], '->render() renders a select tag for the day');

// invalid date
$t->diag('invalid date');
$dom->loadHTML($w->render('foo', array('year' => null, 'month' => 10)));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_year option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the year');
$t->is($css->matchSingle('#foo_month option[selected="selected"]')->getValue(), 10, '->render() renders a select tag for the month');
$t->is($css->matchSingle('#foo_day option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the day');

$dom->loadHTML($w->render('foo', 'invaliddate'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_year option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the year');
$t->is($css->matchSingle('#foo_month option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the month');
$t->is($css->matchSingle('#foo_day option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the day');

// number of options in each select
$t->diag('number of options in each select');
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_year option')->getNodes()), 12, '->render() renders a select tag for the 10 years around the current one');
$t->is(count($css->matchAll('#foo_month option')->getNodes()), 13, '->render() renders a select tag for the 12 months in a year');
$t->is(count($css->matchAll('#foo_day option')->getNodes()), 32, '->render() renders a select tag for the 31 days in a month');

// can_be_empty option
$t->diag('can_be_empty option');
$w->setOption('can_be_empty', false);
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_year option')->getNodes()), 11, '->render() renders a select tag for the 10 years around the current one');
$t->is(count($css->matchAll('#foo_month option')->getNodes()), 12, '->render() renders a select tag for the 12 months in a year');
$t->is(count($css->matchAll('#foo_day option')->getNodes()), 31, '->render() renders a select tag for the 31 days in a month');
$w->setOption('can_be_empty', true);

// empty_values
$t->diag('empty_values option');
$w->setOption('empty_values', array('year' => 'YEAR', 'month' => 'MONTH', 'day' => 'DAY'));
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_year option')->getNode()->nodeValue, 'YEAR', '->configure() can change the empty values');
$t->is($css->matchSingle('#foo_month option')->getNode()->nodeValue, 'MONTH', '->configure() can change the empty values');
$t->is($css->matchSingle('#foo_day option')->getNode()->nodeValue, 'DAY', '->configure() can change the empty values');
$w->setOption('empty_values', array('year' => '', 'month' => '', 'day' => ''));

// format option
$t->diag('format option');
$t->is($css->matchSingle('#foo_day')->getNode()->nextSibling->nodeValue, '/', '->render() renders 3 selects with a default / as a separator');
$t->like($css->matchSingle('#foo_month')->getNode()->nextSibling->nodeValue, '#^/#', '->render() renders 3 selects with a default / as a separator');

$w->setOption('format', '%month%#%day%#%year%');
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_day')->getNode()->nextSibling->nodeValue, '#', '__construct() can change the default date format');
$t->like($css->matchSingle('#foo_month')->getNode()->nextSibling->nodeValue, '/^#/', '__construct() can change the default date format');

$w->setOption('format', '%day%/%month%/%year%');
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('select')->getNode()->getAttribute('name'), 'foo[day]', '__construct() can change the default date format');

// days / months / years options
$t->diag('days / months / years options');
$w->setOption('years', array(1998 => 1998, 1999 => 1999, 2000 => 2000, 2001 => 2001));
$w->setOption('months', array(1 => 1, 2 => 2, 3 => 3));
$w->setOption('days', array(1 => 1, 2 => 2));
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_year option')->getNodes()), 5, '__construct() can change the default array used for years');
$t->is(count($css->matchAll('#foo_month option')->getNodes()), 4, '__construct() can change the default array used for months');
$t->is(count($css->matchAll('#foo_day option')->getNodes()), 3, '__construct() can change the default array used for days');

// attributes
$t->diag('attributes');
$dom->loadHTML($w->render('foo', '2005-10-15', array('disabled' => 'disabled')));
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 3, '->render() takes the attributes into account for all the three embedded widgets');

$w->setAttribute('disabled', 'disabled');
$dom->loadHTML($w->render('foo', '2005-10-15'));
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 3, '->render() takes the attributes into account for all the three embedded widgets');
