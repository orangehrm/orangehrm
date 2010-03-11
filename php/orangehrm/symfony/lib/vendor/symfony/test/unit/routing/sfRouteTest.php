<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(50, new lime_output_color());

// ->matchesUrl()
$t->diag('->matchesUrl()');
$route = new sfRoute('/');
$t->is($route->matchesUrl('/'), array(), '->matchesUrl() takes a URL as its first argument');
$t->is($route->matchesUrl('/foo'), false, '->matchesUrl() returns false if the route does not match');

$route = new sfRoute('/', array('foo' => 'bar'));
$t->is($route->matchesUrl('/'), array('foo' => 'bar'), '->matchesUrl() returns default values for parameters not in the route');

$route = new sfRoute('/:bar', array('foo' => 'bar'));
$t->is($route->matchesUrl('/foobar'), array('foo' => 'bar', 'bar' => 'foobar'), '->matchesUrl() returns variables from the pattern');

$route = new sfRoute('/:foo', array('foo' => 'bar'));
$t->is($route->matchesUrl('/foobar'), array('foo' => 'foobar'), '->matchesUrl() overrides default value with pattern value');

$route = new sfRoute('/:foo', array('foo' => 'bar'));
$t->is($route->matchesUrl('/'), array('foo' => 'bar'), '->matchesUrl() matches routes with an optional parameter at the end');

$route = new sfRoute('/:foo', array('foo' => null));
$t->is($route->matchesUrl('/'), array('foo' => null), '->matchesUrl() matches routes with an optional parameter at the end, even if it is null');

$route = new sfRoute('/:foo', array('foo' => ''));
$t->is($route->matchesUrl('/'), array('foo' => ''), '->matchesUrl() matches routes with an optional parameter at the end, even if it is empty');

$route = new sfRoute('/:foo/bar', array('foo' => null));
$t->is($route->matchesUrl('//bar'), false, '->matchesUrl() does not match routes with an empty parameter not at the end');
$t->is($route->matchesUrl('/bar'), false, '->matchesUrl() does not match routes with an empty parameter not at the end');

$route = new sfRoute('/foo/:foo/bar/:bar', array('foo' => 'bar', 'bar' => 'foo'));
$t->is($route->matchesUrl('/foo/bar/bar'), array('foo' => 'bar', 'bar' => 'foo'), '->matchesUrl() matches routes with an optional parameter at the end');

$route = new sfRoute('/:foo/:bar', array('foo' => 'bar', 'bar' => 'foo'));
$t->is($route->matchesUrl('/'), array('foo' => 'bar', 'bar' => 'foo'), '->matchesUrl() matches routes with multiple optionals parameters at the end');

$route = new sfRoute('/', array());
$route->setDefaultParameters(array('foo' => 'bar'));
$t->is($route->matchesUrl('/'), array('foo' => 'bar'), '->matchesUrl() gets default parameters from the routing object if it exists');

$route = new sfRoute('/', array('foo' => 'foobar'));
$route->setDefaultParameters(array('foo' => 'bar'));
$t->is($route->matchesUrl('/'), array('foo' => 'foobar'), '->matchesUrl() overrides routing default parameters with route default parameters');

$route = new sfRoute('/:foo', array('foo' => 'foobar'));
$route->setDefaultParameters(array('foo' => 'bar'));
$t->is($route->matchesUrl('/barfoo'), array('foo' => 'barfoo'), '->matchesUrl() overrides routing default parameters with pattern parameters');

$route = new sfRoute('/:foo', array(), array('foo' => '\d+'));
$t->is($route->matchesUrl('/bar'), false, '->matchesUrl() enforces requirements');

$route = new sfRoute('/:foo', array(), array('foo' => '\w+'));
$t->is($route->matchesUrl('/bar'), array('foo' => 'bar'), '->matchesUrl() enforces requirements');

// ->matchesParameters()
$t->diag('->matchesParameters()');
$route = new sfRoute('/', array());
$t->is($route->matchesParameters('string'), false, '->matchesParameters() returns false if the argument is not an array of parameters');

$route = new sfRoute('/:foo');
$t->is($route->matchesParameters(array()), false, '->matchesParameters() returns false if one of the pattern variable is not provided');

