<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class sfValidatorTestHelper
{
  protected $t = null;
  protected $context = null;

  public function __construct($context, $testObject)
  {
    $this->t = $testObject;
    $this->context = $context;
  }


  function launchTests($validator, $value, $retval, $main_param, $main_error_param, $parameters)
  {
    $t = $this->t;

    $error = null;
    $validator->initialize($this->context, $parameters);
    $t->is($validator->execute($value, $error), $retval, sprintf('->execute() accepts a "%s" parameter', $main_param));
    if (false === $retval)
    {
      $t->isnt($error, null, '->execute() changes "$error" with a default message if it returns false');
    }
    else
    {
      $t->is($error, null, '->execute() doesn\'t change "$error" if it returns true');
    }

    // test error customization
    if (null !== $main_error_param && false === $retval)
    {
      $validator->initialize($this->context, array_merge($parameters, array($main_error_param => 'my custom error message')));
      $validator->execute($value, $error);
      $t->is($error, 'my custom error message', sprintf('->execute() changes "$error" with a custom message from "%s" parameter', $main_error_param));
    }
  }
}
