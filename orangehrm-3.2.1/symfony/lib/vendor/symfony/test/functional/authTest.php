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

class sfAuthTestBrowser extends sfTestBrowser
{
  public function checkNonAuth()
  {
    return $this->
      get('/auth/basic')->
      with('request')->begin()->
        isParameter('module', 'auth')->
        isParameter('action', 'basic')->
      end()->
      with('response')->begin()->
        isStatusCode(401)->
        checkElement('#user', '')->
        checkElement('#password', '')->
        checkElement('#msg', 'KO')->
      end()
    ;
  }

  public function checkAuth()
  {
    return $this->
      get('/auth/basic')->
      with('request')->begin()->
        isParameter('module', 'auth')->
        isParameter('action', 'basic')->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#user', 'foo')->
        checkElement('#password', 'bar')->
        checkElement('#msg', 'OK')->
      end()
    ;
  }
}

$b = new sfAuthTestBrowser();

// default main page
$b->
  checkNonAuth()->

  setAuth('foo', 'bar')->

  checkAuth()->

  restart()->

  checkNonAuth()
;
