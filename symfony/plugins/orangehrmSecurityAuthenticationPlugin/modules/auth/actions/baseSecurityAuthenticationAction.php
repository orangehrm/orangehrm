<?php

abstract class baseSecurityAuthenticationAction extends sfAction {

    private $securityAuthenticationService;

    /**
     *
     * @return SecurityAuthenticationServiceTest
     */
    protected function getSecurityAuthenticationService() {
        if (is_null($this->securityAuthenticationService)) {
            $this->securityAuthenticationService = new SecurityAuthenticationService();
        }

        return $this->securityAuthenticationService;
    }


}

