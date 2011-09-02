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
  
    /**
     * Show not authorized message
     * @return boolean true if successfully deleted, false otherwise
     */
    public function executeUnauthorized(sfWebRequest $request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $response = $this->getResponse();
        $response->setStatusCode(401, 'Not authorized');        
        return $this->renderText("You do not have the proper credentials to access this page!");
    }   
}
