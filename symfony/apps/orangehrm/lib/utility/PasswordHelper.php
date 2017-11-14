<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */ 
 



/**
 * Password Helper
 */

use ZxcvbnPhp\Zxcvbn;
class PasswordHelper {

    const VERY_WEAK = 0;
    const WEAK = 1;
    const BETTER = 2;
    const MEDIUM = 3;
    const STRONG = 4;
    const STRONGEST = 5;

    const PASSWORD_MAX_LENGTH = 64;
    const PASSWORD_MIN_LENGTH = 8;

    private $authenticationConfigService;
	
	public function getPasswordStrength($password){
        if(isset($password) && !empty($password)){
            $strength = $this->calculatePasswordStrength($password);
             if ($strength >= self::MEDIUM) {
                 $allCharacterClassesIncluded = $this->checkForDifferentCharacterClasses($password);
                 if (!$allCharacterClassesIncluded) {
                     return self::BETTER;
                 }
                 return $strength;
             }
             return $strength;
        }
        return self::VERY_WEAK;
    }

    public function calculatePasswordStrength($password){
        $zxcvbn = new Zxcvbn();
        $strength = $zxcvbn->passwordStrength($password);
        if($strength['crack_time']< pow(10, 0))return self::VERY_WEAK;
        if($strength['crack_time']< pow(10, 2))return self::WEAK;
        if($strength['crack_time']< pow(10, 4))return self::BETTER;
        if($strength['crack_time']< pow(10, 6))return self::MEDIUM;
        if($strength['crack_time']< pow(10, 9))return self::STRONG;
        if($strength['crack_time']>= pow(10, 9))return self::STRONGEST;
        return self::VERY_WEAK;
    }

    /**
     * Checks that the password contains at least one character from each of the following character sets:
     * 1. lower case characters
     * 2. upper case characters
     * 3. digits
     * 4. special characters
     *
     * @return true if password contains one character from each set.
     */
    protected function checkForDifferentCharacterClasses($password) {

        $score = 0;

        if (preg_match('/[a-z]/', $password)) { // contains lower case characters
            $score++;
        }
        if (preg_match('/[A-Z]/', $password)) { // contains upper case characters
            $score++;
        }

        if (preg_match('/\d/', $password)) { // contains digit(s)
            $score++;
        }

        if (preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $password)) { // contains special character(s)
            $score++;
        }

        return $score == 4;
    }

    public function isPasswordStrongWithEnforcement($password){
	    if($this->getSecurityAuthenticationConfigService()->isPasswordStengthEnforced()){
            return $this->getPasswordStrength($password)>=$this->getSecurityAuthenticationConfigService()->getRequiredPasswordStength() && strlen($password)>=self::PASSWORD_MIN_LENGTH;
        }
        return true;
    }

    public function getColorClass($score){
        return $this->getSecurityAuthenticationConfigService()->getPasswordStrengths()[$score];
    }

    /**
     *
     * @return SecurityAuthenticationConfigService
     */
    public function getSecurityAuthenticationConfigService() {
        if (!isset($this->authenticationConfigService)) {
            $this->authenticationConfigService = new SecurityAuthenticationConfigService();
        }
        return $this->authenticationConfigService;
    }

    /**
     * @param mixed SecurityAuthenticationConfigService
     */
    public function setSecurityAuthenticationConfigService($authenticationConfigService)
    {
        $this->authenticationConfigService = $authenticationConfigService;
    }

    /**
     * @param string $password
     * @return string validationMsg
     */
    public function getCustomValidationBasedOnPassword($password)
    {
        if(strlen($password) >= self::PASSWORD_MIN_LENGTH && strlen($password) < self::PASSWORD_MAX_LENGTH){
            if(!$this->isPasswordStrongWithEnforcement($password)) {
                if ($this->checkForDifferentCharacterClasses($password)) {
                    return __("Your password meets the minimum requirements, but it could be guessable. Try a different password.");
                }else{
                    return __("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");
                }
            }
        }
        return "";

    }
}
