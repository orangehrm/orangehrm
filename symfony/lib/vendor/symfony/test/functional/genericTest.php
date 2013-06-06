<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// default main page
$b->
  getAndCheck('default', 'index', '/')->
  with('response')->begin()->
    checkElement('body', '/congratulations/i')->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
    checkElement('link[href="/css/main.css"]')->
    checkElement('link[href="/css/multiple_media.css"][media="print,handheld"]')->
    matches('#'.preg_quote('<!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="screen" href="/css/ie6.css" /><![endif]-->').'#')->
  end()
;

// default 404
$b->
  get('/nonexistant')->
  with('response')->isStatusCode(404)
;
/*
$b->
  get('/nonexistant/')->
  isStatusCode(404)
;
*/
// 404 with ETag enabled must returns 404, not 304
sfConfig::set('sf_cache', true);
sfConfig::set('sf_etag', true);
$b->
  get('/notfound')->
  with('request')->begin()->
    isParameter('module', 'notfound')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
    checkElement('body', '/404/')->
  end()->

  get('/notfound')->
  with('request')->begin()->
    isParameter('module', 'notfound')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
    checkElement('body', '/404/')->
  end()
;
sfConfig::set('sf_cache', false);
sfConfig::set('sf_etag', false);

// unexistant action
$b->
  get('/default/nonexistantaction')->
  with('response')->isStatusCode(404)
;

// module.yml: enabled
$b->
  get('/configModuleDisabled')->
  with('request')->begin()->
    isForwardedTo('default', 'disabled')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/module is unavailable/i')->
    checkElement('body', '!/congratulations/i')->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
  end()
;

// view.yml: has_layout
$b->
  get('/configViewHasLayout/withoutLayout')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/no layout/i')->
    checkElement('head title', false)->
  end()
;

// security.yml: is_secure
$b->
  get('/configSecurityIsSecure')->
  with('request')->begin()->
    isForwardedTo('default', 'login')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/Login Required/i')->

    // check that there is no double output caused by the forwarding in a filter
    checkElement('body', 1)->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
  end()
;

// security.yml: case sensitivity
$b->
  get('/configSecurityIsSecureAction/index')->
  with('request')->begin()->
    isForwardedTo('default', 'login')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/Login Required/i')->
  end()
;

$b->
  get('/configSecurityIsSecureAction/Index')->
  with('request')->begin()->
    isForwardedTo('default', 'login')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/Login Required/i')->
  end()
;

// Max forwards
$b->
  get('/configSettingsMaxForwards/selfForward')->
  with('response')->isStatusCode(500)->
  throwsException(null, '/Too many forwards have been detected for this request/i')
;

// filters.yml: add a filter
$b->
  get('/configFiltersSimpleFilter')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/in a filter/i')->
    checkElement('body', '!/congratulation/i')->
  end()
;

// css and js inclusions
$b->
  get('/assetInclusion/index')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('head link[rel="stylesheet"]', false)->
    checkElement('head script[type="text/javascript"]', false)->
  end()
;

// libraries autoloading
$b->
  get('/autoload/index')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#lib1', 'pong')->
    checkElement('#lib2', 'pong')->
    checkElement('#lib3', 'pong')->
    checkElement('#lib4', 'nopong')->
  end()
;

// libraries autoloading in a plugin
$b->
  get('/autoloadPlugin/index')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#lib1', 'pong')->
    checkElement('#lib2', 'pong')->
    checkElement('#lib3', 'pong')->
  end()
;

// renderText
$b->
  get('/renderText')->
  with('response')->begin()->
    isStatusCode(200)->
    matches('/foo/')->
  end()
;

// view.yml when changing template
$b->
  get('/view')->
  with('response')->begin()->
    isStatusCode(200)->
    isHeader('Content-Type', 'text/html; charset=utf-8')->
    checkElement('head title', 'foo title')->
  end()
;

// view.yml with other than default content-type
$b->
  get('/view/plain')->
  with('response')->begin()->
    isHeader('Content-Type', 'text/plain; charset=utf-8')->
    isStatusCode(200)->
    matches('/<head>/')->
    matches('/plaintext/')->
  end()
;

// view.yml with other than default content-type and no layout
$b->
  get('/view/image')->
  with('response')->begin()->
    isStatusCode(200)->
    isHeader('Content-Type', 'image/jpg')->
    matches('/image/')->
  end()
;

// getPresentationFor()
$b->
  get('/presentation')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#foo', 'foo')->
    checkElement('#foo_bis', 'foo')->
  end()
;
