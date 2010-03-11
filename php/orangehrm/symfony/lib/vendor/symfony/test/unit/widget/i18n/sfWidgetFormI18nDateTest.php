<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(7, new lime_output_color());

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->configure()
$t->diag('->configure()');

$w = new sfWidgetFormI18nDate(array('culture' => 'fr'));
$t->is($w->getOption('format'), '%day%/%month%/%year%', '->configure() automatically changes the date format for the given culture');
$w = new sfWidgetFormI18nDate(array('culture' => 'en_US'));
$t->is($w->getOption('format'), '%month%/%day%/%year%', '->configure() automatically changes the date format for the given culture');
$w = new sfWidgetFormI18nDate(array('culture' => 'sr'));
$t->is($w->getOption('format'), '%day%.%month%.%year%.', '->configure() automatically changes the date format for the given culture');

$w = new sfWidgetFormI18nDate(array('culture' => 'fr', 'month_format' => 'name'));
$months = $w->getOption('months');
$t->is($months[2], 'février', '->configure() automatically changes the date format for the given culture');

$w = new sfWidgetFormI18nDate(array('culture' => 'fr', 'month_format' => 'short_name'));
$months = $w->getOption('months');
$t->is($months[2], 'févr.', '->configure() automatically changes the date format for the given culture');

$w = new sfWidgetFormI18nDate(array('culture' => 'fr', 'month_format' => 'number'));
$months = $w->getOption('months');
$t->is($months[2], 2, '->configure() automatically changes the date format for the given culture');

try
{
  new sfWidgetFormI18nDate(array('culture' => 'fr', 'month_format' => 'nonexistant'));
  $t->fail('->configure() throws an InvalidArgumentException if the month_format type does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->configure() throws an InvalidArgumentException if the month_format type does not exist');
}
