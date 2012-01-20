<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend';
$fixtures = 'fixtures';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$tests = array(
  '/doctrine/route/test1' => '/Article/',
  '/doctrine/route/test2' => '/Article/',
  '/doctrine/route/test3' => '/Doctrine_Collection/',
  '/doctrine/route/test4' => '/Doctrine_Collection/',
  '/doctrine/route/test5/1/some_fake_value' => '/Article/',
  '/doctrine/route/test6/english-title/some_fake_value' => '/Article/',
  '/doctrine/route/test7/some_fake_value' => '/Doctrine_Collection/',
  '/doctrine/route/test9/1/english-title/English+Title/test' => '/Article/',
  '/doctrine/route/test10/1/test' => '/Doctrine_Collection/',
);

$b = new sfTestBrowser();
foreach ($tests as $url => $check)
{
  $b->
    get($url)->
    with('response')->begin()->
      isStatusCode('200')->
      matches($check)->
    end()
  ;
}

$article = Doctrine_Core::getTable('Article')->find(1);

$routes = array(
  'doctrine_route_test5' => array(
    'url' => '/index.php/doctrine/route/test5/1/test-english-title',
    'params' => $article
  ),
  'doctrine_route_test6' => array(
    'url' => '/index.php/doctrine/route/test6/english-title/test-english-title',
    'params' => $article
  ),
  'doctrine_route_test7' => array(
    'url' => '/index.php/doctrine/route/test7/w00t',
    'params' => array('testing_non_column' => 'w00t')
  ),
  'doctrine_route_test8' => array(
    'url' => '/index.php/doctrine/route/test8/1/english-title/English+Title/test',
    'params' => array(
      'id' => $article->id,
      'slug' => $article->slug,
      'title' => $article->title,
      'testing_non_column2' => 'test'
    )
  ),
);

foreach ($routes as $route => $check)
{
  $url = url_for2($route, $check['params']);
  $b->test()->is($url, $check['url'], 'Check "' . $route . '" generates correct url');
}