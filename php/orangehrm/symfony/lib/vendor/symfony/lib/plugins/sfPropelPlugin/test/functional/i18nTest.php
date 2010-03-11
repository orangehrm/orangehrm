<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

class myBrowser extends sfBrowser
{
  public function getContext($forceReload = false)
  {
    parent::getContext($forceReload);

    sfPropel::initialize($this->context->getEventDispatcher());

    return $this->context;
  }
}

$b = new sfTestBrowser(new myBrowser());
$b->setTester('propel', 'sfTesterPropel');

// en
$b->
  get('/i18n/default')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'default')->
  checkResponseElement('#movies .default:first', '')->
  checkResponseElement('#movies .it:first', 'La Vita è bella')->
  checkResponseElement('#movies .fr:first', 'La Vie est belle')
;

// fr
$b->
  get('/i18n/index')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'index')->
  checkResponseElement('#movies .default:first', 'La Vie est belle')->
  checkResponseElement('#movies .it:first', 'La Vita è bella')->
  checkResponseElement('#movies .fr:first', 'La Vie est belle')
;

// still fr
$b->
  get('/i18n/default')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'default')->
  checkResponseElement('#movies .default:first', 'La Vie est belle')->
  checkResponseElement('#movies .it:first', 'La Vita è bella')->
  checkResponseElement('#movies .fr:first', 'La Vie est belle')
;

// i18n forms
$b->
  get('/i18n/movie')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'movie')->
  end()->
  isStatusCode(200)->
  click('submit', array('movie' => array('director' => 'Robert Aldrich', 'en' => array('title' => 'The Dirty Dozen'), 'fr' => array('title' => 'Les Douze Salopards'))))->
  isRedirected()->

  followRedirect()->
  with('response')->begin()->
    checkElement('input[value="Robert Aldrich"]')->
    checkElement('input[value="The Dirty Dozen"]')->
    checkElement('input[value="Les Douze Salopards"]')->
    checkElement('#movie_fr_id', false)->
    checkElement('#movie_fr_culture', false)->
  end()->

  with('propel')->begin()->
    check('Movie', array(), 2)->
    check('Movie', array('director' => 'Robert Aldrich', 'id' => 2))->
    check('MovieI18N', array(), 4)->
    check('MovieI18N', array('id' => 2), 2)->
    check('MovieI18N', array('culture' => 'fr', 'id' => 2, 'title' => 'Les Douze Salopards'))->
    check('MovieI18N', array('culture' => 'en', 'id' => 2, 'title' => 'The Dirty Dozen'))->
  end()->

  click('submit', array('movie' => array('director' => 'Robert Aldrich (1)', 'en' => array('title' => 'The Dirty Dozen (1)'), 'fr' => array('title' => 'Les Douze Salopards (1)'))))->
  isRedirected()->

  followRedirect()->
  with('response')->begin()->
    checkElement('input[value="Robert Aldrich (1)"]')->
    checkElement('input[value="The Dirty Dozen (1)"]')->
    checkElement('input[value="Les Douze Salopards (1)"]')->
    checkElement('#movie_fr_id', false)->
    checkElement('#movie_fr_culture', false)->
  end()->

  with('propel')->begin()->
    check('Movie', array(), 2)->
    check('Movie', array('director' => 'Robert Aldrich (1)', 'id' => 2))->
    check('MovieI18N', array(), 4)->
    check('MovieI18N', array('id' => 2), 2)->
    check('MovieI18N', array('culture' => 'fr', 'id' => 2, 'title' => 'Les Douze Salopards (1)'))->
    check('MovieI18N', array('culture' => 'en', 'id' => 2, 'title' => 'The Dirty Dozen (1)'))->
  end()
;
