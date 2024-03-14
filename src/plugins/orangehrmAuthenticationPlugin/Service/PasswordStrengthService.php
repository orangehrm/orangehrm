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

namespace OrangeHRM\Authentication\Service;

use Exception;
use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dao\EnforcePasswordDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Utility\Base64Url;
use OrangeHRM\Entity\EnforcePasswordRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;

class PasswordStrengthService
{
    use ConfigServiceTrait;
    use I18NHelperTrait;
    use TextHelperTrait;
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;
    use LoggerTrait;
    use UserRoleManagerTrait;
    use UserServiceTrait;

    private const UPPERCASE_REGEX = '/[A-Z]/';
    private const LOWERCASE_REGEX = '/[a-z]/';
    private const NUMBER_REGEX = '/[0-9]/';
    private const SPACES_REGEX = '/\s/';
    private const SPECIAL_CHARACTER_REGEX = '/[@#\\\\\/\-!$%^&*()_+|~=`{}\[\]:";\'<>?,.]/';
    private const ENFORCE_PASSWORD_RESET_CODE_BYTES_LENGTH = 16;

    protected ?EnforcePasswordDao $enforcePasswordDao = null;

    protected int $minLength;
    protected int $maxLength;
    protected int $minNoOfLowercaseLetters;
    protected int $minNoOfUppercaseLetters;
    protected int $minNoOfNumbers;
    protected int $minNoOfSpecialCharacters;
    protected string $isSpacesAllowed;
    private string $defaultPasswordStrength;

    /**
     * @return EnforcePasswordDao
     */
    public function getEnforcePasswordDao(): EnforcePasswordDao
    {
        return $this->enforcePasswordDao ??= new EnforcePasswordDao();
    }

