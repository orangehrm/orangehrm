<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'i18n';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// default culture (en)
$b->
  get('/en/i18n/i18nForm')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'i18nForm')->
  end()->
  with('user')->isCulture('en')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label', 'First name', array('position' => 0))->
    checkElement('label', 'Last name', array('position' => 1))->
    checkElement('label', 'Email address', array('position' => 2))->
    checkElement('td', '/Put your first name here/i', array('position' => 0))->
  end()->
  setField('i18n[email]', 'foo/bar')->
  click('Submit')->
  with('response')->begin()->
    checkElement('ul li', 'Required.', array('position' => 0))->
    checkElement('ul li', 'foo/bar is an invalid email address', array('position' => 2))->
  end()
;

// changed culture (fr)
$b->
  get('/fr/i18n/i18nForm')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'i18nForm')->
  end()->
  with('user')->isCulture('fr')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label', 'Prénom', array('position' => 0))->
    checkElement('label', 'Nom', array('position' => 1))->
    checkElement('label', 'Adresse email', array('position' => 2))->
    checkElement('td', '/Mettez votre prénom ici/i', array('position' => 0))->
  end()->
  setField('i18n[email]', 'foo/bar')->
  click('Submit')->
  with('response')->begin()->
    checkElement('ul li', 'Champ requis.', array('position' => 0))->
    checkElement('ul li', 'foo/bar est une adresse email invalide', array('position' => 2))->
  end()
;

// forms label custom catalogue test
$b->
  get('/fr/i18n/i18nCustomCatalogueForm')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'i18nCustomCatalogueForm')->
  end()->
  with('user')->isCulture('fr')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('label', 'Prénom!!!', array('position' => 0))->
    checkElement('label', 'Nom!!!', array('position' => 1))->
    checkElement('label', 'Adresse email!!!', array('position' => 2))->
  end()
;
