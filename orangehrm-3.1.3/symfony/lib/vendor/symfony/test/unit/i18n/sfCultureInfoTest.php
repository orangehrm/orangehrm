<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(67);

// ->getInstance()
$t->diag('->getInstance()');
$c = sfCultureInfo::getInstance();
$t->is($c->getName(), 'en', '->__construct() returns an object with "en" as the default culture');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getName(), 'fr', '->__construct() takes a culture as its first argument');
$c = sfCultureInfo::getInstance('');
$t->is($c->getName(), 'en', '->__construct() returns an object with "en" as the default culture');

// __toString()
$t->diag('__toString()');
$c = sfCultureInfo::getInstance();
$t->is($c->__toString(), 'en', '->__toString() returns the name of the culture');

try
{
  $c = sfCultureInfo::getInstance('xxx');
  $t->fail('->__construct() throws an exception if the culture is not valid');
}
catch (sfException $e)
{
  $t->pass('->__construct() throws an exception if the culture is not valid');
}

$c_en = sfCultureInfo::getInstance();
$c_fr = sfCultureInfo::getInstance('fr');

// ->getLanguage()
$t->diag('->getLanguage()');
$language_en = $c_en->getLanguage('fr');
$language_fr = $c_fr->getLanguage('fr');
$t->is($language_en, 'French', '->getLanguage() returns the language name for the current culture');
$t->is($language_fr, 'français', '->getLanguage() returns the language name for the current culture');

try
{
  $c_en->getLanguage('gb');
  $t->fail('->getLanguage() throws an Exception if the given language is invalid.');
}
catch (Exception $e)
{
  $t->pass('->getLanguage() throws an Exception if the given language is invalid.');
}

// ->getCurrency()
$t->diag('->getCurrency()');
$currency_en = $c_en->getCurrency('EUR');
$currency_fr = $c_fr->getCurrency('EUR');
$t->is($currency_en, 'Euro', '->getCurrency() returns the currency name for the current culture');
$t->is($currency_fr, 'euro', '->getCurrency() returns the currency name for the current culture');

try
{
  $c_en->getCurrency('FRANCS');
  $t->fail('->getCurrency() throws an Exception if the given currency is invalid.');
}
catch (Exception $e)
{
  $t->pass('->getCurrency() throws an Exception if the given currency is invalid.');
}

// ->getCountry()
$t->diag('->getCountry()');
$country_en = $c_en->getCountry('FR');
$country_fr = $c_fr->getCountry('FR');
$t->is($country_en, 'France', '->getCountry() returns the country name for the current culture');
$t->is($country_fr, 'France', '->getCountry() returns the country name for the current culture');

try
{
  $c_en->getCountry('en');
  $t->fail('->getCountry() throws an Exception if the given country is invalid.');
}
catch (Exception $e)
{
  $t->pass('->getCountry() throws an Exception if the given country is invalid.');
}

// ->getLanguages()
$t->diag('->getLanguages()');
$languages_en = $c_en->getLanguages();
$languages_fr = $c_fr->getLanguages();
$t->is($languages_en['fr'], 'French', '->getLanguages() returns a list of languages in the language of the localized version');
$t->is($languages_fr['fr'], 'français', '->getLanguages() returns a list of languages in the language of the localized version');
$t->is($languages_en, $c_en->Languages, '->getLanguages() is equivalent to ->Languages');

$languages = $c_en->getLanguages(array('fr', 'es'));
$t->is(array_keys($languages), array('fr', 'es'), '->getLanguages() takes an array of languages as its first argument');

try
{
  $c_en->getLanguages(array('fr', 'gb'));
  $t->fail('->getLanguages() throws an Exception if the list of given languages contains some invalid ones.');
}
catch (Exception $e)
{
  $t->pass('->getLanguages() throws an Exception if the list of given languages contains some invalid ones.');
}