    /**
     * @return int
     */
    protected function getMinLength(): int
    {
        return $this->minLength ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_PASSWORD_LENGTH
        );
    }

    /**
     * @return int
     */
    protected function getMaxLength(): int
    {
        return $this->maxLength ??= ConfigService::MAX_PASSWORD_LENGTH;
    }

    /**
     * @return int
     */
    protected function getMinNoOfLowercaseLetters(): int
    {
        return $this->minNoOfLowercaseLetters ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_LOWERCASE_LETTERS
        );
    }

    /**
     * @return int
     */
    protected function getMinNoOfUppercaseLetters(): int
    {
        return $this->minNoOfUppercaseLetters ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_UPPERCASE_LETTERS
        );
    }

    /**
     * @return int
     */
    protected function getMinNoOfNumbers(): int
    {
        return $this->minNoOfNumbers ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_NUMBERS_IN_PASSWORD
        );
    }

    /**
     * @return int
     */
    protected function getMinNoOfSpecialCharacters(): int
    {
        return $this->minNoOfSpecialCharacters ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_MIN_SPECIAL_CHARACTERS
        );
    }

    /**
     * @return string
     */
    protected function getIsSpacesAllowed(): string
    {
        return $this->isSpacesAllowed ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_IS_SPACES_ALLOWED
        );
    }

    /**
     * @return string
     */
    protected function getDefaultPasswordStrength(): string
    {
        return $this->defaultPasswordStrength ??= $this->getConfigService()->getConfigDao()->getValue(
            ConfigService::KEY_DEFAULT_PASSWORD_STRENGTH
        );
    }

    /**
     * @param string $password
     * @return bool
     */
    protected function checkMinPasswordLength(string $password): bool
    {
        $minLength = $this->getMinLength();

        if ($minLength < 0) {
            $minLength = 0;
        }
        return $this->getTextHelper()->strLength($password) < $minLength;
    }

    /**
     * @param string $password
     * @return bool
     */
    protected function checkMaxPasswordLength(string $password): bool
    {
        $maxLength = $this->getMaxLength();

        if ($maxLength < 0) {
            $maxLength = 0;
        }

        return $this->getTextHelper()->strLength($password) > $maxLength;
    }

    /**
     * @param string $password
     * @return bool
     */
    protected function checkMinLowercaseLetters(string $password): bool
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
    protected function checkMinUppercaseLetters(string $password): bool
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
    protected function checkMinNumbersInPassword(string $password): bool
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
    protected function checkMinSpecialCharacters(string $password): bool
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
    protected function checkSpacesInPassword(string $password): bool
    {
        $isSpacesAllowed = $this->getIsSpacesAllowed();
        return $isSpacesAllowed === 'false' && preg_match_all(self::SPACES_REGEX, $password) > 0;
    }

    /**
     * @param int $passwordStrength
     * @return bool
     */
    protected function checkRequiredDefaultPasswordStrength(int $passwordStrength): bool
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
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_min_length', ['count' => $this->getMinLength()]);
        }
        if ($this->checkMaxPasswordLength($password)) {
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_max_length', ['count' => $this->getMaxLength()]);
        }
        if ($this->checkMinLowercaseLetters($password)) {
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_n_lowercase_letters', ['count' => $this->getMinNoOfLowercaseLetters()]);
        }
        if ($this->checkMinUppercaseLetters($password)) {
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_n_uppercase_letters', ['count' => $this->getMinNoOfUppercaseLetters()]);
        }
        if ($this->checkMinNumbersInPassword($password)) {
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_n_numbers', ['count' => $this->getMinNoOfNumbers()]);
        }
        if ($this->checkMinSpecialCharacters($password)) {
            $messages[] = $this->getI18NHelper()
                ->trans('auth.password_n_special_characters', ['count' => $this->getMinNoOfSpecialCharacters()]);
        }
        if ($this->checkSpacesInPassword($password)) {
            $messages[] = $this->getI18NHelper()->trans('auth.password_spaces_not_allowed');
        }
        if ($this->checkRequiredDefaultPasswordStrength($passwordStrength) && count($messages) === 0) {
            $messages[] = $this->getI18NHelper()->trans('auth.password_could_be_guessable');
        }
        return $messages;
    }

    /**
     * @param UserCredential $credential
     * @param int $passwordStrength
     * @return bool
     */
    public function isValidPassword(UserCredential $credential, int $passwordStrength): bool
    {
        $password = $credential->getPassword();
        return (!$this->checkMinPasswordLength($password) &&
           !$this->checkMaxPasswordLength($password) &&
           !$this->checkMinLowercaseLetters($password) &&
           !$this->checkMinUppercaseLetters($password) &&
           !$this->checkMinNumbersInPassword($password) &&
           !$this->checkMinSpecialCharacters($password) &&
           !$this->checkSpacesInPassword($password) &&
           (!$this->checkRequiredDefaultPasswordStrength($passwordStrength)));
    }

    /**
     * @return string
     */
    private function generateEnforcePasswordResetCode(): string
    {
        return Base64Url::encode(
            random_bytes(static::ENFORCE_PASSWORD_RESET_CODE_BYTES_LENGTH)
        );
    }

    /**
     * @return string
     */
    public function logPasswordEnforceRequest(): string
    {
        $enforcePasswordRequest = new EnforcePasswordRequest();
        $date = $this->getDateTimeHelper()->getNow();
        $user = $this->getUserRoleManager()->getUser();
        $resetCode = $this->generateEnforcePasswordResetCode();

        $enforcePasswordRequest->setResetRequestDate($date);
        $enforcePasswordRequest->setUser($user);
        $enforcePasswordRequest->setResetCode($resetCode);
        $enforcePasswordRequest->setExpired(false);

        $this->getEnforcePasswordDao()->saveEnforcedPasswordRequest($enforcePasswordRequest);
        return $resetCode;
    }

    /**
     * @param string $resetCode
     * @return bool|null
     */
    public function validateUrl(string $resetCode): ?bool
    {
        $enforcedPasswordLog = $this->getEnforcePasswordDao()->getEnforcedPasswordLogByResetCode($resetCode);
        if ($enforcedPasswordLog instanceof EnforcePasswordRequest) {
            if ($enforcedPasswordLog->isExpired()) {
                $this->getLogger()->warning('Enforce password reset code already used', ['resetCode' => $resetCode]);
                return false;
            }
            if ($this->isResetCodeTimeOut($enforcedPasswordLog)) {
                $this->getEnforcePasswordDao()->updateEnforcedPasswordValid(
                    $enforcedPasswordLog->getUser()->getId(),
                    true
                );
                $this->getLogger()->warning('Enforce password reset code expired', ['resetCode' => $resetCode]);
                return false;
            }
            return true;
        }
        $this->getLogger()->warning('Invalid reset code', ['resetCode' => $resetCode]);
        return false;
    }

    /**
     * @param EnforcePasswordRequest $enforcedPasswordLog
     * @return bool
     */
    private function isResetCodeTimeOut(EnforcePasswordRequest $enforcedPasswordLog): bool
    {
        $resetRequestTime = $enforcedPasswordLog->getResetRequestDate();
        $currentTime = $this->getDateTimeHelper()->getNow();

        $timeDiff = $this->getDateTimeHelper()->dateDiffInHours($currentTime, $resetRequestTime);
        return $timeDiff > 1;
    }

    /**
     * @param string $resetCode
     * @return string|null
     */
    public function getUserNameByResetCode(string $resetCode): ?string
    {
        $request = $this->getEnforcePasswordDao()->getEnforcedPasswordLogByResetCode($resetCode);
        return $request->getUser()->getUserName();
    }

    /**
     * @param User|null $user
     * @return User|null
     */
    private function validateUser(?User $user): ?User
    {
        if ($user instanceof User) {
            if ($user->getEmployee()->getEmployeeTerminationRecord()) {
                return null;
            }
            return $user;
        }
        return null;
    }

    /**
     * @param UserCredential $credential
     * @return bool
     * @throws TransactionException
     */
    public function saveEnforcedPassword(UserCredential $credential): bool
    {
        $this->beginTransaction();
        try {
            $success = false;
            $user = $this->getUserService()->geUserDao()->getUserByUserName($credential->getUsername());
            if ($this->validateUser($user) instanceof User) {
                $user->getDecorator()->setNonHashedPassword($credential->getPassword());
                $user->setDateModified($this->getDateTimeHelper()->getNow());
                $user->setModifiedUserId($user->getId());
                $this->getUserService()->saveSystemUser($user);
                $success = $this->getEnforcePasswordDao()->updateEnforcedPasswordValid($user->getId(), true);
            }
            $this->commitTransaction();
            return $success;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }
}
