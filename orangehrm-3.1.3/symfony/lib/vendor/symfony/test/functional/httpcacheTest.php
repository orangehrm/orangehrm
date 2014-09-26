<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'cache';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestFunctional(new sfBrowser());

$b->
  info('No expiration (client_lifetime is 0)')->
  get('/httpcache/page1')->
  with('response')->begin()->
    isHeader('Last-Modified', '/^'.substr(preg_quote(sfWebResponse::getDate(time()), '/'), 5, 16).'/')->
    isHeader('ETag', true)->
    isHeader('Expires', false)->
    isHeader('Cache-Control', false)->
  end()
;

$b->
  info('Expiration (client_lifetime is 86400)')->
  get('/httpcache/page2')->
  with('response')->begin()->
    isHeader('Last-Modified', false)->
    isHeader('ETag', false)->
    isHeader('Expires', '/^'.substr(preg_quote(sfWebResponse::getDate(time() + 86400), '/'), 5, 16).'/')->
    isHeader('Cache-Control', '/max-age=86400/')->
  end()
;

$b->
  info('Expiration (client_lifetime is 86400) but the developer has set a Last-Modified header')->
  get('/httpcache/page3')->
  with('response')->begin()->
    isHeader('Last-Modified', '/^'.substr(preg_quote(sfWebResponse::getDate(time() - 86400), '/'), 5, 16).'/')->
    isHeader('ETag', false)->
    isHeader('Expires', false)->
    isHeader('Cache-Control', false)->
  end()
;

$b->
  info('No expiration and the developer has set a Last-Modified header')->
  get('/httpcache/page4')->
  with('response')->begin()->
    isHeader('Last-Modified', '/^'.substr(preg_quote(sfWebResponse::getDate(time() - 86400), '/'), 5, 16).'/')->
    isHeader('ETag', true)->
    isHeader('Expires', false)->
    isHeader('Cache-Control', false)->
  end()
;
