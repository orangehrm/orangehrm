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

    public function executeIndex(sfWebRequest $request) {
        $this->forward('auth', 'login');
    }

}
