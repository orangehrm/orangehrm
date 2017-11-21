<?php

/**
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
class SecurityAuthenticationConfigService extends ConfigService
{

    private $configValues;
    private $defaultPasswordStrengths = array("veryWeak", "weak", "better", "medium", "strong", "strongest");
    private $defaultPasswordStrengthsWithViewValues = array("veryWeak" => "Very Weak", "weak" => "Weak", "better" => "Better", "medium" => "Medium", "strong" => "Strong", "strongest" => "Strongest");

    const KEY_SECURITY_AUTHENTICATION = 'security_authentication';

    const STATUS = 'authentication.status';
    const ENFORCE_PASSWORD_STRENGTH = 'authentication.enforce_password_strength';
    const DEFAULT_REQUIRED_PASSWORD_STRENGTH = 'authentication.default_required_password_strength';

    /**
     *
     * @return bool
     */
    public function isPluginEnabled()
    {
        return ($this->_getConfigValue(self::STATUS) == 'Enable');
    }

    /**
     *
     * @return bool
     */
    public function getAuthenticationStatus()
    {
        return $this->_getConfigValue(self::STATUS);
    }

    /**
     * Returns whether auto reset password is enable for user creation
     * @return bool
     */
    public function isPasswordStengthEnforced()
    {
        return $this->isPluginEnabled() && ($this->_getConfigValue(self::ENFORCE_PASSWORD_STRENGTH) == 'on');
    }

    /**
     * Returns whether auto reset password is enable for user creation
     * @return bool
     */
    public function getRequiredPasswordStength()
    {
        $configValue = $this->_getConfigValue(self::DEFAULT_REQUIRED_PASSWORD_STRENGTH);
        $strengthIndex = array_search($configValue, $this->defaultPasswordStrengths);
        if ($strengthIndex) {
            return $strengthIndex;
        }
        return 0;
    }

    public function getCurrentPasswordStrength()
    {
        return $this->defaultPasswordStrengths[$this->getRequiredPasswordStength()];
    }

    public function getPasswordStrengths()
    {
        return $this->defaultPasswordStrengths;
    }

    public function getPasswordStrengthsWithViewValues()
    {
        return $this->defaultPasswordStrengthsWithViewValues;
    }


    /**
     * Setters
     */

    /**
     * @param string $value
     * @return boolean
     */
    public function setAuthenticationStatus($value)
    {
        return $this->_setConfigValue(self::STATUS, $value);
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function setEnforcePasswordStrength($value)
    {
        return $this->_setConfigValue(self::ENFORCE_PASSWORD_STRENGTH, $value);
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function setDefaultRequiredPasswordStrength($value)
    {
        return $this->_setConfigValue(self::DEFAULT_REQUIRED_PASSWORD_STRENGTH, $value);
    }


}

