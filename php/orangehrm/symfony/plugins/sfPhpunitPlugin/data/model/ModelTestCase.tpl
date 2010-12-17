<?php

/**
 * {className}
 */
class {className} extends {parentName}
{
  /**
  * {modelClassName}
  *
  * @var {modelClassName}
  */
  protected $o;

  protected function _start()
  {
    $this->o = new {modelClassName}();
  }
  
  {methods}
  
  protected function _end()
  {
  }
}