<?php

/**
 * escaping actions.
 *
 * @package    project
 * @subpackage escaping
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 27755 2010-02-08 20:51:02Z Kris.Wallsmith $
 */
class escapingActions extends sfActions
{
  public function preExecute()
  {
    $this->var = 'Lorem <strong>ipsum</strong> dolor sit amet.';
    $this->setLayout(false);
    $this->setTemplate('index');
  }

  public function executeOn()
  {
    sfConfig::set('sf_escaping_strategy', true);
  }

  public function executeOff()
  {
    sfConfig::set('sf_escaping_strategy', false);
  }
}
