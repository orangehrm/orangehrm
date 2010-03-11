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
      isStatusCode(401)->
      isRequestParameter('module', 'auth')->
      isRequestParameter('action', 'basic')->
      checkResponseElement('#user', '')->
      checkResponseElement('#password', '')->
      checkResponseElement('#msg', 'KO')
    ;
  }

  public function checkAuth()
  {
    return $this->
      get('/auth/basic')->
      isStatusCode(200)->
      isRequestParameter('module', 'auth')->
      isRequestParameter('action', 'basic')->
      checkResponseElement('#user', 'foo')->
      checkResponseElement('#password', 'bar')->
      checkResponseElement('#msg', 'OK')
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
