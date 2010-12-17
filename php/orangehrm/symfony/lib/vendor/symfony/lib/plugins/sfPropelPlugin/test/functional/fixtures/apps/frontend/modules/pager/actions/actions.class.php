<?php

class pagerActions extends sfActions
{
  public function executeInterfaces()
  {
    $this->pager = new sfPropelPager('Article');
    $this->pager->init();
  }
}
