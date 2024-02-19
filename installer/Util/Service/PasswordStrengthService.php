<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Installer\Util\Service;

use OrangeHRM\Authentication\Dto\UserCredential;

class PasswordStrengthService
{
    private const UPPERCASE_REGEX = '/[A-Z]/';
    private const LOWERCASE_REGEX = '/[a-z]/';
    private const NUMBER_REGEX = '/[0-9]/';
    private const SPACES_REGEX = '/\s/';
    private const SPECIAL_CHARACTER_REGEX = '/[@#\\\\\/\-!$%^&*()_+|~=`{}\[\]:";\'<>?,.]/';

    private const DEFAULT_MIN_LENGTH = 8;
    private const DEFAULT_MAX_LENGTH = 64;
    private const DEFAULT_MIN_NO_OF_LOWERCASE_LETTERS = 1;
    private const DEFAULT_MIN_NO_OF_UPPERCASE_LETTERS = 1;
    private const DEFAULT_MIN_NO_OF_NUMBERS = 1;
    private const DEFAULT_MIN_NO_OF_SPECIAL_CHARACTERS = 1;
    private const DEFAULT_IS_SPACES_ALLOWED = 'false';
    private const DEFAULT_PASSWORD_STRENGTH = 'strong';

    private int $minLength;
    private int $maxLength;
    private int $minNoOfLowercaseLetters;
    private int $minNoOfUppercaseLetters;
    private int $minNoOfNumbers;
    private int $minNoOfSpecialCharacters;
    private string $isSpacesAllowed;
    private string $defaultPasswordStrength;

    /**
     * @return int
     */
    private function getMinLength(): int
    {
        return $this->minLength ??= self::DEFAULT_MIN_LENGTH;
    }

    /**
     * @return int
     */
    private function getMaxLength(): int
    {
        return $this->maxLength ??= self::DEFAULT_MAX_LENGTH;
    }

    /**
     * @return int
     */
    private function getMinNoOfLowercaseLetters(): int
    {
        return $this->minNoOfLowercaseLetters ??= self::DEFAULT_MIN_NO_OF_LOWERCASE_LETTERS;
    }

    /**
     * @return int
     */
    private function getMinNoOfUppercaseLetters(): int
    {
        return $this->minNoOfUppercaseLetters ??= self::DEFAULT_MIN_NO_OF_UPPERCASE_LETTERS;
    }

    /**
     * @return int
     */
    private function getMinNoOfNumbers(): int
    {
        return $this->minNoOfNumbers ??= self::DEFAULT_MIN_NO_OF_NUMBERS;
    }

    /**
     * @return int
     */
    private function getMinNoOfSpecialCharacters(): int
    {
        return $this->minNoOfSpecialCharacters ??= self::DEFAULT_MIN_NO_OF_SPECIAL_CHARACTERS;
    }

    /**
     * @return string
     */
    private function getIsSpacesAllowed(): string
    {
        return $this->isSpacesAllowed ??= self::DEFAULT_IS_SPACES_ALLOWED;
    }

    /**
     * @return string
     */
    private function getDefaultPasswordStrength(): string
    {
        return $this->defaultPasswordStrength ??= self::DEFAULT_PASSWORD_STRENGTH;
    }

