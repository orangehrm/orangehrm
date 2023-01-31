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

namespace OrangeHRM\Authentication\Service;

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class PasswordStrengthService
{
    use ConfigServiceTrait;
    use I18NHelperTrait;
    use TextHelperTrait;

    private const UPPERCASE_REGEX = '/[A-Z]/';
    private const LOWERCASE_REGEX = '/[a-z]/';
    private const NUMBER_REGEX = '/[0-9]/';
    private const SPACES_REGEX = '/\s/';
    private const SPECIAL_CHARACTER_REGEX = '/[!@#\$%^&*(),.?":{}|<>]/';

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMinPasswordLength(string $password): ?string
    {
        $minLength = $this->getConfigService()->getConfigDao()->getValue(ConfigService::KEY_MIN_PASSWORD_LENGTH);
        if ($this->getTextHelper()->strLength($password) < $minLength && $minLength >= 0) {
            return $this->getI18NHelper()->trans('auth.password_min_length', ['count' => $minLength]);
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMaxPasswordLength(string $password): ?string
    {
        if ($this->getTextHelper()->strLength($password) > ConfigService::KEY_MAX_PASSWORD_LENGTH) {
            return $this->getI18NHelper()->trans(
                'auth.password_max_length',
                ['count' => ConfigService::KEY_MAX_PASSWORD_LENGTH]
            );
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMinLowercaseLetters(string $password): ?string
    {
        $minNoOfLowercaseLetters = $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_LOWERCASE_LETTERS
        );
        $noOfLowercaseLetters = preg_match_all(self::LOWERCASE_REGEX, $password);
        if ($minNoOfLowercaseLetters < 0) {
            $minNoOfLowercaseLetters = 0;
        }
        if ($minNoOfLowercaseLetters > $noOfLowercaseLetters) {
            return $this->getI18NHelper()->trans('auth.password_n_lowercase_letters', ['count' => $minNoOfLowercaseLetters]);
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMinUppercaseLetters(string $password): ?string
    {
        $minNoOfUppercaseLetters = $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_UPPERCASE_LETTERS
        );
        $noOfUppercaseLetters = preg_match_all(self::UPPERCASE_REGEX, $password);
        if ($minNoOfUppercaseLetters < 0) {
            $minNoOfUppercaseLetters = 0;
        }
        if ($minNoOfUppercaseLetters > $noOfUppercaseLetters) {
            return $this->getI18NHelper()->trans('auth.password_n_uppercase_letters', ['count' => $minNoOfUppercaseLetters]);
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMinNumbersInPassword(string $password): ?string
    {
        $minNoOfNumbers = $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_NUMBERS_IN_PASSWORD
        );
        $noOfNumbers = preg_match_all(self::NUMBER_REGEX, $password);
        if ($minNoOfNumbers < 0) {
            $minNoOfNumbers = 0;
        }
        if ($minNoOfNumbers > $noOfNumbers) {
            return $this->getI18NHelper()->trans('auth.password_n_numbers', ['count' => $minNoOfNumbers]);
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkMinSpecialCharacters(string $password): ?string
    {
        $minNoOfSpecialCharacters = $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_SPECIAL_CHARACTERS
        );
        $noOfSpecialCharacters = preg_match_all(self::SPECIAL_CHARACTER_REGEX, $password);
        if ($minNoOfSpecialCharacters < 0) {
            $minNoOfSpecialCharacters = 0;
        }
        if ($minNoOfSpecialCharacters > $noOfSpecialCharacters) {
            return $this->getI18NHelper()->trans(
                'auth.password_n_special_characters',
                ['count' => $minNoOfSpecialCharacters]
            );
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    private function checkSpacesInPassword(string $password): ?string
    {
        $isSpacesAllowed = $this->getConfigService()->getConfigDao()->getValue(ConfigService::KEY_IS_SPACES_ALLOWED);
        if ($isSpacesAllowed === 'false' && preg_match_all(self::SPACES_REGEX, $password) > 0) {
            return $this->getI18NHelper()->trans('auth.password_spaces_not_allowed');
        }
        return null;
    }

    /**
     * @param int $passwordStrength
     * @return string|null
     */
    private function checkRequiredDefaultPasswordStrength(int $passwordStrength): ?string
    {
        if ($this->getConfigService()->getConfigDao()->getValue(ConfigService::KEY_DEFAULT_PASSWORD_STRENGTH)
            >= $passwordStrength
        ) {
            return $this->getI18NHelper()->trans('auth.password_could_be_guessable');
        }
        return null;
    }

    /**
     * @param string $password
     * @return array
     */
    public function validatePasswordPolicies(string $password, int $passwordStrength): array
    {
        $messages = [];
        if ($check = $this->checkMinPasswordLength($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkMaxPasswordLength($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkMinLowercaseLetters($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkMinUppercaseLetters($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkMinNumbersInPassword($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkMinSpecialCharacters($password)) {
            $messages[] = $check;
        }
        if ($check = $this->checkSpacesInPassword($password)) {
            $messages[] = $check;
        }
        if ($this->checkRequiredDefaultPasswordStrength($passwordStrength) && count($messages) === 0) {
            $messages[] = $this->checkRequiredDefaultPasswordStrength($passwordStrength);
        }
        return $messages;
    }
}
