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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Pim\Api\Model\EmployeeAllowedLanguageModel;
use OrangeHRM\Pim\Dto\EmployeeAllowedLanguageSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeLanguageService;

class EmployeeAllowedLanguageAPI extends Endpoint implements CollectionEndpoint
{
    /**
     * @var EmployeeLanguageService|null
     */
    protected ?EmployeeLanguageService $employeeLanguageService = null;

    /**
     * @return EmployeeLanguageService
     */
    public function getEmployeeLanguageService(): EmployeeLanguageService
    {
        if (!$this->employeeLanguageService instanceof EmployeeLanguageService) {
            $this->employeeLanguageService = new EmployeeLanguageService();
        }
        return $this->employeeLanguageService;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeAllowedLanguageSearchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAllowedLanguageSearchFilterParams);
        $employeeAllowedLanguageSearchFilterParams->setEmpNumber($empNumber);

        $employeeLanguages = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getAllowedEmployeeLanguages($employeeAllowedLanguageSearchFilterParams);
        $count = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getAllowedEmployeeLanguagesCount($employeeAllowedLanguageSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAllowedLanguageModel::class,
            [
                EmployeeAllowedLanguageModel::LANGUAGES => $employeeLanguages,
                EmployeeAllowedLanguageModel::EMP_NUMBER => $empNumber
            ],
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $count,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeAllowedLanguageSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
