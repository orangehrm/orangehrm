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
use OrangeHRM\Authentication\Service\PasswordStrengthService;
use OrangeHRM\Core\Service\TextHelperService;

class InstallerPasswordStrengthService extends PasswordStrengthService
{
    private ?TextHelperService $textHelperService = null;

    private const DEFAULT_MIN_LENGTH = 8;
    private const DEFAULT_MAX_LENGTH = 64;
    private const DEFAULT_MIN_NO_OF_LOWERCASE_LETTERS = 1;
    private const DEFAULT_MIN_NO_OF_UPPERCASE_LETTERS = 1;
    private const DEFAULT_MIN_NO_OF_NUMBERS = 1;
    private const DEFAULT_MIN_NO_OF_SPECIAL_CHARACTERS = 1;
    private const DEFAULT_IS_SPACES_ALLOWED = 'false';

    /**
     * @return TextHelperService
     */
    protected function getTextHelper(): TextHelperService
    {
        return $this->textHelperService ??= new TextHelperService();
    }

    /**
     * @return int
     */
    protected function getMinLength(): int
    {
        return $this->minLength ??= self::DEFAULT_MIN_LENGTH;
    }

    /**
     * @return int
     */
    protected function getMaxLength(): int
    {
        return $this->maxLength ??= self::DEFAULT_MAX_LENGTH;
    }

    /**
     * @return int
     */
    protected function getMinNoOfLowercaseLetters(): int
    {
        return $this->minNoOfLowercaseLetters ??= self::DEFAULT_MIN_NO_OF_LOWERCASE_LETTERS;
    }

    /**
     * @return int
     */
    protected function getMinNoOfUppercaseLetters(): int
    {
        return $this->minNoOfUppercaseLetters ??= self::DEFAULT_MIN_NO_OF_UPPERCASE_LETTERS;
    }

    /**
     * @return int
     */
    protected function getMinNoOfNumbers(): int
    {
        return $this->minNoOfNumbers ??= self::DEFAULT_MIN_NO_OF_NUMBERS;
    }

    /**
     * @return int
     */
    protected function getMinNoOfSpecialCharacters(): int
    {
        return $this->minNoOfSpecialCharacters ??= self::DEFAULT_MIN_NO_OF_SPECIAL_CHARACTERS;
    }

    /**
     * @return string
     */
    protected function getIsSpacesAllowed(): string
    {
        return $this->isSpacesAllowed ??= self::DEFAULT_IS_SPACES_ALLOWED;
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
        return $messages;
    }
}
