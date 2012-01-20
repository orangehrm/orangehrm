<?php

/**
 * default actions.
 *
 * @package    orangehrm
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class defaultActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }
  
  /**
 * Warning page for restricted area - requires login
 *
 */
	public function executeSecure()
	{
	}

    public function executeError404() {
        
    }
  
}
