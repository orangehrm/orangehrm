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

$t = new lime_test(19);

class myView extends sfView
{
  function execute() {}
  function configure() {}
  function getEngine() {}
  function render() {}
}

class configuredView extends myView
{
  static public $isDecorated = false;

  function initialize($context, $moduleName, $actionName, $viewName)
  {
    $this->setDecorator(self::$isDecorated);

    parent::initialize($context, $moduleName, $actionName, $viewName);
  }
}

$context = sfContext::getInstance(array('request' => 'sfWebRequest', 'response' => 'sfWebResponse'));

$view = new myView($context, '', '', '');

// ->isDecorator() ->setDecorator()
$t->diag('->isDecorator() ->setDecorator()');
$t->is($view->isDecorator(), false, '->isDecorator() returns true if the current view have to be decorated');
$view->setDecorator(true);
$t->is($view->isDecorator(), true, '->setDecorator() sets the decorator status for the view');

// format
$t->diag('format');
$context = sfContext::getInstance(array('request' => 'sfWebRequest', 'response' => 'sfWebResponse'), true);
$context->getRequest()->setFormat('js', 'application/x-javascript');
$context->getRequest()->setRequestFormat('js');
configuredView::$isDecorated = true;
$view = new configuredView($context, '', '', '');
$t->is($view->isDecorator(), false, '->initialize() uses the format to configure the view');
$t->is($context->getResponse()->getContentType(), 'application/x-javascript', '->initialize() uses the format to configure the view');
$t->is($view->getExtension(), '.js.php', '->initialize() uses the format to configure the view');
$context = sfContext::getInstance(array('request' => 'sfWebRequest', 'response' => 'sfWebResponse'), true);
$context->getEventDispatcher()->connect('view.configure_format', 'configure_format');

$context->getRequest()->setRequestFormat('js');
configuredView::$isDecorated = true;
$view = new configuredView($context, '', '', '');
$t->is($view->isDecorator(), true, '->initialize() uses the format to configure the view');
$t->is($context->getResponse()->getContentType(), 'application/javascript', '->initialize() uses the format to configure the view');

// parameter holder proxy
require_once($_test_dir.'/unit/sfParameterHolderTest.class.php');
$pht = new sfParameterHolderProxyTest($t);
$pht->launchTests($view, 'parameter');

// new methods via sfEventDispatcher
require_once($_test_dir.'/unit/sfEventDispatcherTest.class.php');
$dispatcherTest = new sfEventDispatcherTest($t);
$dispatcherTest->launchTests($context->getEventDispatcher(), $view, 'view');

function configure_format(sfEvent $event)
{
  $event->getSubject()->setDecorator(true);
  $event['response']->setContentType('application/javascript');

  return true;
}
