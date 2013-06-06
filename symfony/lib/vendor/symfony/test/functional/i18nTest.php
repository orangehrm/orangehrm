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
      with('response')->begin()->
        // messages in the global directories
        checkElement('#action', '/une phrase en français/i')->
        checkElement('#template', '/une phrase en français/i')->

        // messages in the module directories
        checkElement('#action_local', '/une phrase locale en français/i')->
        checkElement('#template_local', '/une phrase locale en français/i')->

        // messages in another global catalogue
        checkElement('#action_other', '/une autre phrase en français/i')->
        checkElement('#template_other', '/une autre phrase en français/i')->

        // messages in another module catalogue
        checkElement('#action_other_local', '/une autre phrase locale en français/i')->
        checkElement('#template_other_local', '/une autre phrase locale en français/i')->
      end()
    ;
  }
}

$b = new myTestBrowser();

// default culture (en)
$b->
  get('/')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#action', '/an english sentence/i')->
    checkElement('#template', '/an english sentence/i')->
  end()->
  with('user')->isCulture('en')
;

$b->
  get('/fr/i18n/index')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'index')->
  end()->
  with('response')->isStatusCode(200)->
  with('user')->isCulture('fr')->
  checkResponseForCulture('fr')
;

// change user culture in the action
$b->
  get('/en/i18n/indexForFr')->
  with('request')->begin()->
    isParameter('module', 'i18n')->
    isParameter('action', 'indexForFr')->
  end()->
  with('response')->isStatusCode(200)->
  with('user')->isCulture('fr')->
  checkResponseForCulture('fr')
;

// messages for a module plugin
$b->
  get('/fr/sfI18NPlugin/index')->
  with('request')->begin()->
    isParameter('module', 'sfI18NPlugin')->
    isParameter('action', 'index')->
  end()->
  with('user')->isCulture('fr')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#action', '/une phrase en français - from plugin/i')->
    checkElement('#template', '/une phrase en français - from plugin/i')->
    checkElement('#action_local', '/une phrase locale en français - from plugin/i')->
    checkElement('#template_local', '/une phrase locale en français - from plugin/i')->
    checkElement('#action_other', '/une autre phrase en français - from plugin but translation overridden in the module/i')->
    checkElement('#template_other', '/une autre phrase en français - from plugin but translation overridden in the module/i')->
    checkElement('#action_yetAnother', '/encore une autre phrase en français - from plugin but translation overridden in the application/i')->
    checkElement('#template_yetAnother', '/encore une autre phrase en français - from plugin but translation overridden in the application/i')->
    checkElement('#action_testForPluginI18N', '/une phrase en français depuis un plugin - global/i')->
    checkElement('#template_testForPluginI18N', '/une phrase en français depuis un plugin - global/i')->
  end()
;
