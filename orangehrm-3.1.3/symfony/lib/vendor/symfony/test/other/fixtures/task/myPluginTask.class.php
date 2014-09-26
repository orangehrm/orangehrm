<?php
class myPluginTask extends sfBaseTask 
{
  public function configure()
  {
    $this->namespace = 'p';
    $this->name = 'run';
  }
  public function execute($arguments = array(), $options = array())
  {
  }
}