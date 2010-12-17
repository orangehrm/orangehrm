<?php

/**
 * renderText actions.
 *
 * @package    project
 * @subpackage renderText
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 2895 2006-12-04 12:40:19Z fabien $
 */
class renderTextActions extends sfActions
{
  public function executeIndex()
  {
    return $this->renderText('foo');
  }
}
