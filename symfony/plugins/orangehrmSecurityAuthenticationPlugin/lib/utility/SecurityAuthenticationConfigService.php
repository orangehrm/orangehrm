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
//    const USER_CAN_RESET = 'authentication.user_can_reset';
//    const ENABLE_CAPTCHA = 'authentication.enable_captcha';
//    const BLOCK_ACCESS = 'authentication.block_access';
//    const ATTEMPTS_FOR_CAPTCHA = 'authentication.attempts_for_captcha';
//    const ATTEMPTS_FOR_BLOCK = 'authentication.attempts_for_block';
//    const BLOCKED_DURATION = 'authentication.blocked_duration';
//    const SECONDARY_PASSWORD_ENABLED = 'authentication.secondary_password_enabled';
//    const PASSWORD_EXPIRATION_ENABLED = 'authentication.password_expiration_enabled';
//    const NUM_OF_MONTHS_FOR_PASSWORD_EXPIRATION = 'authentication.num_of_months_for_password_expiration';
//    const ADMIN_CAN_RESET = 'authentication.admin_can_reset';
//    const NOTIFY_EMPLOYEE_AFTER_USER_CREATION = 'authentication.notify_employee_after_user_creation';
    const ENFORCE_PASSWORD_STRENGTH = 'authentication.enforce_password_strength';
    const DEFAULT_REQUIRED_PASSWORD_STRENGTH = 'authentication.default_required_password_strength';

//    const DEFAULT_ATTEMPTS_FOR_CAPTCHA = 3;

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
     * @return bool
     */
//    public function isCAPTCHAEnabled()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::ENABLE_CAPTCHA) == 'on');
//    }

    /**
     *
     * @return bool
     */
//    public function isPasswordResetEnabled()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::USER_CAN_RESET) == 'on');
//    }

    /**
     *
     * @return bool
     */
//    public function isBlockingEnabled()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::BLOCK_ACCESS) == 'on');
//    }

    /**
     *
     * @return int
     */
//    public function getAttemptsToBlock()
//    {
//        if ($this->isBlockingEnabled()) {
//            return $attemptsToBlock = (int)$this->_getConfigValue(self::ATTEMPTS_FOR_BLOCK);
//        } else {
//            return 999;
//        }
//    }

//    public function getAttemptsToBlockConfig()
//    {
//        return (int)$this->_getConfigValue(self::ATTEMPTS_FOR_BLOCK);
//    }

    /**
     *
     * @return int
     */
//    public function getAttemptsToCAPTCHA()
//    {
//        $configValue = $this->_getConfigValue(self::ATTEMPTS_FOR_CAPTCHA);
//        if (isset($configValue) && !empty($configValue)) {
//            return (int)$configValue;
//        }
//        return self::DEFAULT_ATTEMPTS_FOR_CAPTCHA;
//    }

    /**
     *
     * @param bool $formatted
     * @return string
     */
//    public function getDisabledTime($formatted = true)
//    {
//        $disabledTime = $this->_getConfigValue(self::BLOCKED_DURATION);
//
//        if ($formatted && !empty($disabledTime)) {
//            list($days, $hours, $minutes) = explode(':', $disabledTime);
//            $disabledTime = "+{$days} days +{$hours} hours +{$minutes} minutes";
//        }
//
//        return $disabledTime;
//    }

    /**
     *
     * @return string
     */
//    public function getBlockedDuration()
//    {
//        if ($this->isPluginEnabled() && $this->isBlockingEnabled()) {
//            return $this->_getConfigValue(self::BLOCKED_DURATION);
//        }
//        return '';
//    }

    /**
     *
     * @return bool
     */
//    public function isSecondaryPasswordEnabled()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::SECONDARY_PASSWORD_ENABLED) == 'on');
//    }

    /**
     *
     * @return bool
     */
//    public function isPasswordExpirationEnabled()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::PASSWORD_EXPIRATION_ENABLED) == 'on');
//    }

    /**
     *
     * @return integer
     */
//    public function getPasswordExpirationPeriod()
//    {
//        $configValue = $this->_getConfigValue(self::NUM_OF_MONTHS_FOR_PASSWORD_EXPIRATION);
//        if ($configValue) {
//            return (int)$configValue;
//        }
//        return 0;
//    }

    /**
     *
     * @return bool
     */
//    public function canAdminResetEmployeePasswords()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::ADMIN_CAN_RESET) == 'on');
//    }

    /**
     * Returns whether auto reet password is enable for user creation
     * @return bool
     */
//    public function autoResetPasswordAtUserCreation()
//    {
//        return $this->isPluginEnabled() && ($this->_getConfigValue(self::NOTIFY_EMPLOYEE_AFTER_USER_CREATION) == 'on');
//    }

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

