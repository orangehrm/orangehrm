<?php
class zTask extends sfPropelBaseTask 
{
  public function configure()
  {
    $this->namespace = 'z';
    $this->name = 'run';
  }
  public function execute($arguments = array(), $options = array())
  {
  }
}