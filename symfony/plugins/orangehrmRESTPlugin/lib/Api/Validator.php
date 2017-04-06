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

namespace Orangehrm\Rest\Api;

use Respect\Validation\Rules;
use Orangehrm\Rest\Api\Exception\InvalidParamException as InvalidParamException;
class Validator
{
    /**
     * @param array $values
     * @param array $rule
     * @return bool
     * @throws InvalidParamException
     */
    public static function validate(array $values, array $rule )
    {
        try {
            foreach ($rule as $property => $propertyRules) {

               if(isset($values[$property])) {
                    $classNames = array();
                    foreach ($propertyRules as $ruleType => $params) {
                        if (!is_array($params)) {
                            $params = array();
                        }
                        $classNames[] = call_user_func_array(
                            array(new \ReflectionClass('Respect\\Validation\\Rules\\' . $ruleType), 'newInstance'),
                            $params
                        );

                    }
                    $propertyValidatorRule = new Rules\AllOf($classNames);
                    $propertyValidator = new Rules\Key($property, $propertyValidatorRule);
                    $propertyValidator->check(array($property=>$values[$property]));
                }

            }
            return true;

        } catch (\Exception $e) {
            throw new InvalidParamException($e->getMessage());
        }
    }

}
