<?php

//class myUser extends sfBasicSecurityUser
class myUser extends sfUser implements sfSecurityUser {
    

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
          if (! $useAnd ) {
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

      // Checks orangehrm session variable
     return isset($_SESSION['fname']);
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
      
}