    /**
     * @param string $text
     * @param string|null $encoding
     * @return int
     * @link https://www.php.net/manual/en/mbstring.supported-encodings.php
     */
    public function strLength(string $text, ?string $encoding = null): int
    {
        if (function_exists('mb_strlen')) {
            if (is_null($encoding)) {
                return mb_strlen($text);
            }
            return mb_strlen($text, $encoding);
        } else {
            return strlen($text);
        }
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMinPasswordLength(string $password): bool
    {
        $minLength = $this->getMinLength();

        if ($minLength < 0) {
            $minLength = 0;
        }
        return $this->strLength($password) < $minLength;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMaxPasswordLength(string $password): bool
    {
        $maxLength = $this->getMaxLength();

        if ($maxLength < 0) {
            $maxLength = 0;
        }

        return $this->strLength($password) > $maxLength;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMinLowercaseLetters(string $password): bool
    {
        $minNoOfLowercaseLetters = $this->getMinNoOfLowercaseLetters();

        $noOfLowercaseLetters = preg_match_all(self::LOWERCASE_REGEX, $password);
        if ($minNoOfLowercaseLetters < 0) {
            $minNoOfLowercaseLetters = 0;
        }
        return $minNoOfLowercaseLetters > $noOfLowercaseLetters;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMinUppercaseLetters(string $password): bool
    {
        $minNoOfUppercaseLetters = $this->getMinNoOfUppercaseLetters();
        $noOfUppercaseLetters = preg_match_all(self::UPPERCASE_REGEX, $password);
        if ($minNoOfUppercaseLetters < 0) {
            $minNoOfUppercaseLetters = 0;
        }
        return $minNoOfUppercaseLetters > $noOfUppercaseLetters;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMinNumbersInPassword(string $password): bool
    {
        $minNoOfNumbers = $this->getMinNoOfNumbers();
        $noOfNumbers = preg_match_all(self::NUMBER_REGEX, $password);
        if ($minNoOfNumbers < 0) {
            $minNoOfNumbers = 0;
        }
        return $minNoOfNumbers > $noOfNumbers;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkMinSpecialCharacters(string $password): bool
    {
        $minNoOfSpecialCharacters = $this->getMinNoOfSpecialCharacters();
        $noOfSpecialCharacters = preg_match_all(self::SPECIAL_CHARACTER_REGEX, $password);
        if ($minNoOfSpecialCharacters < 0) {
            $minNoOfSpecialCharacters = 0;
        }
        return $minNoOfSpecialCharacters > $noOfSpecialCharacters;
    }

    /**
     * @param string $password
     * @return bool
     */
    private function checkSpacesInPassword(string $password): bool
    {
        $isSpacesAllowed = $this->getIsSpacesAllowed();
        return $isSpacesAllowed === 'false' && preg_match_all(self::SPACES_REGEX, $password) > 0;
    }

    /**
     * @param int $passwordStrength
     * @return bool
     */
    private function checkRequiredDefaultPasswordStrength(int $passwordStrength): bool
    {
        $defaultPasswordStrength = $this->getDefaultPasswordStrength();
        return (($defaultPasswordStrength === 'veryWeak' && $passwordStrength < 0)
            || ($defaultPasswordStrength === 'weak'
                && $passwordStrength < 1)
            || ($defaultPasswordStrength === 'better' && $passwordStrength < 2)
            || ($defaultPasswordStrength === 'strong' && $passwordStrength < 3)
            || ($defaultPasswordStrength === 'strongest' && $passwordStrength < 4)
        );
    }

    /**
     * @param UserCredential $credential
     * @param int $passwordStrength
     *
     * @return array
     */
    public function checkPasswordPolicies(UserCredential $credential, int $passwordStrength): array
    {
        $messages = [];
        $password = $credential->getPassword();
        if ($this->checkMinPasswordLength($password)) {
            $messages[] = 'Should have at least ' . $this->getMinLength() . ' character(s)';
        }
        if ($this->checkMaxPasswordLength($password)) {
            $messages[] = 'Should not exceed ' . $this->getMaxLength() . ' character(s)';
        }
        if ($this->checkMinLowercaseLetters($password)) {
            $messages[] = 'Your password must contain minimum ' . $this->getMinNoOfLowercaseLetters() . ' lower-case letter(s)';
        }
        if ($this->checkMinUppercaseLetters($password)) {
            $messages[] = 'Your password must contain minimum ' . $this->getMinNoOfUppercaseLetters() . ' upper-case letter(s)';
        }
        if ($this->checkMinNumbersInPassword($password)) {
            $messages[] = 'Your password must contain minimum ' . $this->getMinNoOfNumbers() . ' number(s)';
        }
        if ($this->checkMinSpecialCharacters($password)) {
            $messages[] = 'Your password must contain minimum ' . $this->getMinNoOfSpecialCharacters() . ' special character(s)';
        }
        if ($this->checkSpacesInPassword($password)) {
            $messages[] = 'Your password should not contain spaces';
        }
        if ($this->checkRequiredDefaultPasswordStrength($passwordStrength) && count($messages) === 0) {
            $messages[] = 'Your password meets the minimum requirements, but it could be guessable';
        }
        return $messages;
    }
}
