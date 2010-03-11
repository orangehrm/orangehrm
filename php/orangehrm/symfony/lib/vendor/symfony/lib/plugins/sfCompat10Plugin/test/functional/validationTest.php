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
  get('/validation')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'index')->
  checkResponseElement('body h1', 'Form validation tests')->
  checkResponseElement('body form input[name="fake"][value=""]')->
  checkResponseElement('body form input[name="id"][value="1"]')->
  checkResponseElement('body form input[name="article[title]"][value="title"]')->
  checkResponseElement('body form textarea[name="article[body]"]', 'body')->
  checkResponseElement('body ul[class="errors"] li', 0)
;

// test fill in filter
$b->
  click('submit')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'index')->

  checkResponseElement('body form input[name="fake"][value=""]')->
  checkResponseElement('body form input[name="id"][value="1"]')->
  checkResponseElement('body form input[name="password"][value=""]')->
  checkResponseElement('body form input[name="article[title]"][value="title"]')->
  checkResponseElement('body form textarea[name="article[body]"]', 'body')->

  checkResponseElement('body ul[class="errors"] li[class="fake"]')
;

$b->
  click('submit', array('article' => array('title' => 'my title', 'body' => 'my body', 'password' => 'test', 'id' => 4)))->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'index')->

  checkResponseElement('body form input[name="fake"][value=""]')->
  checkResponseElement('body form input[name="id"][value="1"]')->
  checkResponseElement('body form input[name="password"][value=""]')->
  checkResponseElement('body form input[name="article[title]"][value="my title"]')->
  checkResponseElement('body form textarea[name="article[body]"]', 'my body')->

  checkResponseElement('body ul[class="errors"] li[class="fake"]')
;

// test group feature (with validator)
$b->test()->diag('test group feature (with validator)');
$b->
  get('/validation/group')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'group')
;

$b->test()->diag('when none of the two inputs are filled, the validation passes (ok)');
$b->
  click('submit')->
  checkResponseElement('body ul[class="errors"] li', false)
;

$b->test()->diag('when both fields are filled, the validation passes (ok)');
$b->
  click('submit', array('input1' => 'foo', 'input2' => '1234567890'))->
  checkResponseElement('body ul[class="errors"] li', false)
;

$b->test()->diag('when both fields are filled, and input2 has incorrect data, the validation fails because of the nameValidator on input2');
$b->
  click('submit', array('input1' => 'foo', 'input2' => 'bar'))->
  checkResponseElement('body ul[class="errors"] li[class="input1"]', false)->
  checkResponseElement('body ul[class="errors"] li[class="input2"]', 'nameValidator')
;

$b->test()->diag('when only the second input is filled, and with incorrect data, the validation fails because of the nameValidator on input2 and input1 is required');
$b->
  click('submit', array('input2' => 'foo'))->
  checkResponseElement('body ul[class="errors"] li[class="input1"]', 'Required')->
  checkResponseElement('body ul[class="errors"] li[class="input2"]', 'nameValidator')
;

$b->test()->diag('when only the first input is filled, the validation fails because of a required on input2');
$b->
  click('submit', array('input1' => 'foo'))->
  checkResponseElement('body ul[class="errors"] li[class="input1"]', false)->
  checkResponseElement('body ul[class="errors"] li[class="input2"]', 'Required')
;

// test group feature (without validator)
$b->test()->diag('test group feature (without validator)');
$b->
  get('/validation/group')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'group')
;

$b->test()->diag('when none of the two inputs are filled, the validation passes (ok)');
$b->
  click('submit')->
  checkResponseElement('body ul[class="errors"] li', false)
;

$b->test()->diag('when both fields are filled, the validation passes (ok)');
$b->
  click('submit', array('input3' => 'foo', 'input4' => 'bar'))->
  checkResponseElement('body ul[class="errors"] li', false)
;

$b->test()->diag('when only input4 is filled, the validation fails because input3 is required');
$b->
  click('submit', array('input4' => 'foo'))->
  checkResponseElement('body ul[class="errors"] li[class="input3"]', 'Required')->
  checkResponseElement('body ul[class="errors"] li[class="input4"]', false)
;

$b->test()->diag('when only input3 is filled, the validation fails because input4 is required');
$b->
  click('submit', array('input3' => 'foo'))->
  checkResponseElement('body ul[class="errors"] li[class="input3"]', false)->
  checkResponseElement('body ul[class="errors"] li[class="input4"]', 'Required')
;

// check that /validation/index and /validation/Index both uses the index.yml validation file (see #1617)
// those tests are only relevant on machines where filesystems are case sensitive.
$b->
  post('/validation/index')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'index')->
  isResponseHeader('X-Validated', 'ko')
;

$b->
  post('/validation/Index')->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'Index')->
  isResponseHeader('X-Validated', 'ko')
;

// needed to pass tests on case and non case sensitive machines
if (!file_exists(dirname(__FILE__).'/fixtures/apps/frontend/modules/validation/templates/IndexSuccess.php'))
{
  $b->throwsException('sfRenderException');
}

$b->
  post('/validation/INdex')->
  isStatusCode(404)
;

$b->
  post('/validation/index2')->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'index2')->
  isResponseHeader('X-Validated', 'ko')
;

if (!is_readable(dirname(__FILE__).'/fixtures/apps/frontend/modules/validation/templates/index2Success.php'))
{
  $b->throwsException('sfRenderException');
}

$b->
  post('/validation/Index2')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'Index2')->
  isResponseHeader('X-Validated', 'ko')
;