// ->getCurrencies()
$t->diag('->getCurrencies()');
$currencies_en = $c_en->getCurrencies();
$currencies_fr = $c_fr->getCurrencies();
$t->is($currencies_en['EUR'], 'Euro', '->getCurrencies() returns a list of currencies in the language of the localized version');
$t->is($currencies_fr['EUR'], 'euro', '->getCurrencies() returns a list of currencies in the language of the localized version');
$t->is($currencies_en, $c_en->Currencies, '->getCurrencies() is equivalent to ->Currencies');

$currencies = $c_en->getCurrencies(array('USD', 'EUR'));
$t->is(array_keys($currencies), array('EUR', 'USD'), '->getCurrencies() takes an array of currencies as its first argument');

try
{
  $c_en->getCurrencies(array('USD', 'FRANCS'));
  $t->fail('->getCurrencies() throws an Exception if the list of given currencies contains some invalid ones.');
}
catch (Exception $e)
{
  $t->pass('->getCurrencies() throws an Exception if the list of given currencies contains some invalid ones.');
}

// ->getCountries()
$t->diag('->getCountries()');
$countries_en = $c_en->getCountries();
$countries_fr = $c_fr->getCountries();
$t->is($countries_en['ES'], 'Spain', '->getCountries() returns a list of countries in the language of the localized version');
$t->is($countries_fr['ES'], 'Espagne', '->getCountries() returns a list of countries in the language of the localized version');
$t->is($countries_en, $c_en->Countries, '->getCountries() is equivalent to ->Countries');

$countries = $c_en->getCountries(array('FR', 'ES'));
$t->is(array_keys($countries), array('FR', 'ES'), '->getCountries() takes an array of countries as its first argument');

try
{
  $c_en->getCountries(array('FR', 'EN'));
  $t->fail('->getCountries() throws an Exception if the list of given countries contains some invalid ones.');
}
catch (Exception $e)
{
  $t->pass('->getCountries() throws an Exception if the list of given countries contains some invalid ones.');
}

// ->getScripts()
$t->diag('->getScripts()');
$scripts_en = $c_en->getScripts();
$scripts_fr = $c_fr->getScripts();
$t->is($scripts_en['Arab'], 'Arabic', '->getScripts() returns a list of scripts in the language of the localized version');
$t->is($scripts_fr['Arab'], 'arabe', '->getScripts() returns a list of scripts in the language of the localized version');
$t->is($scripts_en, $c_en->Scripts, '->getScripts() is equivalent to ->Scripts');

// ->getTimeZones()
$t->diag('->getTimeZones()');
$time_zones_en = $c_en->getTimeZones();
$time_zones_fr = $c_fr->getTimeZones();

$t->is($time_zones_en['America/Juneau']['ld'], 'Alaska Daylight Time', '->getTimeZones() returns a list of time zones in the language of the localized version');
$t->is($time_zones_fr['America/Juneau']['ld'], 'heure avancée de l’Alaska', '->getTimeZones() returns a list of time zones in the language of the localized version');
$t->is($time_zones_en, $c_en->TimeZones, '->getTimeZones() is equivalent to ->TimeZones');

// ->validCulture()
$t->diag('->validCulture()');
$t->is($c->validCulture('fr'), true, '->validCulture() returns true if the culture is valid');
$t->is($c->validCulture('fr_FR'), true, '->validCulture() returns true if the culture is valid');
foreach (array('xxx', 'pp', 'frFR') as $culture)
{
  $t->is($c->validCulture($culture), false, '->validCulture() returns false if the culture does not exist');
}

// ::getCultures()
$t->diag('::getCultures()');
$cultures = sfCultureInfo::getCultures();
$t->is(in_array('fr', $cultures), true, '::getCultures() returns an array of all available cultures');
$t->is(in_array('fr_FR', $cultures), true, '::getCultures() returns an array of all available cultures');

$cultures = sfCultureInfo::getCultures(sfCultureInfo::NEUTRAL);
$t->is(in_array('fr', $cultures), true, '::getCultures() returns an array of all available cultures');
$t->is(in_array('fr_FR', $cultures), false, '::getCultures() returns an array of all available cultures');

$cultures = sfCultureInfo::getCultures(sfCultureInfo::SPECIFIC);
$t->is(in_array('fr', $cultures), false, '::getCultures() returns an array of all available cultures');
$t->is(in_array('fr_FR', $cultures), true, '::getCultures() returns an array of all available cultures');

