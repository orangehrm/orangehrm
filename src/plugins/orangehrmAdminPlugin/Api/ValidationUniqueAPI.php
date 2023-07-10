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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Dao\ValidationUniqueDao;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;

class ValidationUniqueAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_VALUE = 'value';
    public const PARAMETER_ENTITY_Id = 'entityId';
    public const PARAMETER_ENTITY_NAME = 'entityName';
    public const PARAMETER_ATTRIBUTE_NAME = 'attributeName';
    public const PARAMETER_MATCH_BY_FIELD = 'matchByField';
    public const PARAMETER_MATCH_BY_VALUE = 'matchByValue';
    public const PARAMETER_IS_UNIQUE_RECORD = 'valid';

    private ?ValidationUniqueDao $validationUniqueDao = null;

    public function getOne(): EndpointResult
    {
        $value = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_VALUE);
        $entityId = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ENTITY_Id);
        $entityName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ENTITY_NAME);
        $attributeName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_ATTRIBUTE_NAME);
        $matchByField = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_MATCH_BY_FIELD);
        $matchByValue = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_MATCH_BY_VALUE);

        $unique = $this->getValidationUniqueDao()->isValueUnique(
            $value,
            $entityName,
            $attributeName,
            $entityId,
            $matchByField,
            $matchByValue
        );

        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_IS_UNIQUE_RECORD => $unique,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramsRules = new ParamRuleCollection();
        $paramsRules->addExcludedParamKey(self::PARAMETER_VALUE);
        $paramsRules->addExcludedParamKey(self::PARAMETER_ENTITY_Id);
        $paramsRules->addExcludedParamKey(self::PARAMETER_ENTITY_NAME);
        $paramsRules->addExcludedParamKey(self::PARAMETER_ATTRIBUTE_NAME);
        $paramsRules->addExcludedParamKey(self::PARAMETER_MATCH_BY_FIELD);
        $paramsRules->addExcludedParamKey(self::PARAMETER_MATCH_BY_VALUE);
        return $paramsRules;
    }

    /**
     * @return ValidationUniqueDao
     */
    public function getValidationUniqueDao(): ValidationUniqueDao
    {
        return $this->validationUniqueDao ??= new ValidationUniqueDao();
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
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
