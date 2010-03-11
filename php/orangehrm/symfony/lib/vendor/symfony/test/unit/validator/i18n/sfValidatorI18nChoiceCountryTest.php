<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(3, new lime_output_color());

// ->configure()
$t->diag('->configure()');

try
{
  new sfValidatorI18nChoiceCountry(array('countries' => array('EN')));
  $t->fail('->configure() throws an InvalidArgumentException if a country does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->configure() throws an InvalidArgumentException if a country does not exist');
}

$v = new sfValidatorI18nChoiceCountry(array('countries' => array('FR', 'GB')));
$t->is($v->getOption('choices'), array('FR', 'GB'), '->configure() can restrict the number of countries with the countries option');

// ->clean()
$t->diag('->clean()');
$v = new sfValidatorI18nChoiceCountry(array('countries' => array('FR', 'GB')));
$t->is($v->clean('FR'), 'FR', '->clean() cleans the input value');