// ->getParent()
$t->diag('->getParent()');
$c = sfCultureInfo::getInstance('fr_FR');
$t->isa_ok($c->getParent(), 'sfCultureInfo', '->getParent() returns a sfCultureInfo instance');
$t->is($c->getParent()->getName(), 'fr', '->getParent() returns the parent culture');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getParent()->getName(), 'en', '->getParent() returns the invariant culture if the culture is neutral');

// ->getIsNeutralCulture()
$t->diag('->getIsNeutralCulture()');
$c = sfCultureInfo::getInstance('fr_FR');
$t->is($c->getIsNeutralCulture(), false, '->getIsNeutralCulture() returns false if the culture is specific');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getIsNeutralCulture(), true, '->getIsNeutralCulture() returns true if the culture is neutral');

// ->getEnglishName()
$t->diag('->getEnglishName()');
$c = sfCultureInfo::getInstance('fr_FR');
$t->is($c->getEnglishName(), 'French (France)', '->getEnglishName() returns the english name of the current culture');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getEnglishName(), 'French', '->getEnglishName() returns the english name of the current culture');
$t->is($c->getEnglishName(), $c->EnglishName, '->getEnglishName() is equivalent to ->EnglishName');

// ->getNativeName()
$t->diag('->getNativeName()');
$c = sfCultureInfo::getInstance('fr_FR');
$t->is($c->getNativeName(), 'français (France)', '->getNativeName() returns the native name of the current culture');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getNativeName(), 'français', '->getNativeName() returns the native name of the current culture');
$t->is($c->getNativeName(), $c->NativeName, '->getNativeName() is equivalent to ->NativeName');

// ->getCalendar()
$t->diag('->getCalendar()');
$c = sfCultureInfo::getInstance('fr');
$t->is($c->getCalendar(), 'gregorian', '->getCalendar() returns the default calendar');
$t->is($c->getCalendar(), $c->Calendar, '->getCalendar() is equivalent to ->Calendar');

// __get()
$t->diag('__get()');
try
{
  $c->NonExistant;
  $t->fail('__get() throws an exception if the property does not exist');
}
catch (sfException $e)
{
  $t->pass('__get() throws an exception if the property does not exist');
}

// __set()
$t->diag('__set()');
try
{
  $c->NonExistant = 12;
  $t->fail('__set() throws an exception if the property does not exist');
}
catch (sfException $e)
{
  $t->pass('__set() throws an exception if the property does not exist');
}

// ->getDateTimeFormat()
$t->diag('->getDateTimeFormat()');
$c = sfCultureInfo::getInstance();
$t->isa_ok($c->getDateTimeFormat(), 'sfDateTimeFormatInfo', '->getDateTimeFormat() returns a sfDateTimeFormatInfo instance');

// ->setDateTimeFormat()
$t->diag('->setDateTimeFormat()');
$d = $c->getDateTimeFormat();
$c->setDateTimeFormat('yyyy');
$t->is($c->getDateTimeFormat(), 'yyyy', '->setDateTimeFormat() sets the sfDateTimeFormatInfo instance');
$c->DateTimeFormat = 'mm';
$t->is($c->getDateTimeFormat(), 'mm', '->setDateTimeFormat() is equivalent to ->DateTimeFormat = ');

// ->getNumberFormat()
$t->diag('->getNumberFormat()');
$c = sfCultureInfo::getInstance();
$t->isa_ok($c->getNumberFormat(), 'sfNumberFormatInfo', '->getNumberFormat() returns a sfNumberFormatInfo instance');

// ->setNumberFormat()
$t->diag('->setNumberFormat()');
$d = $c->getNumberFormat();
$c->setNumberFormat('.');
$t->is($c->getNumberFormat(), '.', '->setNumberFormat() sets the sfNumberFormatInfo instance');
$c->NumberFormat = '#';
$t->is($c->getNumberFormat(), '#', '->setNumberFormat() is equivalent to ->NumberFormat = ');
