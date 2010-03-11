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
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'i18nForm')->
  isUserCulture('en')->
  checkResponseElement('label', 'First name', array('position' => 0))->
  checkResponseElement('label', 'Last name', array('position' => 1))->
  checkResponseElement('label', 'Email address', array('position' => 2))->
  checkResponseElement('td', '/Put your first name here/i', array('position' => 0))->
  setField('i18n[email]', 'foo/bar')->
  click('Submit')->
  checkResponseElement('ul li', 'Required.', array('position' => 0))->
  checkResponseElement('ul li', 'foo/bar is an invalid email address', array('position' => 2))
;

// changed culture (fr)
$b->
  get('/fr/i18n/i18nForm')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'i18nForm')->
  isUserCulture('fr')->
  checkResponseElement('label', 'Prénom', array('position' => 0))->
  checkResponseElement('label', 'Nom', array('position' => 1))->
  checkResponseElement('label', 'Adresse email', array('position' => 2))->
  checkResponseElement('td', '/Mettez votre prénom ici/i', array('position' => 0))->
  setField('i18n[email]', 'foo/bar')->
  click('Submit')->
  checkResponseElement('ul li', 'Champ requis.', array('position' => 0))->
  checkResponseElement('ul li', 'foo/bar est une adresse email invalide', array('position' => 2))
;

// forms label custoim catalogue test
$b->
  get('/fr/i18n/i18nCustomCatalogueForm')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'i18nCustomCatalogueForm')->
  isUserCulture('fr')->
  checkResponseElement('label', 'Prénom!!!', array('position' => 0))->
  checkResponseElement('label', 'Nom!!!', array('position' => 1))->
  checkResponseElement('label', 'Adresse email!!!', array('position' => 2))
;
