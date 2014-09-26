<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2);
class myPager extends sfPager
{
  function init() {}
  function retrieveObject($offset) {}
  function getResults() 
  {
    $this->setNbResults(2);
    return array("foo", "bar");
  }
}

$pager = new myPager("fooClass");

// #8021
// ->rewind()
$t->diag('->rewind()');
$countRuns = 0;
foreach ($pager as $item)
{
  $countRuns++;
}
$t->is($countRuns, $pager->count(), 'iterating first time will invoke on all items');

$countRuns = 0;
$pager->rewind();
foreach ($pager as $item)
{
  $countRuns++;
}
$t->is($countRuns, $pager->count(), '->rewind will allow reiterating');