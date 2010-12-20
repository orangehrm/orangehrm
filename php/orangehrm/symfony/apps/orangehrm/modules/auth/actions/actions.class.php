<?php

/**
 * auth actions.
 *
 * @package    orangehrm
 * @subpackage auth
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class authActions extends sfActions {
    
 /**
  * Login action. Forwards to OrangeHRM login page if not already logged in.
  *
  * @param sfRequest $request A request object
  */
  public function executeLogin(sfWebRequest $request) {
      
      $this->getContext()->getConfiguration()->loadHelpers('Url');
      $this->redirect(public_path('../../login.php'));
  }
  
}
