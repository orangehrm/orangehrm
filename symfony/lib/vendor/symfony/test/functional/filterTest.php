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

// filter
$b->
  get('/filter')->
  with('request')->begin()->
    isParameter('module', 'filter')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="before"]', 1)->
    checkElement('div[class="after"]', 1)->
  end()
;

// filter with a forward in the same module
$b->
  get('/filter/indexWithForward')->
  with('request')->begin()->
    isParameter('module', 'filter')->
    isParameter('action', 'indexWithForward')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div[class="before"]', 2)->
    checkElement('div[class="after"]', 1)->
  end()
;
