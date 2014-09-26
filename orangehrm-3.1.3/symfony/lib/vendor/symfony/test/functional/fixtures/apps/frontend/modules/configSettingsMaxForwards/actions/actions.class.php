<?php

/**
 * configSettingsMaxForwards actions.
 *
 * @package    project
 * @subpackage configSettingsMaxForwards
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class configSettingsMaxForwardsActions extends sfActions
{
  public function executeSelfForward()
  {
    $this->forward('configSettingsMaxForwards', 'selfForward');
  }
}
