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
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\CustomFieldModel;
use OrangeHRM\Pim\Dto\CustomFieldSearchFilterParams;
use OrangeHRM\Pim\Service\CustomFieldService;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeCustomFieldAPI extends Endpoint implements ResourceEndpoint
{
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;
    use NormalizerServiceTrait;

    public const PARAMETER_SCREEN = 'screen';
    public const META_PARAMETER_FIELDS = 'fields';

    public const PARAM_RULE_CUSTOM_FIELD_MAX_LENGTH = 250;

    /**
     * @var null|CustomFieldService
     */
    protected ?CustomFieldService $customFieldService = null;

    /**
     * @return CustomFieldService
     */
    public function getCustomFieldService(): CustomFieldService
    {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new CustomFieldService();
        }
        return $this->customFieldService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        list($empNumber, $screen) = $this->getUrlParams();
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $customFieldSearchParams->setLimit(0);
        if ($screen) {
            $customFieldSearchParams->setScreen($screen);
        }
        $customFields = $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->searchCustomField($customFieldSearchParams);

        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $fieldNumbers = $this->extractFieldNumbersFromCustomFields($customFields);
        $customFieldsArray = $this->getNormalizerService()
            ->normalizeArray(CustomFieldModel::class, $customFields);

        return new EndpointResourceResult(
            ArrayModel::class,
            $this->getEmployeeCustomFieldsByFieldNumbers($fieldNumbers, $employee),
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::META_PARAMETER_FIELDS => $customFieldsArray,
                ]
            )
        );
    }

    /**
     * @param int[] $fieldNumbers
     * @param Employee $employee
     * @return array<string, string>
     */
    private function getEmployeeCustomFieldsByFieldNumbers(array $fieldNumbers, Employee $employee): array
    {
        $employeeCustomFieldsArray = [];
        foreach ($this->getCustomFieldService()->generateGettersByFieldNumbers($fieldNumbers) as $fieldNum => $getter) {
            $fieldKey = $this->getCustomFieldService()->generateFieldKeyByFieldId($fieldNum);
            $employeeCustomFieldsArray[$fieldKey] = $employee->$getter();
        }
        return $employeeCustomFieldsArray;
    }

    /**
     * @param CustomField[] $customFields
     * @return int[]
     */
    private function extractFieldNumbersFromCustomFields(array $customFields): array
    {
        return array_map(
            function (CustomField $customField) {
                return $customField->getFieldNum();
            },
            $customFields
        );
    }

    /**
     * @return array
     */
    private function getUrlParams(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $screen = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_SCREEN);

        return [$empNumber, $screen];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SCREEN,
                    new Rule(Rules::IN, [CustomField::SCREENS])
                )
            )
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
    public function update(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlParams();
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $customFieldKeys = $this->getRequest()->getBody()->keys();
        $customFieldNumbers = $this->getCustomFieldService()->extractFieldNumbersFromFieldKeys($customFieldKeys);

        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $customFieldSearchParams->setLimit(0);
        $customFieldSearchParams->setFieldNumbers($customFieldNumbers);
        $customFields = $this->getCustomFieldService()
            ->getCustomFieldDao()
            ->searchCustomField($customFieldSearchParams);
        $customFieldsAssoc = array_combine($this->extractFieldNumbersFromCustomFields($customFields), $customFields);

        foreach ($customFieldKeys as $index => $customFieldKey) {
            $customFieldValue = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                $customFieldKey
            );
            $customFieldNumber = $customFieldNumbers[$index];
            $customFieldScreen = $customFieldsAssoc[$customFieldNumber]->getScreen();
            $permission = $this->getUserRoleManagerHelper()->getDataGroupPermissionsForEmployee(
                "${customFieldScreen}_custom_fields",
                $empNumber
            );
            if (!$permission->canUpdate()) {
                throw $this->getForbiddenException();
            }
            $setter = $this->getCustomFieldService()->generateSetterByFieldKey($customFieldKey);
            $employee->$setter($customFieldValue);
        }
        $this->getEmployeeService()->saveEmployee($employee);
        $customFieldsArray = $this->getNormalizerService()
            ->normalizeArray(CustomFieldModel::class, $customFields);

        return new EndpointResourceResult(
            ArrayModel::class,
            $this->getEmployeeCustomFieldsByFieldNumbers($customFieldNumbers, $employee),
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    self::META_PARAMETER_FIELDS => $customFieldsArray,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getCustomFieldsParamsRules()
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCustomFieldsParamsRules(): array
    {
        $rules = [];
        for ($i = 1; $i <= CustomField::MAX_FIELD_NUM; $i++) {
            $rules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CustomFieldService::EMPLOYEE_CUSTOM_FIELD_PREFIX . $i,
                    new Rule(Rules::NOT_EMPTY),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CUSTOM_FIELD_MAX_LENGTH])
                )
            );
        }
        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
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
