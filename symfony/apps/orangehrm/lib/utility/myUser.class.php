<?php

class myUser extends sfBasicSecurityUser {

    private $dateFormat;
    private $timeFormat;

    /**
     * Get date format for user
     *
     * @return String Date Format string. (see sfDateFormat)
     */
    public function getDateFormat() {
        return $this->dateFormat;
    }

    /**
     * Set date format for user
     * @param String Date Format string. (see sfDateFormat)
     * @return
     */
    public function setDateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Get time format for user
     * @return String Time Format string. (see sfDateFormat)
     */
    public function getTimeFormat() {
        return $this->timeFormat;
    }

    /**
     * Set time format for user
     * @param String Time Format string. (see sfDateFormat)
     * @return
     */
    public function setTimeFormat($timeFormat) {
        $this->timeFormat = $timeFormat;
    }

    /**
     * Add a credential to this user.
     * Not implemented
     *
     * @param mixed $credential Credential data.
     */
    public function addCredential($credential) {
        
    }

    /**
     * Clear all credentials associated with this user.
     * Not implemented
     */
    public function clearCredentials() {
        
    }

    /**
     * Taken from sfBasicSecurityUser:
     * Returns true if user has credential.
     *
     * @param  mixed $credentials
     * @param  bool  $useAnd       specify the mode, either AND or OR
     * @return bool
     *
     * @author Olivier Verdier <Olivier.Verdier@free.fr>
     */
    public function hasCredential($credentials, $useAnd = true) {

        $auth = Auth::instance();

        // If not an array, check now and return    
        if (!is_array($credentials)) {
            return $auth->hasRole($credentials);
        }

        // true for empty arrays
        $result = true;

        foreach ($credentials as $credential) {

            // recursively check the credential with a switched AND/OR mode
            if ($this->hasCredential($credential, $useAnd ? false : true)) {

                // lazy OR
                if (!$useAnd) {
                    return true;
                }
            } else {
                if ($useAnd) {
                    return false; // lazy AND
                } else {
                    $result = false; // save for OR
                }
            }
        }

        return $result;
    }

    /**
     * Indicates whether or not this user is authenticated.
     *
     * @return bool true, if this user is authenticated, otherwise false.
     */
    public function isAuthenticated() {

        if (sfContext::getInstance()->getModuleName() == 'recruitmentApply') {
            return true;
        } else {
            // Checks orangehrm session variable
            return isset($_SESSION['user']);
        }
    }

    /**
     * Remove a credential from this user.
     * Not implemented
     * @param mixed $credential  Credential data.
     */
    public function removeCredential($credential) {
        
    }

    /**
     * Set the authenticated status of this user.
     * Not implemented
     * @param bool $authenticated  A flag indicating the authenticated status of this user.
     */
    public function setAuthenticated($authenticated) {
        
    }

    public function getEmployeeNumber() {
        $auth = Auth::instance();
        return $auth->getEmployeeNumber();
    }
    
    public function getUserTimeZoneOffset() {
        return $this->getAttribute('system.timeZoneOffset', 0);
    }
    
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);
        if ($this->isTimedOut()) {
            $authService = new AuthenticationService();
            $authService->clearCredentials();    
            $_SESSION = array();
        }
    }

}
