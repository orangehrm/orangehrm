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

$w = new sfWidgetFormTime(array('with_seconds' => true));

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->render()
$t->diag('->render()');

foreach (array(
  '12:30:35',
  mktime(12, 30, 35, 15, 10, 2005),
) as $date)
{
  $dom->loadHTML($w->render('foo', $date));
  $css = new sfDomCssSelector($dom);

  // selected date
  $t->is($css->matchSingle('#foo_hour option[value="12"][selected="selected"]')->getValue(), 12, '->render() renders a select tag for the hour');
  $t->is($css->matchSingle('#foo_minute option[value="30"][selected="selected"]')->getValue(), 30, '->render() renders a select tag for the minute');
  $t->is($css->matchSingle('#foo_second option[value="35"][selected="selected"]')->getValue(), 35, '->render() renders a select tag for the second');
}

// time as an array
$t->diag('time as an array');
$dom->loadHTML($w->render('foo', array('hour' => 12, 'minute' => '30', 'second' => 35)));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_hour option[value="12"][selected="selected"]')->getValue(), 12, '->render() renders a select tag for the hour');
$t->is($css->matchSingle('#foo_minute option[value="30"][selected="selected"]')->getValue(), 30, '->render() renders a select tag for the minute');
$t->is($css->matchSingle('#foo_second option[value="35"][selected="selected"]')->getValue(), 35, '->render() renders a select tag for the second');

// time as an array - single digits
$t->diag('time as an array - single digits');
$dom->loadHTML($w->render('foo', '01:03:05'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_hour option[value="1"][selected="selected"]')->getValue(), 1, '->render() renders a select tag for the hour');
$t->is($css->matchSingle('#foo_minute option[value="3"][selected="selected"]')->getValue(), 3, '->render() renders a select tag for the minute');
$t->is($css->matchSingle('#foo_second option[value="5"][selected="selected"]')->getValue(), 5, '->render() renders a select tag for the second');

// invalid time
$t->diag('time as an array');
$dom->loadHTML($w->render('foo', array('hour' => null, 'minute' => 30)));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_hour option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the hour');
$t->is($css->matchSingle('#foo_minute option[selected="selected"]')->getValue(), 30, '->render() renders a select tag for the minute');
$t->is($css->matchSingle('#foo_second option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the second');

$dom->loadHTML($w->render('foo', 'invalidtime'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_hour option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the hour');
$t->is($css->matchSingle('#foo_minute option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the minute');
$t->is($css->matchSingle('#foo_second option[selected="selected"]')->getValue(), '', '->render() renders a select tag for the second');

// number of options in each select
$t->diag('number of options in each select');
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_hour option')->getNodes()), 25, '->render() renders a select tag for the 24 hours in a day');
$t->is(count($css->matchAll('#foo_minute option')->getNodes()), 61, '->render() renders a select tag for the 60 minutes in an hour');
$t->is(count($css->matchAll('#foo_second option')->getNodes()), 61, '->render() renders a select tag for the 60 seconds in a minute');

// can_be_empty option
$t->diag('can_be_empty option');
$w->setOption('can_be_empty', false);
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_hour option')->getNodes()), 24, '->render() renders a select tag for the 24 hours around in a day');
$t->is(count($css->matchAll('#foo_minute option')->getNodes()), 60, '->render() renders a select tag for the 60 minutes in an hour');
$t->is(count($css->matchAll('#foo_second option')->getNodes()), 60, '->render() renders a select tag for the 60 seconds in a minute');
$w->setOption('can_be_empty', true);

// empty_values
$t->diag('empty_values option');
$w->setOption('empty_values', array('hour' => 'HOUR', 'minute' => 'MINUTE', 'second' => 'SECOND'));
$dom->loadHTML($w->render('foo', '2005-10-15'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo_hour option')->getNode()->nodeValue, 'HOUR', '->configure() can change the empty values');
$t->is($css->matchSingle('#foo_minute option')->getNode()->nodeValue, 'MINUTE', '->configure() can change the empty values');
$t->is($css->matchSingle('#foo_second option')->getNode()->nodeValue, 'SECOND', '->configure() can change the empty values');
$w->setOption('empty_values', array('hour' => '', 'minute' => '', 'second' => ''));

// format option
$t->diag('format option');
$t->like($css->matchSingle('#foo_hour')->getNode()->nextSibling->nodeValue, '/^:/', '->render() renders 3 selects with a default : as a separator');
$t->is($css->matchSingle('#foo_minute')->getNode()->nextSibling->nodeValue, ':', '->render() renders 3 selects with a default : as a separator');

$w->setOption('format', '%hour%#%minute%#%second%');
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->like($css->matchSingle('#foo_hour')->getNode()->nextSibling->nodeValue, '/^#/', '__construct() can change the default format');
$t->is($css->matchSingle('#foo_minute')->getNode()->nextSibling->nodeValue, '#', '__construct() can change the default format');

$w->setOption('format', '%minute%#%hour%#%second%');
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('select')->getNode()->getAttribute('name'), 'foo[minute]', '__construct() can change the default time format');

// hours / minutes / seconds options
$t->diag('hours / minutes / seconds options');
$w->setOption('hours', array(1 => 1, 2 => 2, 3 => 3, 4 => 4));
$w->setOption('minutes', array(1 => 1, 2 => 2));
$w->setOption('seconds', array(15 => 15, 30 => 30, 45 => 45));
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_hour option')->getNodes()), 5, '__construct() can change the default array used for hours');
$t->is(count($css->matchAll('#foo_minute option')->getNodes()), 3, '__construct() can change the default array used for minutes');
$t->is(count($css->matchAll('#foo_second option')->getNodes()), 4, '__construct() can change the default array used for seconds');

// with_seconds option
$t->diag('with_seconds option');
$w->setOption('with_seconds', false);
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo_second option')->getNodes()), 0, '__construct() can enable or disable the seconds select box with the with_seconds option');

$w->setOption('format_without_seconds', '%hour%#%minute%');
$dom->loadHTML($w->render('foo', '12:30:35'));
$css = new sfDomCssSelector($dom);
$t->like($css->matchSingle('#foo_hour')->getNode()->nextSibling->nodeValue, '/^#/', '__construct() can change the default format');
$t->ok(!count($css->matchSingle('#foo_second')->getNodes()), '__construct() can change the default format');

// attributes
$t->diag('attributes');
$w->setOption('with_seconds', true);
$dom->loadHTML($w->render('foo', '12:30:35', array('disabled' => 'disabled')));
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 3, '->render() takes the attributes into account for all the three embedded widgets');

$w->setAttribute('disabled', 'disabled');
$dom->loadHTML($w->render('foo', '12:30:35'));
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 3, '->render() takes the attributes into account for all the three embedded widgets');
