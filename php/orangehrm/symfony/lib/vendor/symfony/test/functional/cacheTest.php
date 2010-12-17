<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'cache';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

class myTestBrowser extends sfTestBrowser
{
  function checkResponseContent($content, $message)
  {
    $this->test()->ok($this->getResponse()->getContent() == $content, $message);

    return $this;
  }

  function getMultiAction($parameter = null)
  {
    return $this->
      get('/cache/multi'.(null !== $parameter ? '/param/'.$parameter : ''))->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'multi')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->

        // partials
        checkElement('#partial .partial')->

        // contextual partials
        checkElement('#contextualPartial .contextualPartial')->
        checkElement('#contextualCacheablePartial .contextualCacheablePartial__'.$parameter)->
        checkElement('#contextualCacheablePartialVarParam .contextualCacheablePartial_varParam_'.$parameter)->

        // components
        checkElement('#component .component__componentParam_'.$parameter)->
        checkElement('#componentVarParam .component_varParam_componentParam_'.$parameter)->

        // contextual components
        checkElement('#contextualComponent .contextualComponent__componentParam_'.$parameter)->
        checkElement('#contextualComponentVarParam .contextualComponent_varParam_componentParam_'.$parameter)->
        checkElement('#contextualCacheableComponent .contextualCacheableComponent__componentParam_'.$parameter)->
        checkElement('#contextualCacheableComponentVarParam .contextualCacheableComponent_varParam_componentParam_'.$parameter)->
      end()->

      with('view_cache')->begin()->
        isCached(false)->

        // partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_partial&sf_cache_key='.md5(serialize(array())), false)->
        isUriCached('@sf_cache_partial?module=cache&action=_partial&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), false)->

        isUriCached('@sf_cache_partial?module=cache&action=_cacheablePartial&sf_cache_key='.md5(serialize(array())), true)->
        isUriCached('@sf_cache_partial?module=cache&action=_cacheablePartial&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), true)->

        isUriCached('@sf_cache_partial?module=cache&action=_cacheablePartial&sf_cache_key='.md5(serialize(array('varParam' => 'another'))), false)->

        // contextual partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualPartial&sf_cache_key='.md5(serialize(array())), false)->
        isUriCached('@sf_cache_partial?module=cache&action=_contextualPartial&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), false)->

        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheablePartial&sf_cache_key='.md5(serialize(array())), true)->
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheablePartial&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), true)->

        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheablePartial&sf_cache_key='.md5(serialize(array('varParam' => 'another'))), false)->

        // component cache
        isUriCached('@sf_cache_partial?module=cache&action=_component&sf_cache_key='.md5(serialize(array())), false)->
        isUriCached('@sf_cache_partial?module=cache&action=_component&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), false)->

        isUriCached('@sf_cache_partial?module=cache&action=_cacheableComponent&sf_cache_key='.md5(serialize(array())), true)->
        isUriCached('@sf_cache_partial?module=cache&action=_cacheableComponent&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), true)->

        isUriCached('@sf_cache_partial?module=cache&action=_cacheableComponent&sf_cache_key='.md5(serialize(array('varParam' => 'another'))), false)->

        // contextual component cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualComponent&sf_cache_key='.md5(serialize(array())), false)->
        isUriCached('@sf_cache_partial?module=cache&action=_contextualComponent&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), false)->

        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key='.md5(serialize(array())), true)->
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key='.md5(serialize(array('varParam' => 'varParam'))), true)->

        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key='.md5(serialize(array('varParam' => 'another'))), false)->
      end()
    ;
  }

  public function launch()
  {
    $b = $this;

    // default page is in cache (without layout)
    $b->
      get('/')->
      with('request')->begin()->
        isParameter('module', 'default')->
        isParameter('action', 'index')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/congratulations/i')->
      end()->

      with('view_cache')->isCached(true)
    ;

    $b->
      get('/nocache')->
      with('request')->begin()->
        isParameter('module', 'nocache')->
        isParameter('action', 'index')->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/nocache/i')->
      end()->
      with('view_cache')->isCached(false)
    ;

    $b->
      get('/cache/page')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'page')->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/page in cache/')->
      end()->
      with('view_cache')->isCached(true, true)
    ;

    $b->
      get('/cache/forward')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'forward')->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/page in cache/')->
      end()->
      with('view_cache')->isCached(true)
    ;

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    $b->
      getMultiAction()->

      getMultiAction('requestParam')->

      // component already in cache and not contextual, so request parameter is not there
      with('response')->begin()->
        checkElement('#cacheableComponent .cacheableComponent__componentParam_')->
        checkElement('#cacheableComponentVarParam .cacheableComponent_varParam_componentParam_')->
        checkElement('#cacheablePartial .cacheablePartial__')->
        checkElement('#cacheablePartialVarParam .cacheablePartial_varParam_')->
      end()
    ;

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    $b->
      getMultiAction('requestParam')->

      with('response')->begin()->
        checkElement('#cacheableComponent .cacheableComponent__componentParam_requestParam')->
        checkElement('#cacheableComponentVarParam .cacheableComponent_varParam_componentParam_requestParam')->
        checkElement('#cacheablePartial .cacheablePartial__requestParam')->
        checkElement('#cacheablePartialVarParam .cacheablePartial_varParam_requestParam')->
      end()->

      getMultiAction()->

      with('response')->begin()->
        checkElement('#cacheableComponent .cacheableComponent__componentParam_requestParam')->
        checkElement('#cacheableComponentVarParam .cacheableComponent_varParam_componentParam_requestParam')->
        checkElement('#cacheablePartial .cacheablePartial__requestParam')->
        checkElement('#cacheablePartialVarParam .cacheablePartial_varParam_requestParam')->
      end()->

      getMultiAction('anotherRequestParam')->

      with('response')->begin()->
        checkElement('#cacheableComponent .cacheableComponent__componentParam_requestParam')->
        checkElement('#cacheableComponentVarParam .cacheableComponent_varParam_componentParam_requestParam')->
        checkElement('#cacheablePartial .cacheablePartial__requestParam')->
        checkElement('#cacheablePartialVarParam .cacheablePartial_varParam_requestParam')->
      end()
    ;

    // check contextual cache with another action
    $b->
      get('/cache/multiBis')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'multiBis')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->

        // partials
        checkElement('#cacheablePartial .cacheablePartial__requestParam')->

        // contextual partials
        checkElement('#contextualCacheablePartial .contextualCacheablePartial__')->

        // components
        checkElement('#cacheableComponent .cacheableComponent__componentParam_requestParam')->

        // contextual components
        checkElement('#contextualCacheableComponent .contextualCacheableComponent__componentParam_')->
      end()->

      with('view_cache')->begin()->
        isCached(false)->
        // partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_cacheablePartial&sf_cache_key='.md5(serialize(array())), true)->

        // contextual partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key='.md5(serialize(array())), true)->

        // component cache
        isUriCached('@sf_cache_partial?module=cache&action=_cacheableComponent&sf_cache_key='.md5(serialize(array())), true)->

        // contextual component cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key='.md5(serialize(array())), true)->
      end()
    ;

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    // check user supplied cache key for partials and components
    $b->
      get('/cache/specificCacheKey')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'specificCacheKey')->
      end()->

      with('response')->isStatusCode(200)->
      with('view_cache')->begin(200)->
        isCached(false)->

        // partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_cacheablePartial&sf_cache_key=cacheablePartial', true)->

        // contextual partial cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key=contextualCacheableComponent', true)->

        // component cache
        isUriCached('@sf_cache_partial?module=cache&action=_cacheableComponent&sf_cache_key=cacheableComponent', true)->

        // contextual component cache
        isUriCached('@sf_cache_partial?module=cache&action=_contextualCacheableComponent&sf_cache_key=contextualCacheableComponent', true)->
      end()
    ;

    // check cache content for actions

    // remove all cache
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

