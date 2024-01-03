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

namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\ValidatorException;
use OrangeHRM\Time\Api\ValidationRules\TimesheetDateRule;

class MyTimesheetAPI extends EmployeeTimesheetAPI
{
    /**
     * @inheritDoc
     */
    protected function getEmpNumber(): int
    {
        return $this->getAuthUser()->getEmpNumber();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRuleForGetAll();
        $paramRuleCollection->removeParamValidation(CommonParams::PARAMETER_EMP_NUMBER);
        return $paramRuleCollection;
    }

    /**
     * @inheritDoc
     * @throws ValidatorException
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRuleForCreate();
        $paramRuleCollection->removeParamValidation(CommonParams::PARAMETER_EMP_NUMBER);
        $paramRuleCollection->removeParamValidation(self::PARAMETER_DATE);
        $paramRuleCollection->addParamValidation(
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::API_DATE),
                new Rule(
                    TimesheetDateRule::class,
                    [$this->getAuthUser()->getEmpNumber()]
                ),
            )
        );

        return $paramRuleCollection;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRuleForUpdate();
        $paramRuleCollection->removeParamValidation(CommonParams::PARAMETER_EMP_NUMBER);
        return $paramRuleCollection;
    }
}
