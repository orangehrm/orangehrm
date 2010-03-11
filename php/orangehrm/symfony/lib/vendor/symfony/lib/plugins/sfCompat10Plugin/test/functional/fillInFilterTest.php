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
  post('/fillInFilter/forward', array('name' => 'fabien'))->
  isStatusCode(200)->
  isRequestParameter('module', 'fillInFilter')->
  isRequestParameter('action', 'forward')->
  checkResponseElement('body div', 'foo')
;

$b->
  post('/fillInFilter/update', array('first_name' => 'fabien'))->
  isStatusCode(200)->
  isRequestParameter('module', 'fillInFilter')->
  isRequestParameter('action', 'update')->
  checkResponseElement('input[name="first_name"][value="fabien"]')
;
