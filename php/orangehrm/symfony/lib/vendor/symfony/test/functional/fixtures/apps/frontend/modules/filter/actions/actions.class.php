<?php

/**
 * filter actions.
 *
 * @package    project
 * @subpackage filter
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 2569 2006-10-30 16:36:31Z fabien $
 */
class filterActions extends sfActions
{
  public function executeIndex()
  {
    return $this->renderText('foo');
  }

  public function executeIndexWithForward()
  {
    $this->forward('filter', 'index');
  }
}
