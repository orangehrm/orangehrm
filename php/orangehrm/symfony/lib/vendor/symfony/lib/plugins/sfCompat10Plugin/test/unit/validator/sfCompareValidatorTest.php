<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/../../../../test/unit/sfContextMock.class.php');
require_once(dirname(__FILE__).'/sfValidatorTestHelper.class.php');

$t = new lime_test(55, new lime_output_color());

class sfRequest
{
  public $parameters = array();

  function getParameter($key)
  {
    return $this->parameters[$key];
  }
}

$context = sfContext::getInstance(array('request' => 'sfRequest'));
$request = $context->request;

$h = new sfValidatorTestHelper($context, $t);

// check exceptions
try
{
  $v = new sfCompareValidator($context);
  $t->fail('->initialize() takes a required "check" parameter');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() takes a required "check" parameter');
}

try
{
  $v = new sfCompareValidator($context, array('check' => 'value', 'operator' => 'N'));
  $t->fail('->initialize() takes an "operator" parameter in (>, >=, <, <=, ==, !=)');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() takes an "operator" parameter in (>, >=, <, <=, ==, !=)');
}

// == operator (default operator)
$t->diag('->execute() - == operator (default operator)');
$v = new sfCompareValidator($context, array('check' => 'value'));
$request->parameters = array('value2' => 'azerty');
$options = array('check' => 'value2');
$h->launchTests($v, 'azerty', true, 'check', null, $options);
$h->launchTests($v, 'azerty1', false, 'check', null, $options);
$h->launchTests($v, 'azerty1', false, 'check', 'compare_error', $options);

// == operator
$t->diag('->execute() - == operator');
$request->parameters = array('value2' => 'azerty');
$options = array('check' => 'value2', 'operator' => '==');
$h->launchTests($v, 'azerty', true, 'check', null, $options);
$h->launchTests($v, 'azerty1', false, 'check', null, $options);
$h->launchTests($v, 'azerty1', false, 'check', 'compare_error', $options);

// != operator
$t->diag('->execute() - != operator');
$request->parameters = array('value2' => 'azerty');
$options = array('check' => 'value2', 'operator' => '!=');
$h->launchTests($v, 'azerty1', true, 'check', null, $options);
$h->launchTests($v, 'azerty', false, 'check', null, $options);
$h->launchTests($v, 'azerty', false, 'check', 'compare_error', $options);

// > operator
$t->diag('->execute() - > operator');
$request->parameters = array('value2' => 10);
$options = array('check' => 'value2', 'operator' => '>');
$h->launchTests($v, 20, true, 'check', null, $options);
$h->launchTests($v, 10, false, 'check', null, $options);
$h->launchTests($v, 5, false, 'check', 'compare_error', $options);

// >= operator
$t->diag('->execute() - >= operator');
$request->parameters = array('value2' => 10);
$options = array('check' => 'value2', 'operator' => '>=');
$h->launchTests($v, 20, true, 'check', null, $options);
$h->launchTests($v, 10, true, 'check', null, $options);
$h->launchTests($v, 5, false, 'check', null, $options);
$h->launchTests($v, 5, false, 'check', 'compare_error', $options);

// < operator
$t->diag('->execute() - < operator');
$request->parameters = array('value2' => 1);
$options = array('check' => 'value2', 'operator' => '<');
$h->launchTests($v, 0, true, 'check', null, $options);
$h->launchTests($v, 1, false, 'check', null, $options);
$h->launchTests($v, 10, false, 'check', 'compare_error', $options);

// <= operator
$t->diag('->execute() - <= operator');
$request->parameters = array('value2' => 10);
$options = array('check' => 'value2', 'operator' => '<=');
$h->launchTests($v, 5, true, 'check', null, $options);
$h->launchTests($v, 10, true, 'check', null, $options);
$h->launchTests($v, 15, false, 'check', null, $options);
$h->launchTests($v, 15, false, 'check', 'compare_error', $options);
