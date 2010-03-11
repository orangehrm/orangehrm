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

$t = new lime_test(17, new lime_output_color());

class myFilter extends sfFilter
{
  public function isFirstCall()
  {
    return parent::isFirstCall();
  }
}

$context = sfContext::getInstance();
$filter = new myFilter($context);

// ->initialize()
$t->diag('->initialize()');
$filter = new myFilter($context);
$t->is($filter->getContext(), $context, '->initialize() takes a sfContext object as its first argument');
$filter->initialize($context, array('foo' => 'bar'));
$t->is($filter->getParameter('foo'), 'bar', '->initialize() takes an array of parameters as its second argument');

// ->getContext()
$t->diag('->getContext()');
$filter->initialize($context);
$t->is($filter->getContext(), $context, '->getContext() returns the current context');

// ->isFirstCall()
$t->diag('->isFirstCall()');
$t->is($filter->isFirstCall('beforeExecution'), true, '->isFirstCall() returns true if this is the first call with this argument');
$t->is($filter->isFirstCall('beforeExecution'), false, '->isFirstCall() returns false if this is not the first call with this argument');
$t->is($filter->isFirstCall('beforeExecution'), false, '->isFirstCall() returns false if this is not the first call with this argument');

$filter = new myFilter($context);
$filter->initialize($context);
$t->is($filter->isFirstCall('beforeExecution'), false, '->isFirstCall() returns false if this is not the first call with this argument');

// parameter holder proxy
require_once($_test_dir.'/unit/sfParameterHolderTest.class.php');
$pht = new sfParameterHolderProxyTest($t);
$pht->launchTests($filter, 'parameter');
