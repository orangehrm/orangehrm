<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(10, new lime_output_color());

class myClass
{
  public $ret = '';
  static public $retStatic = '';

  public function myMethod()
  {
    $this->ret  = "before myMethod\n";
    sfMixer::callMixins();
    $this->ret .= "after myMethod\n";

    return $this->ret;
  }

  public function myMethodWithSeveralHooks()
  {
    $this->ret  = "before myMethodWithSeveralHooks\n";
    sfMixer::callMixins('before');

    $this->ret .= "myMethodWithSeveralHooks\n";
    sfMixer::callMixins();

    $this->ret .= "after myMethodWithSeveralHooks\n";
    sfMixer::callMixins('after');

    return $this->ret;
  }

  public function myMethodWithArgs($arg1, $arg2 = 'default')
  {
    $this->ret  = "before myMethodWithArgs\n";
    sfMixer::callMixins();
    $this->ret .= "after myMethodWithArgs\n";

    return $this->ret;
  }

  static public function myStaticMethod()
  {
    self::$retStatic = "before myStaticMethod\n";
    sfMixer::callMixins();
    self::$retStatic .= "after myStaticMethod\n";

    return self::$retStatic;
  }

  static public function myStaticMethodWithArgs($arg1, $arg2 = 'default')
  {
    self::$retStatic = "before myStaticMethodWithArgs\n";
    sfMixer::callMixins();
    self::$retStatic .= "after myStaticMethodWithArgs\n";

    return self::$retStatic;
  }

  function __call($method, $arguments)
  {
    $r  = "before __call\n";
    $r .= sfMixer::callMixins();
    $r .= "after __call\n";

    return $r;
  }
}

$m = new myClass();

$t->is($m->myMethod(), "before myMethod\nafter myMethod\n", 'method call without mixins');
$t->is(myClass::myStaticMethod(), "before myStaticMethod\nafter myStaticMethod\n", 'static method call without mixins');

try
{
  $m->newMethod();
  $t->fail('method call that does not exist');
}
catch (Exception $e)
{
  $t->pass('method call that does not exist');
}

class myClassMixins
{
  public function myMixinMethod($object)
  {
    $object->ret .= "in myMethod mixin method\n";
  }

  public function myMethodWithSeveralHooks($object)
  {
    $object->ret .= "in myMethodWithSeveralHooks mixin method for default hook\n";
  }

  public function myMethodWithSeveralHooksBefore($object)
  {
    $object->ret .= "in myMethodWithSeveralHooks mixin method for before hook\n";
  }

  public function myMethodWithSeveralHooksAfter($object)
  {
    $object->ret .= "in myMethodWithSeveralHooks mixin method for after hook\n";
  }

// TODO
  public function myStaticMixinMethod($object)
  {
    $object->ret .= "in myStaticMethod mixin method\n";
  }

  public function myMixinMethodWithArgs($object, $arg1, $arg2 = 'default')
  {
    $object->ret .= "in myMethodWithArgs mixin method ($arg1, $arg2)\n";
  }

  public function myMixinStaticMethod()
  {
    myClass::$retStatic .= "in myStaticMethod mixin method\n";
  }

  public function myMixinStaticMethodWithArgs($class, $arg1, $arg2 = 'default')
  {
    myClass::$retStatic .= "in myStaticMethodWithArgs mixin method ($arg1, $arg2)\n";
  }

  public function newMethod($object)
  {
    return "in newMethod mixin method\n";
  }

  public function newMethodWithArgs($object, $arg1, $arg2 = 'default')
  {
    return "in newMethodWithArgs mixin method ($arg1, $arg2)\n";
  }
}

sfMixer::register('myClass:myMethod', array('myClassMixins', 'myMixinMethod'));
sfMixer::register('myClass:myStaticMethod', array('myClassMixins', 'myMixinStaticMethod'));

$t->is($m->myMethod(), "before myMethod\nin myMethod mixin method\nafter myMethod\n", 'method call with a mixin');
$t->is(myClass::myStaticMethod(), "before myStaticMethod\nin myStaticMethod mixin method\nafter myStaticMethod\n", 'static method call with a mixin');

sfMixer::register('myClass:myMethodWithArgs', array('myClassMixins', 'myMixinMethodWithArgs'));
$t->is($m->myMethodWithArgs('value'), "before myMethodWithArgs\nin myMethodWithArgs mixin method (value, default)\nafter myMethodWithArgs\n", 'method call with arguments with a mixin');

sfMixer::register('myClass:myStaticMethodWithArgs', array('myClassMixins', 'myMixinStaticMethodWithArgs'));
$t->is(myClass::myStaticMethodWithArgs('value'), "before myStaticMethodWithArgs\nin myStaticMethodWithArgs mixin method (value, default)\nafter myStaticMethodWithArgs\n", 'static method call with arguments with a mixin');

sfMixer::register('myClass', array('myClassMixins', 'newMethod'));
$t->is($m->newMethod(), "before __call\nin newMethod mixin method\nafter __call\n", 'method call from a mixin');

sfMixer::register('myClass', array('myClassMixins', 'newMethodWithArgs'));
$t->is($m->newMethodWithArgs('value'), "before __call\nin newMethodWithArgs mixin method (value, default)\nafter __call\n", 'method call from a mixin with arguments');

sfMixer::register('myClass:myMethodWithSeveralHooks:before', array('myClassMixins', 'myMethodWithSeveralHooksBefore'));
sfMixer::register('myClass:myMethodWithSeveralHooks', array('myClassMixins', 'myMethodWithSeveralHooks'));
sfMixer::register('myClass:myMethodWithSeveralHooks:after', array('myClassMixins', 'myMethodWithSeveralHooksAfter'));
$t->is($m->myMethodWithSeveralHooks(), "before myMethodWithSeveralHooks\nin myMethodWithSeveralHooks mixin method for before hook\nmyMethodWithSeveralHooks\nin myMethodWithSeveralHooks mixin method for default hook\nafter myMethodWithSeveralHooks\nin myMethodWithSeveralHooks mixin method for after hook\n", 'method call with several registered hooks');
