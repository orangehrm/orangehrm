<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(15);

$v = new sfValidatorUrl();

// ->clean()
$t->diag('->clean()');
foreach (array(
  'http://www.google.com',
  'https://google.com/',
  'https://google.com:80/',
  'http://www.symfony-project.com/',
  'http://127.0.0.1/',
  'http://127.0.0.1:80/',
  'ftp://google.com/foo.tgz', 
  'ftps://google.com/foo.tgz', 
) as $url)
{
  $t->is($v->clean($url), $url, '->clean() checks that the value is a valid URL');
}

foreach (array(
  'google.com',
  'http:/google.com',
  'http://google.com::aa',
) as $nonUrl)
{
  try
  {
    $v->clean($nonUrl);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid URL');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid URL');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}

$v = new sfValidatorUrl(array('protocols' => array('http', 'https')));
try
{
  $v->clean('ftp://google.com/foo.tgz');
  $t->fail('->clean() only allows protocols specified in the protocols option');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() only allows protocols specified in the protocols option');
}
