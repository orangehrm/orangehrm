<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');
require_once(dirname(__FILE__).'/../../../lib/helper/PartialHelper.php');

// Fixme: make this test more beautiful and extend it

$t = new lime_test(2, new lime_output_color());

class MyTestPartialView extends sfPartialView
{
  public function render()
  {
    return '==RENDERED==';
  }

  public function initialize($context, $moduleName, $actionName, $viewName)
  {
  }

  public function setPartialVars(array $partialVars)
  {
  }
}

$t->diag('get_partial()');
sfConfig::set('mod_module_partial_view_class', 'MyTest');

$t->is(get_partial('module/dummy'), '==RENDERED==', 'get_partial() uses the class specified in partial_view_class for the given module');
$t->is(get_partial('MODULE/dummy'), '==RENDERED==', 'get_partial() accepts a case-insensitive module name');
