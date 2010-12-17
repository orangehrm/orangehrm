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

$b->get('/escaping/on')

  ->with('request')->begin()
    ->isParameter('module', 'escaping')
    ->isParameter('action', 'on')
  ->end()

  ->with('response')->begin()
    ->isStatusCode(200)
    ->matches('#<h1>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h1>#')
    ->matches('#<h2>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h2>#')
    ->matches('#<h3>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h3>#')
    ->matches('#<h4>Lorem <strong>ipsum</strong> dolor sit amet.</h4>#')
    ->matches('#<h5>Lorem &lt;strong&gt;ipsum&lt;/strong&gt; dolor sit amet.</h5>#')
    ->matches('#<h6>Lorem <strong>ipsum</strong> dolor sit amet.</h6>#')
    ->checkElement('span.no', 2)
  ->end()
;

$b->get('/escaping/off')

  ->with('request')->begin()
    ->isParameter('module', 'escaping')
    ->isParameter('action', 'off')
  ->end()

  ->with('response')->begin()
    ->isStatusCode(200)
    ->matches('#<h1>Lorem <strong>ipsum</strong> dolor sit amet.</h1>#')
    ->matches('#<h2>Lorem <strong>ipsum</strong> dolor sit amet.</h2>#')
  ->end()
;
