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

namespace OrangeHRM\Authentication\Api;

use OrangeHRM\Authentication\lib\utility\PasswordStrengthValidation;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class PasswordStrengthValidationAPI extends Endpoint implements CollectionEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_PASSWORD = 'password';
    public const PARAMETER_PASSWORD_STRENGTH = 'strength';
    public const PARAMETER_MESSAGES = 'messages';

    public const MIN_PASSWORD_LENGTH = 'authentication.password_policy.min_password_length';
    public const MAX_PASSWORD_LENGTH = 'authentication.password_policy.max_password_length';
    public const MIN_UPPERCASE_LETTERS = 'authentication.password_policy.min_uppercase_letters';
    public const MIN_LOWERCASE_LETTERS = 'authentication.password_policy.min_lowercase_letters';
    public const MIN_NUMBERS_IN_PASSWORD = 'authentication.password_policy.min_numbers_in_password';
    public const MIN_SPECIAL_CHARACTERS = 'authentication.password_policy.min_special_characters';

    private const UPPERCASE_REGEX = '/[A-Z]/';
    private const LOWERCASE_REGEX = '/[a-z]/';
    private const NUMBER_REGEX = '/[0-9]/';
    private const SPECIAL_CHARACTER_REGEX = '/[!@#\$%^&*(),.?":{}|<>]/';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $password = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PASSWORD);
        $passwordStrengthValidation = new PasswordStrengthValidation();

        $passwordStrength = $passwordStrengthValidation->checkPasswordStrength($password);

        $messages = $this->validatePasswordPolicies($password);

        return new EndpointResourceResult(
            ArrayModel::class,
            [self::PARAMETER_MESSAGES => $messages],
            new ParameterBag([self::PARAMETER_PASSWORD_STRENGTH => $passwordStrength])
        );
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMinPasswordLength(string $password): ?string
    {
        $minLength =  $this->getConfigService()->getConfigDao()->getValue(self::MIN_PASSWORD_LENGTH);
        if (strlen($password) < $minLength && $minLength >= 0) {
            return 'Should have at least ' . $minLength .' characters';
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMaxPasswordLength(string $password): ?string
    {
        $maxLength =  $this->getConfigService()->getConfigDao()->getValue(self::MAX_PASSWORD_LENGTH);
        if (strlen($password) > $maxLength && $maxLength > 0) {
            return 'Should not exceed ' . $maxLength . ' characters';
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMinLowercaseLetters(string $password): ?string
    {
        $minNoOfLowercaseLetters = $this->getConfigService()->getConfigDao()->getValue(self::MIN_LOWERCASE_LETTERS);
        $noOfLowercaseLetters =  preg_match_all(self::LOWERCASE_REGEX, $password);
        if ($minNoOfLowercaseLetters < 0) {
            $minNoOfLowercaseLetters = 0;
        }
        if ($minNoOfLowercaseLetters > $noOfLowercaseLetters) {
            return 'Your password must contain minimum '. $minNoOfLowercaseLetters . ' lower-case letters';
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMinUppercaseLetters(string $password): ?string
    {
        $minNoOfUppercaseLetters = $this->getConfigService()->getConfigDao()->getValue(self::MIN_UPPERCASE_LETTERS);
        $noOfUppercaseLetters =  preg_match_all(self::UPPERCASE_REGEX, $password);
        if ($minNoOfUppercaseLetters < 0) {
            $minNoOfUppercaseLetters = 0;
        }
        if ($minNoOfUppercaseLetters > $noOfUppercaseLetters) {
            return 'Your password must contain minimum '. $minNoOfUppercaseLetters . ' upper-case letters';
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMinNumbersInPassword(string $password): ?string
    {
        $minNoOfNumbers = $this->getConfigService()->getConfigDao()->getValue(self::MIN_NUMBERS_IN_PASSWORD);
        $noOfNumbers =  preg_match_all(self::NUMBER_REGEX, $password);
        if ($minNoOfNumbers < 0) {
            $minNoOfNumbers = 0;
        }
        if ($minNoOfNumbers > $noOfNumbers) {
            return 'Your password must contain minimum '. $minNoOfNumbers . ' numbers';
        }
        return null;
    }

    /**
     * @param string $password
     * @return string|null
     */
    public function checkMinSpecialCharacters(string $password): ?string
    {
        $minNoOfSpecialCharacters = $this->getConfigService()->getConfigDao()->getValue(self::MIN_SPECIAL_CHARACTERS);
        $noOfSpecialCharacters = preg_match_all(self::SPECIAL_CHARACTER_REGEX, $password);
        if ($minNoOfSpecialCharacters < 0) {
            $minNoOfSpecialCharacters = 0;
        }
        if ($minNoOfSpecialCharacters > $noOfSpecialCharacters) {
            return 'Your password must contain minimum '. $minNoOfSpecialCharacters . ' special characters';
        }
        return null;
    }

    /**
     * @param string $password
     * @return array
     */
    private function validatePasswordPolicies(string $password): array
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
        return $messages;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_PASSWORD,
                new Rule(Rules::STRING_TYPE),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
