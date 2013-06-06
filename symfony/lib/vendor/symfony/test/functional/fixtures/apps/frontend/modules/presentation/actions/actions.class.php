<?php

/**
 * presentation actions.
 *
 * @package    project
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 4937 2007-08-30 08:40:10Z fabien $
 */
class presentationActions extends sfActions
{
  public function executeIndex()
  {
    $this->foo = $this->getController()->getPresentationFor('presentation', 'foo');
  }

  public function executeFoo()
  {
    $this->setLayout(false);
  }
}
