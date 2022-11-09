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

namespace OrangeHRM\Core\Api\V2\Validator;

use Exception;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\Exceptions\ValidationEscapableException;
use OrangeHRM\Core\Api\V2\Validator\Exceptions\ValidationException;
use Respect\Validation\Rules;

class Validator
{
    /**
     * @param array $values
     * @param ParamRuleCollection|null $rules
     * @return bool
     * @throws InvalidParamException
     */
    public static function validate(array $values, ?ParamRuleCollection $rules = null): bool
    {
        $paramRules = $rules->getMap();
        $paramKeys = array_keys($paramRules);
        $values = self::getOnlyNecessaryValues($values, $rules);

        if ($rules->isStrict()) {
            $paramKeys = array_unique(array_merge($paramKeys, array_keys($values)));
        }

        $errorBag = [];
        foreach ($paramKeys as $paramKey) {
            try {
                if (isset($paramRules[$paramKey])) {
                    $paramRule = $paramRules[$paramKey];

                    $compositeClass = $paramRule->getCompositeClass();
                    $paramValidatorRule = new $compositeClass(...$paramRule->getRules());
                    $paramValidator = new Rules\Key($paramKey, $paramValidatorRule);
                    $paramValidator->check(
                        [$paramKey => $values[$paramKey] ?? $paramRule->getDefault()]
                    );
                } else {
                    throw new InvalidParamException(
                        [],
                        sprintf('Unexpected Parameter (`%s`) Received', $paramKey)
                    );
                }
            } catch (ValidationException | Exception $e) {
                if ($e instanceof ValidationEscapableException) {
                    throw $e;
                }
                $errorBag[$paramKey] = $e;
            }
        }
        if (!empty($errorBag)) {
            throw new InvalidParamException($errorBag);
        }

        return true;
    }

    /**
     * @param array $values
     * @param ParamRuleCollection|null $rules
     * @return array
     */
    private static function getOnlyNecessaryValues(array $values, ?ParamRuleCollection $rules = null): array
    {
        $excludedParamKeys = is_null($rules) ?
            ParamRuleCollection::DEFAULT_EXCLUDED_PARAM_KEYS :
            $rules->getExcludedParamKeys();
        foreach ($excludedParamKeys as $excludedParamKey) {
            unset($values[$excludedParamKey]);
        }
        return $values;
    }
}
