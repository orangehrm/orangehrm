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

use OrangeHRM\Admin\Api\Model\SubunitModel;
use OrangeHRM\Admin\Api\Model\SubunitTreeModel;
use OrangeHRM\Admin\Traits\Service\CompanyStructureServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Subunit;

class SubunitAPI extends Endpoint implements CrudEndpoint
{
    use CompanyStructureServiceTrait;

    public const PARAMETER_PARENT_ID = 'parentId';
    public const PARAMETER_UNIT_ID = 'unitId';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';

    public const FILTER_DEPTH = 'depth';
    public const FILTER_MODE = 'mode';

    public const MODE_LIST = 'list';
    public const MODE_TREE = 'tree';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        return new EndpointResourceResult(SubunitModel::class, $subunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $depth = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_DEPTH);
        $mode = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODE,
            self::MODE_LIST
        );
        $subunits = $this->getCompanyStructureService()->getSubunitTree($depth);

        $modelClass = SubunitModel::class;
        if ($mode === self::MODE_TREE) {
            $modelClass = SubunitTreeModel::class;
        }

        return new EndpointCollectionResult($modelClass, $subunits);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_DEPTH,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_MODE,
                    new Rule(Rules::IN, [[self::MODE_LIST, self::MODE_TREE]])
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $parentUnitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PARENT_ID);
        $parentSubunit = $this->getCompanyStructureService()->getSubunitById($parentUnitId);
        $this->throwRecordNotFoundExceptionIfNotExist($parentSubunit, Subunit::class);

        $subunit = new Subunit();
        $subunit = $this->setParamsToSubunit($subunit);
        $this->getCompanyStructureService()->addSubunit($parentSubunit, $subunit);

        return new EndpointResourceResult(SubunitModel::class, $subunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_PARENT_ID),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(self::PARAMETER_UNIT_ID),
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_DESCRIPTION),
        ];
    }

    /**
     * @param Subunit $subunit
     * @return Subunit
     */
    private function setParamsToSubunit(Subunit $subunit): Subunit
    {
        $unitId = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_UNIT_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        $subunit->setUnitId($unitId);
        $subunit->setName($name);
        $subunit->setDescription($description);
        return $subunit;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        $subunit = $this->setParamsToSubunit($subunit);

        $this->getCompanyStructureService()->saveSubunit($subunit);
        return new EndpointResourceResult(SubunitModel::class, $subunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $unitId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $subunit = $this->getCompanyStructureService()->getSubunitById($unitId);
        $this->throwRecordNotFoundExceptionIfNotExist($subunit, Subunit::class);

        $resultSubunit = clone $subunit;
        $this->getCompanyStructureService()->deleteSubunit($subunit);

        return new EndpointResourceResult(SubunitModel::class, $resultSubunit);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }
}
