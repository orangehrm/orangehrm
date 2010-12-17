<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$debug = false;
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// default main page (without cache)
$b->
  get('/')->
  with('request')->begin()->
    isParameter('module', 'default')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/congratulations/i')->
  end()
;

// default main page (with cache)
$b->
  get('/')->
  with('request')->begin()->
    isParameter('module', 'default')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '/congratulations/i')->
  end()
;

// 404
$b->
  get('/nonexistant')->
  with('request')->begin()->
    isForwardedTo('default', 'error404')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
    checkElement('body', '!/congratulations/i')->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
  end()
;

$b->
  get('/nonexistant/')->
  with('request')->begin()->
    isForwardedTo('default', 'error404')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
    checkElement('body', '!/congratulations/i')->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
  end()
;

// unexistant action
$b->
  get('/default/nonexistantaction')->
  with('request')->begin()->
    isForwardedTo('default', 'error404')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
    checkElement('body', '!/congratulations/i')->
    checkElement('link[href="/sf/sf_default/css/screen.css"]')->
  end()
;
