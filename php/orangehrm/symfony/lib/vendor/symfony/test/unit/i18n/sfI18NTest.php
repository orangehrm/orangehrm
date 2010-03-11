<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(28, new lime_output_color());

class ProjectConfiguration extends sfProjectConfiguration
{
}

class TestConfiguration extends sfApplicationConfiguration
{
  public function getI18NGlobalDirs()
  {
    return array(dirname(__FILE__).'/fixtures');
  }
}

$configuration = new TestConfiguration('test', true, sfConfig::get('sf_test_cache_dir', sfToolkit::getTmpDir()));
$dispatcher = $configuration->getEventDispatcher();
$cache = new sfNoCache();

// ->initialize()
$t->diag('->initialize()');
$i18n = new sfI18N($configuration, $cache);
$dispatcher->notify(new sfEvent(null, 'user.change_culture', array('culture' => 'fr')));
$t->is($i18n->getCulture(), 'fr', '->initialize() connects to the user.change_culture event');

// ->getCulture() ->setCulture()
$t->diag('->getCulture() ->setCulture()');
$i18n = new sfI18N($configuration, $cache);
$t->is($i18n->getCulture(), 'en', '->getCulture() returns the current culture');
$i18n->setCulture('fr');
$t->is($i18n->getCulture(), 'fr', '->setCulture() sets the current culture');

// ->__()
$t->diag('->__()');
sfConfig::set('sf_charset', 'UTF-8');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->__('an english sentence'), 'une phrase en français', '->__() translates a string');
$args = array('%timestamp%' => $timestamp = time());
$t->is($i18n->__('Current timestamp is %timestamp%', $args), strtr('Le timestamp courant est %timestamp%', $args), '->__() takes an array of arguments as its second argument');
$t->is($i18n->__('an english sentence', array(), 'messages_bis'), 'une phrase en français (bis)', '->__() takes a catalogue as its third argument');

// test for #2161
$t->is($i18n->__('1 minute'), '1 menit', '->__() "1 minute" translated as "1 menit"');
$t->is($i18n->__('1'), '1', '->__() "1" translated as "1"');
$t->is($i18n->__(1), '1', '->__() number 1 translated as "1"');

$i18n->setCulture('fr_BE');
$t->is($i18n->__('an english sentence'), 'une phrase en belge', '->__() translates a string');

// debug
$i18n = new sfI18N($configuration, $cache, array('debug' => true));
$t->is($i18n->__('unknown'), '[T]unknown[/T]', '->__() adds a prefix and a suffix on untranslated strings if debug is on');
$i18n = new sfI18N($configuration, $cache, array('debug' => true, 'untranslated_prefix' => '-', 'untranslated_suffix' => '#'));
$t->is($i18n->__('unknown'), '-unknown#', '->initialize() can change the default prefix and suffix dor untranslated strings');

// ->getCountry()
$t->diag('->getCountry()');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->getCountry('FR'), 'France', '->getCountry() returns the name of a country for the current culture');
$t->is($i18n->getCountry('FR', 'es'), 'Francia', '->getCountry() takes an optional culture as its second argument');

// ->getNativeName()
$t->diag('->getNativeName()');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->getNativeName('fr'), 'français', '->getNativeName() returns the name of a culture');

// ->getTimestampForCulture()
$t->diag('->getTimestampForCulture()');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->getTimestampForCulture('15/10/2005'), mktime(0, 0, 0, '10', '15', '2005'), '->getTimestampForCulture() returns the timestamp for a data formatted in the current culture');
$t->is($i18n->getTimestampForCulture('15/10/2005 15:33'), mktime(15, 33, 0, '10', '15', '2005'), '->getTimestampForCulture() returns the timestamp for a data formatted in the current culture');
$t->is($i18n->getTimestampForCulture('10/15/2005', 'en_US'), mktime(0, 0, 0, '10', '15', '2005'), '->getTimestampForCulture() can take a culture as its second argument');
$t->is($i18n->getTimestampForCulture('10/15/2005 15:33', 'en_US'), mktime(15, 33, 0, '10', '15', '2005'), '->getTimestampForCulture() can take a culture as its second argument');
$t->is($i18n->getTimestampForCulture('not a date'), null, '->getTimestampForCulture() returns the day, month and year for a data formatted in the current culture');

// ->getDateForCulture()
$t->diag('->getDateForCulture()');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->getDateForCulture('15/10/2005'), array('15', '10', '2005'), '->getDateForCulture() returns the day, month and year for a data formatted in the current culture');
$t->is($i18n->getDateForCulture('10/15/2005', 'en_US'), array('15', '10', '2005'), '->getDateForCulture() can take a culture as its second argument');
$t->is($i18n->getDateForCulture(null), null, '->getDateForCulture() returns null in case of conversion problem');
$t->is($i18n->getDateForCulture('not a date'), null, '->getDateForCulture() returns null in case of conversion problem');

// ->getTimeForCulture()
$t->diag('->getTimeForCulture()');
$i18n = new sfI18N($configuration, $cache, array('culture' => 'fr'));
$t->is($i18n->getTimeForCulture('15:33'), array('15', '33'), '->getTimeForCulture() returns the hour and minuter for a time formatted in the current culture');
$t->is($i18n->getTimeForCulture('15:33', 'en_US'), array('15', '33'), '->getTimeForCulture() can take a culture as its second argument');
$t->is($i18n->getTimeForCulture(null), null, '->getTimeForCulture() returns null in case of conversion problem');
$t->is($i18n->getTimeForCulture('not a time'), null, '->getTimeForCulture() returns null in case of conversion problem');
