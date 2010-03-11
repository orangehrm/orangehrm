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

$b->
  get('/escaping/on')->
  isStatusCode(200)->
  isRequestParameter('module', 'escaping')->
  isRequestParameter('action', 'on')->
  responseContains('<h1>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h1>')->
  responseContains('<h2>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h2>');
;

$b->
  get('/escaping/off')->
  isStatusCode(200)->
  isRequestParameter('module', 'escaping')->
  isRequestParameter('action', 'off')->
  responseContains('<h1>Lorem <strong>ipsum</strong> dolor sit amet.</h1>')->
  responseContains('<h2>Lorem <strong>ipsum</strong> dolor sit amet.</h2>');
;
