<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(3);

class sfMessageSource_Simple extends sfMessageSource
{
  function __construct($source) {}
  function delete($message, $catalogue = 'messages') {}
  function update($text, $target, $comments, $catalogue = 'messages') {}
  function catalogues() {}
  function save($catalogue = 'messages') {}
  function getId() {}
}

// ::factory()
$t->diag('::factory()');
$source = sfMessageSource::factory('Simple');
$t->ok($source instanceof sfIMessageSource, '::factory() returns a sfMessageSource instance');

// ->getCulture() ->setCulture()
$t->diag('->getCulture() ->setCulture()');
$source->setCulture('en');
$t->is($source->getCulture(), 'en', '->setCulture() changes the source culture');
$source->setCulture('fr');
$t->is($source->getCulture(), 'fr', '->getCulture() gets the current source culture');
