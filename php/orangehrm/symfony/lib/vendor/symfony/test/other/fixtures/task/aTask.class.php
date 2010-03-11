<?php
class aTask extends sfPropelBaseTask 
{
  public function configure()
  {
    $this->namespace = 'a';
    $this->name = 'run';
  }
  public function execute($arguments = array(), $options = array())
  {
  }
}