<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// helper class to test the event dispatcher
class sfEventDispatcherTest
{
  protected $t = null;

  public function __construct($testObject)
  {
    $this->t = $testObject;
  }

  public function launchTests($dispatcher, $object, $class)
  {
    $this->t->diag('New methods via sfEventDispatcher');
    $dispatcher->connect($class.'.method_not_found', array('myEventDispatcherTest', 'newMethod'));
    $this->t->is($object->newMethod('ok'), 'ok', '__call() accepts new methods via sfEventDispatcher');

    try
    {
      $object->nonexistantmethodname();
      $this->t->fail('__call() throws an exception if the method does not exist as a sfEventDispatcher listener');
    }
    catch (sfException $e)
    {
      $this->t->pass('__call() throws an exception if the method does not exist as a sfEventDispatcher listener');
    }
  }
}

class myEventDispatcherTest
{
  static public function newMethod(sfEvent $event)
  {
    if ($event['method'] == 'newMethod')
    {
      $arguments = $event['arguments'];
      $event->setReturnValue($arguments[0]);

      return true;
    }
  }
}
