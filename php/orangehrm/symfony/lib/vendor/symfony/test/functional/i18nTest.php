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

class myTestBrowser extends sfTestBrowser
{
  public function checkResponseForCulture($culture = 'fr')
  {
    return $this->
      // messages in the global directories
      checkResponseElement('#action', '/une phrase en français/i')->
      checkResponseElement('#template', '/une phrase en français/i')->

      // messages in the module directories
      checkResponseElement('#action_local', '/une phrase locale en français/i')->
      checkResponseElement('#template_local', '/une phrase locale en français/i')->

      // messages in another global catalogue
      checkResponseElement('#action_other', '/une autre phrase en français/i')->
      checkResponseElement('#template_other', '/une autre phrase en français/i')->

      // messages in another module catalogue
      checkResponseElement('#action_other_local', '/une autre phrase locale en français/i')->
      checkResponseElement('#template_other_local', '/une autre phrase locale en français/i')
    ;
  }
}

$b = new myTestBrowser();

// default culture (en)
$b->
  get('/')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'index')->
  isUserCulture('en')->
  checkResponseElement('#action', '/an english sentence/i')->
  checkResponseElement('#template', '/an english sentence/i')
;

$b->
  get('/fr/i18n/index')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'index')->
  isUserCulture('fr')->
  checkResponseForCulture('fr')
;

// change user culture in the action
$b->
  get('/en/i18n/indexForFr')->
  isStatusCode(200)->
  isRequestParameter('module', 'i18n')->
  isRequestParameter('action', 'indexForFr')->
  isUserCulture('fr')->
  checkResponseForCulture('fr')
;

// messages for a module plugin
$b->
  get('/fr/sfI18NPlugin/index')->
  isStatusCode(200)->
  isRequestParameter('module', 'sfI18NPlugin')->
  isRequestParameter('action', 'index')->
  isUserCulture('fr')->
  checkResponseElement('#action', '/une phrase en français - from plugin/i')->
  checkResponseElement('#template', '/une phrase en français - from plugin/i')->
  checkResponseElement('#action_local', '/une phrase locale en français - from plugin/i')->
  checkResponseElement('#template_local', '/une phrase locale en français - from plugin/i')->
  checkResponseElement('#action_other', '/une autre phrase en français - from plugin but translation overridden in the module/i')->
  checkResponseElement('#template_other', '/une autre phrase en français - from plugin but translation overridden in the module/i')->
  checkResponseElement('#action_yetAnother', '/encore une autre phrase en français - from plugin but translation overridden in the application/i')->
  checkResponseElement('#template_yetAnother', '/encore une autre phrase en français - from plugin but translation overridden in the application/i')->
  checkResponseElement('#action_testForPluginI18N', '/une phrase en français depuis un plugin - global/i')->
  checkResponseElement('#template_testForPluginI18N', '/une phrase en français depuis un plugin - global/i')
;
