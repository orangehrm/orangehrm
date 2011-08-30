<?php

/**
 * Login action.
 */
class loginAction extends sfAction {

    /**
     * @param sfRequest $request A request object
     */
    public function execute($request) {
        $this->form = new LoginForm();
    }

}