$route = new sfRoute('/:foo', array('foo' => 'bar'));
$t->is($route->matchesParameters(array()), true, '->matchesParameters() merges the default parameters with the provided parameters to match the route');

$route = new sfRoute('/:foo');
$t->is($route->matchesParameters(array('foo' => 'bar')), true, '->matchesParameters() matches if all variables are given as parameters');

$route = new sfRoute('/:foo');
$t->is($route->matchesParameters(array('foo' => '')), true, '->matchesParameters() matches if optional parameters empty');
$t->is($route->matchesParameters(array('foo' => null)), true, '->matchesParameters() matches if optional parameters empty');

/*
$route = new sfRoute('/:foo/bar');
$t->is($route->matchesParameters(array('foo' => '')), false, '->matchesParameters() does not match is required parameters are empty');
$t->is($route->matchesParameters(array('foo' => null)), false, '->matchesParameters() does not match is required parameters are empty');
*/

$route = new sfRoute('/:foo');
$route->setDefaultParameters(array('foo' => 'bar'));
$t->is($route->matchesParameters(array()), true, '->matchesParameters() merges the routing default parameters with the provided parameters to match the route');

$route = new sfRoute('/:foo', array(), array('foo' => '\d+'));
$t->is($route->matchesParameters(array('foo' => 'bar')), false, '->matchesParameters() enforces requirements');

$route = new sfRoute('/:foo', array(), array('foo' => '\d+'));
$t->is($route->matchesParameters(array('foo' => 12)), true, '->matchesParameters() enforces requirements');

$route = new sfRoute('/', array('foo' => 'bar'));
$t->is($route->matchesParameters(array('foo' => 'foobar')), false, '->matchesParameters() checks that there is no parameter that is not a pattern variable');

$route = new sfRoute('/', array('foo' => 'bar'));
$t->is($route->matchesParameters(array('foo' => 'bar')), true, '->matchesParameters() can override a parameter that is not a pattern variable if the value is the same as the default one');

$route = new sfRoute('/:foo', array('bar' => 'foo'));
$t->is($route->matchesParameters(array('foo' => 'bar', 'bar' => 'foo')), true, '->matchesParameters() can override a parameter that is not a pattern variable if the value is the same as the default one');

$route = new sfRoute('/:foo');
$t->is($route->matchesParameters(array('foo' => 'bar', 'bar' => 'foo')), true, '->generate() matches even if there are extra parameters');

$route = new sfRoute('/:foo', array(), array(), array('extra_parameters_as_query_string' => false));
$t->is($route->matchesParameters(array('foo' => 'bar', 'bar' => 'foo')), false, '->generate() does not match if there are extra parameters if extra_parameters_as_query_string is set to false');

// ->generate()
$t->diag('->generate()');
$route = new sfRoute('/:foo');
$t->is($route->generate(array('foo' => 'bar')), '/bar', '->generate() generates a URL with the given parameters');
$route = new sfRoute('/:foo/:foobar');
$t->is($route->generate(array('foo' => 'bar', 'foobar' => 'barfoo')), '/bar/barfoo', '->generate() replaces longer variables first');

$route = new sfRoute('/:foo');
$t->is($route->generate(array('foo' => '')), '/', '->generate() generates a route if a variable is empty');
$t->is($route->generate(array('foo' => null)), '/', '->generate() generates a route if a variable is empty');
/*
$route = new sfRoute('/:foo/bar');
try
{
  $route->generate(array('foo' => ''));
  $t->fail('->generate() cannot generate a route if a variable is empty and mandatory');
}
catch (Exception $e)
{
  $t->pass('->generate() cannot generate a route if a variable is empty and mandatory');
}
try
{
  $route->generate(array('foo' => null));
  $t->fail('->generate() cannot generate a route if a variable is empty and mandatory');
}
catch (Exception $e)
{
  $t->pass('->generate() cannot generate a route if a variable is empty and mandatory');
}
*/
$route = new sfRoute('/:foo');
$t->is($route->generate(array('foo' => 'bar', 'bar' => 'foo')), '/bar?bar=foo', '->generate() generates extra parameters as a query string');

$route = new sfRoute('/:foo', array(), array(), array('extra_parameters_as_query_string' => false));
$t->is($route->generate(array('foo' => 'bar', 'bar' => 'foo')), '/bar', '->generate() ignores extra parameters if extra_parameters_as_query_string is false');

