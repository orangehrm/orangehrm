<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(24);

foreach (array(
  'cache', 'configuration', 'controller', 'database', 
  'error404', 'factory', 'file', 'filter', 'forward', 'initialization', 'parse', 'render', 'security',
  'stop', 'storage', 'view'
) as $class)
{
  $class = sprintf('sf%sException', ucfirst($class));
  $e = new $class();
  $t->ok($e instanceof sfException, sprintf('"%s" inherits from sfException', $class));
}

class myException extends sfException
{
  static public function formatArgsTest($args, $single = false, $format = 'html')
  {
    return parent::formatArgs($args, $single, $format);
  }
}

// sfException::formatArgs()
$t->diag('sfException::formatArgs()');
$t->is(myException::formatArgsTest('foo', true), "'foo'", 'formatArgs() can format a single argument');
$t->is(myException::formatArgsTest(array('foo', 'bar')), "'foo', 'bar'", 'formatArgs() can format an array of arguments');
$t->is(myException::formatArgsTest(new stdClass(), true), "<em>object</em>('stdClass')", 'formatArgs() can format an objet instance');
$t->is(myException::formatArgsTest(null, true), "<em>null</em>", 'formatArgs() can format a null');
$t->is(myException::formatArgsTest(100, true), "100", 'formatArgs() can format an integer');
$t->is(myException::formatArgsTest(array('foo' => new stdClass(), 'bar' => 2), true), "<em>array</em>('foo' => <em>object</em>('stdClass'), 'bar' => 2)", 'formatArgs() can format a nested array');

$t->is(myException::formatArgsTest('&', true), "'&amp;'", 'formatArgs() escapes strings');
$t->is(myException::formatArgsTest(array('&' => '&'), true), "<em>array</em>('&amp;' => '&amp;')", 'formatArgs() escapes strings for keys and values in arrays');