    $b->
      get('/cache/action')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'action')->
      end()->
      with('response')->isStatusCode(200)->
      with('view_cache')->isCached(true)
    ;

    $b->test()->is(sfConfig::get('ACTION_EXECUTED', false), true, 'action is executed when not in cache');
    sfConfig::set('ACTION_EXECUTED', false);

    $response = $b->getResponse();
    $content1 = $response->getContent();
    $contentType1 = $response->getContentType();
    $headers1 = $response->getHttpHeaders();

    $b->
      get('/cache/action')->
      with('request')->begin()->
        isParameter('module', 'cache')->
        isParameter('action', 'action')->
      end()->
      with('response')->isStatusCode(200)->
      with('view_cache')->isCached(true)
    ;

    $b->test()->is(sfConfig::get('ACTION_EXECUTED', false), false, 'action is not executed when in cache');

    $response = $b->getResponse();
    $content2 = $response->getContent();
    $contentType2 = $response->getContentType();
    $headers2 = $response->getHttpHeaders();

    $b->test()->is($content1, $content2, 'response content is the same');
    $b->test()->is($contentType1, $contentType2, 'response content type is the same');
    $b->test()->is($headers1, $headers2, 'response http headers are the same');
  }
}

$b = new myTestBrowser();

// non HTML cache
$image = file_get_contents(dirname(__FILE__).'/fixtures/apps/cache/modules/cache/data/ok48.png');
sfConfig::set('sf_web_debug', true);
$b->
  get('/cache/imageWithLayoutCacheWithLayout')->
  with('view_cache')->isCached(true, true)->
  checkResponseContent($image, 'image (with layout/page cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageWithLayoutCacheWithLayout')->
  with('view_cache')->isCached(true, true)->
  checkResponseContent($image, 'image (with layout/page cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageWithLayoutCacheNoLayout')->
  with('view_cache')->isCached(true)->
  checkResponseContent($image, 'image (with layout/action cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageWithLayoutCacheNoLayout')->
  with('view_cache')->isCached(true)->
  checkResponseContent($image, 'image (with layout/action cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageNoLayoutCacheWithLayout')->
  with('view_cache')->isCached(true, true)->
  checkResponseContent($image, 'image (no layout/page cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageNoLayoutCacheWithLayout')->
  with('view_cache')->isCached(true, true)->
  checkResponseContent($image, 'image (no layout/page cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageNoLayoutCacheNoLayout')->
  with('view_cache')->isCached(true)->
  checkResponseContent($image, 'image (no layout/action cache) in cache is not decorated when web_debug is on')->
  get('/cache/imageNoLayoutCacheNoLayout')->
  with('view_cache')->isCached(true)->
  checkResponseContent($image, 'image (no layout/action cache) in cache is not decorated when web_debug is on')
;
sfConfig::set('sf_web_debug', false);

// check stylesheets, javascripts inclusions
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
$b->
  get('/cache/multiBis')->
  with('request')->begin()->
    isParameter('module', 'cache')->
    isParameter('action', 'multiBis')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->

    // the first time (no cache)
    checkElement('link[href*="/main_css"]')->
    checkElement('script[src*="/main_js"]')->
    checkElement('link[href*="/partial_css"]')->
    checkElement('script[src*="/partial_js"]')->
    checkElement('link[href*="/another_partial_css"]')->
    checkElement('script[src*="/another_partial_js"]')->
    checkElement('link[href*="/component_css"]')->
    checkElement('script[src*="/component_js"]')->

    checkElement('#partial_slot_content', 'Partial')->
    checkElement('#another_partial_slot_content', 'Another Partial')->
    checkElement('#component_slot_content', 'Component')->
  end()->

  get('/cache/multiBis')->
  with('response')->begin()->

    // when in cache
    checkElement('link[href*="/main_css"]')->
    checkElement('script[src*="/main_js"]')->
    checkElement('link[href*="/partial_css"]')->
    checkElement('script[src*="/partial_js"]')->
    checkElement('link[href*="/another_partial_css"]')->
    checkElement('script[src*="/another_partial_js"]')->
    checkElement('link[href*="/component_css"]')->
    checkElement('script[src*="/component_js"]')->

    checkElement('#partial_slot_content', 'Partial')->
    checkElement('#another_partial_slot_content', 'Another Partial')->
    checkElement('#component_slot_content', 'Component')->
  end()
