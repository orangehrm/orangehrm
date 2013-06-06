<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(8);

class FormListener
{
  public $events = array();

  public function listen(sfEvent $event)
  {
    $this->events[] = func_get_args();
  }

  public function filter(sfEvent $event, $value)
  {
    $this->events[] = func_get_args();

    return $value;
  }

  public function reset()
  {
    $this->events = array();
  }
}

$listener = new FormListener();
$dispatcher = new sfEventDispatcher();

$dispatcher->connect('form.post_configure', array($listener, 'listen'));
$dispatcher->connect('form.filter_values', array($listener, 'filter'));
$dispatcher->connect('form.validation_error', array($listener, 'listen'));

sfFormSymfony::setEventDispatcher($dispatcher);

class TestForm extends sfFormSymfony
{
  public function configure()
  {
    $this->setValidators(array(
      'first_name' => new sfValidatorString(),
      'last_name'  => new sfValidatorString(),
    ));
  }
}

// ->__construct()
$t->diag('->__construct()');

$listener->reset();
$form = new TestForm();
$t->is(count($listener->events), 1, '->__construct() notifies one event');
$t->is($listener->events[0][0]->getName(), 'form.post_configure', '->__construct() notifies the "form.post_configure" event');

// ->bind()
$t->diag('->bind()');

$form = new TestForm();
$listener->reset();
$form->bind(array(
  'first_name' => 'John',
  'last_name'  => 'Doe',
));

$t->is(count($listener->events), 1, '->bind() notifies one event when validation is successful');
$t->is($listener->events[0][0]->getName(), 'form.filter_values', '->bind() notifies the "form.filter_values" event');
$t->is_deeply($listener->events[0][1], array('first_name' => 'John', 'last_name' => 'Doe'), '->bind() filters the tainted values');

$form = new TestForm();
$listener->reset();
$form->bind();

$t->is(count($listener->events), 2, '->bind() notifies two events when validation fails');
$t->is($listener->events[1][0]->getName(), 'form.validation_error', '->bind() notifies the "form.validation_error" event');
$t->isa_ok($listener->events[1][0]['error'], 'sfValidatorErrorSchema', '->bind() notifies the error schema');
