<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
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
            return $this->getPasswordStrength($password) >= $this->getSecurityAuthenticationConfigService()->getRequiredPasswordStength() && strlen($password)>=self::PASSWORD_MIN_LENGTH;
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