;

$b->
  get('/cache/partial')->
  with('request')->begin()->
    isParameter('module', 'cache')->
    isParameter('action', 'partial')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->

    // only partial specific css and js are included
    checkElement('link[href*="/main_css"]', false)->
    checkElement('script[src*="/main_js"]', false)->
    checkElement('link[href*="/partial_css"]')->
    checkElement('script[src*="/partial_js"]')->
    checkElement('link[href*="/another_partial_css"]')->
    checkElement('script[src*="/another_partial_js"]')->
    checkElement('link[href*="/component_css"]', false)->
    checkElement('script[src*="/component_js"]', false)->

    checkElement('#partial_slot_content', 'Partial')->
    checkElement('#another_partial_slot_content', 'Another Partial')->
    checkElement('#component_slot_content', '')->
  end()->

  get('/cache/anotherPartial')->
  with('request')->begin()->
    isParameter('module', 'cache')->
    isParameter('action', 'anotherPartial')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->

    // only partial specific css and js are included
    checkElement('link[href*="/main_css"]', false)->
    checkElement('script[src*="/main_js"]', false)->
    checkElement('link[href*="/partial_css"]', false)->
    checkElement('script[src*="/partial_js"]', false)->
    checkElement('link[href*="/another_partial_css"]')->
    checkElement('script[src*="/another_partial_js"]')->
    checkElement('link[href*="/component_css"]', false)->
    checkElement('script[src*="/component_js"]', false)->

    checkElement('#partial_slot_content', '')->
    checkElement('#another_partial_slot_content', 'Another Partial')->
    checkElement('#component_slot_content', '')->
  end()->

  get('/cache/component')->
  with('request')->begin()->
    isParameter('module', 'cache')->
    isParameter('action', 'component')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->

    // only partial specific css and js are included
    checkElement('link[href*="/main_css"]', false)->
    checkElement('script[src*="/main_js"]', false)->
    checkElement('link[href*="/partial_css"]', false)->
    checkElement('script[src*="/partial_js"]', false)->
    checkElement('link[href*="/another_partial_css"]', false)->
    checkElement('script[src*="/another_partial_js"]', false)->
    checkElement('link[href*="/component_css"]')->
    checkElement('script[src*="/component_js"]')->

    checkElement('#partial_slot_content', '')->
    checkElement('#another_partial_slot_content', '')->
    checkElement('#component_slot_content', 'Component')->
  end()
;

$b->get('/')
  ->with('view_cache')->isUriCached('cache/list', false)
  ->get('/cache/list')
  ->with('view_cache')->isUriCached('cache/list', true)

  // include GET parameters
  ->with('view_cache')->isUriCached('cache/list?page=10', false)
  ->get('/cache/list?page=10')
  ->with('response')->checkElement('#page', '10')
  ->with('view_cache')->isUriCached('cache/list?page=10', true)

  // include different GET parameters
  ->with('view_cache')->isUriCached('cache/list?page=20', false)
  ->get('/cache/list?page=20')
  ->with('response')->checkElement('#page', '20')
  ->with('view_cache')->isUriCached('cache/list?page=20', true)
;

// check for 304 response
sfConfig::set('LAST_MODIFIED', strtotime('2010-01-01'));
$b->get('/cache/lastModifiedResponse')
  ->with('response')->isStatusCode(200)
;

$b->setHttpHeader('If-Modified-Since', sfWebResponse::getDate(sfConfig::get('LAST_MODIFIED')))
  ->get('/cache/lastModifiedResponse')
  ->with('response')->isStatusCode(304)
;

// test with sfFileCache class (default)
$b->launch();

// test with sfSQLiteCache class
if (extension_loaded('SQLite') || extension_loaded('pdo_SQLite')) 
{
  sfConfig::set('sf_factory_view_cache', 'sfSQLiteCache');
  sfConfig::set('sf_factory_view_cache_parameters', array('database' => sfConfig::get('sf_template_cache_dir').DIRECTORY_SEPARATOR.'cache.db'));
  $b->launch();
}
