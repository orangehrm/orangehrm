<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');
require_once($_test_dir.'/unit/sfNoRouting.class.php');

$t = new lime_test(21);

class myWebResponse extends sfWebResponse
{
  public function sendHttpHeaders()
  {
  }

  public function send()
  {
  }
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
sfConfig::set('sf_max_forwards', 10);
$context = sfContext::getInstance(array(
  'routing'  => 'sfNoRouting',
  'request'  => 'sfWebRequest',
  'response' => 'myWebResponse',
));

$controller = new sfFrontWebController($context, null);

$tests = array(
  'module/action' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
    ),
  ),
  'module/action?id=12' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'id'     => 12,
    ),
  ),
  'module/action?id=12&' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'id'     => '12&',
    ),
  ),
  'module/action?id=12&test=4&toto=9' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'id'     => 12,
      'test'   => 4,
      'toto'   => 9,
    ),
  ),
  'module/action?id=12&test=4&5&6&7&&toto=9' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'id'     => 12,
      'test'   => '4&5&6&7&',
      'toto'   => 9,
    ),
  ),
  'module/action?test=value1&value2&toto=9' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'test'   => 'value1&value2',
      'toto'   => 9,
    ),
  ),
  'module/action?test=value1&value2' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'test'   => 'value1&value2',
    ),
  ),
  'module/action?test=value1=value2&toto=9' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'test'   => 'value1=value2',
      'toto'   => 9,
    ),
  ),
  'module/action?test=value1=value2' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'test'   => 'value1=value2',
    ),
  ),
  'module/action?test=4&5&6&7&&toto=9&id=' => array(
    '',
    array(
      'module' => 'module',
      'action' => 'action',
      'test'   => '4&5&6&7&',
      'toto'   => 9,
      'id'     => '',
    ),
  ),
  '@test?test=4' => array(
    'test',
    array(
      'test' => 4
    ),
  ),
  '@test' => array(
    'test',
    array(
    ),
  ),
  '@test?id=12&foo=bar' => array(
    'test',
    array(
      'id' => 12,
      'foo' => 'bar',
    ),
  ),
  '@test?id=foo%26bar&foo=bar%3Dfoo' => array(
    'test',
    array(
      'id' => 'foo&bar',
      'foo' => 'bar=foo',
    ),
  ),
);

// ->convertUrlStringToParameters()
$t->diag('->convertUrlStringToParameters()');
foreach ($tests as $url => $result)
{
  $t->is($controller->convertUrlStringToParameters($url), $result, sprintf('->convertUrlStringToParameters() converts a symfony internal URI to an array of parameters (%s)', $url));
}

try
{
  $controller->convertUrlStringToParameters('@test?foobar');
  $t->fail('->convertUrlStringToParameters() throw a sfParseException if it cannot parse the query string');
}
catch (sfParseException $e)
{
  $t->pass('->convertUrlStringToParameters() throw a sfParseException if it cannot parse the query string');
}

// ->redirect()
$t->diag('->redirect()');
$controller->redirect('module/action?id=1#photos');
$response = $context->getResponse();
$t->like($response->getContent(), '~http\://localhost/index.php/\?module=module&amp;action=action&amp;id=1#photos~', '->redirect() adds a refresh meta in the content');
$t->like($response->getHttpHeader('Location'), '~http\://localhost/index.php/\?module=module&action=action&id=1#photos~', '->redirect() adds a Location HTTP header');

// Test null url argument for ->redirect()
try
{
  $controller->redirect(null);
  $t->fail('->redirect() throw an InvalidArgumentException when the url argument is null');
}
catch (InvalidArgumentException $iae)
{
  $t->pass('->redirect() throw an InvalidArgumentException when the url argument is null');
}
catch(Exception $e)
{
  $t->fail('->redirect() throw an InvalidArgumentException when the url argument is null. '.get_class($e).' was received');
}

// Test empty string url argument for ->redirect()
try
{
  $controller->redirect('');
  $t->fail('->redirect() throw an InvalidArgumentException when the url argument is an empty string');
}
catch (InvalidArgumentException $iae)
{
  $t->pass('->redirect() throw an InvalidArgumentException when the url argument is an empty string');
}
catch(Exception $e)
{
  $t->fail('->redirect() throw an InvalidArgumentException when the url argument is an empty string. '.get_class($e).' was received');
}

// ->genUrl()
$t->diag('->genUrl()');
$t->is($controller->genUrl('module/action?id=4'), $controller->genUrl(array('action' => 'action', 'module' => 'module', 'id' => 4)), '->genUrl() accepts a string or an array as its first argument');

$lastError = error_get_last();
$controller->genUrl('');
$t->is_deeply(error_get_last(), $lastError, '->genUrl() accepts an empty string');
