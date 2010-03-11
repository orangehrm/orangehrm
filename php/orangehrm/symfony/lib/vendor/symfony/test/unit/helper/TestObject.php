<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestObject
{
  protected $value = 'value';
  protected $text  = 'text';

  public function getValue()
  {
    return $this->value;
  }

  public function getText()
  {
    return $this->text;
  }

  public function setText($text)
  {
    $this->text = $text;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getBooleanTrue()
  {
    return true;
  }

  public function getBooleanFalse()
  {
    return false;
  }
}