// checks that explicit 0 values also work - see #5175
$route = new sfRoute('/:foo', array(), array(), array('extra_parameters_as_query_string' => true));
$t->is($route->generate(array('foo' => 'bar', 'bar' => '0')), '/bar?bar=0', '->generate() adds extra parameters if extra_parameters_as_query_string is true');

$route = new sfRoute('/:foo/:bar', array('bar' => 'foo'));
$t->is($route->generate(array('foo' => 'bar')), '/bar', '->generate() generates the shortest URL possible');

$route = new sfRoute('/:foo/:bar', array('bar' => 'foo'), array(), array('generate_shortest_url' => false));
$t->is($route->generate(array('foo' => 'bar')), '/bar/foo', '->generate() generates the longest URL possible if generate_shortest_url is false');

// ->parseStarParameter()
$t->diag('->parseStarParameter()');
$route = new sfRoute('/foo/*');
$t->is($route->matchesUrl('/foo/foo/bar/bar/foo'), array('foo' => 'bar', 'bar' => 'foo'), '->parseStarParameter() parses * as key/value pairs');
$t->is($route->matchesUrl('/foo/foo/foo.bar'), array('foo' => 'foo.bar'), '->parseStarParameter() uses / as the key/value separator');
$t->is($route->matchesUrl('/foo'), array(), '->parseStarParameter() returns no additional parameters if the * value is empty');

$route = new sfRoute('/foo/*', array('module' => 'foo'));
$t->is($route->matchesUrl('/foo/foo/bar/module/barbar'), array('foo' => 'bar', 'module' => 'foo'), '->parseStarParameter() cannot override a default value');

$route = new sfRoute('/:foo/*');
$t->is($route->matchesUrl('/bar/foo/barbar'), array('foo' => 'bar'), '->parseStarParameter() cannot override pattern variables');

$route = new sfRoute('/foo/*/bar');
$t->is($route->matchesUrl('/foo/foo/bar/bar'), array('foo' => 'bar'), '->parseStarParameter() is able to parse a star in the middle of a rule');
$t->is($route->matchesUrl('/foo/bar'), array(), '->parseStarParameter() is able to parse a star if it is empty');

// ->generateStarParameter()
$t->diag('->generateStarParameter()');
$route = new sfRoute('/foo/:foo/*');
$t->is($route->generate(array('foo' => 'bar', 'bar' => 'foo')), '/foo/bar/bar/foo', '->generateStarParameter() replaces * with all the key/pair values that are not variables');

// custom token
$t->diag('custom token');

class MyRoute extends sfRoute
{
  protected function tokenizeBufferBefore(&$buffer, &$tokens, &$afterASeparator, &$currentSeparator)
  {
    if ($afterASeparator && preg_match('#^=('.$this->options['variable_regex'].')#', $buffer, $match))
    {
      // a labelled variable
      $this->tokens[] = array('label', $currentSeparator, $match[0], $match[1]);

      $currentSeparator = '';
      $buffer = substr($buffer, strlen($match[0]));
      $afterASeparator = false;
    }
    else
    {
      return false;
    }
  }

  protected function compileForLabel($separator, $name, $variable)
  {
    if (!isset($this->requirements[$variable]))
    {
      $this->requirements[$variable] = $this->options['variable_content_regex'];
    }

    $this->segments[] = preg_quote($separator, '#').$variable.$separator.'(?P<'.$variable.'>'.$this->requirements[$variable].')';
    $this->variables[$variable] = $name;

    if (!isset($this->defaults[$variable]))
    {
      $this->firstOptional = count($this->segments);
    }
  }

  protected function generateForLabel($optional, $tparams, $separator, $name, $variable)
  {
    if (!empty($tparams[$variable]) && (!$optional || !isset($this->defaults[$variable]) || $tparams[$variable] != $this->defaults[$variable]))
    {
      return $variable . '/' . urlencode($tparams[$variable]);
    }
  }
}

$route = new MyRoute('/=foo');
$t->is($route->matchesUrl('/foo/bar'), array('foo' => 'bar'), '->tokenizeBufferBefore() allows to add a custom token');
$t->is($route->generate(array('foo' => 'bar')), '/foo/bar', '->compileForLabel() adds logic to generate a route for a custom token');
