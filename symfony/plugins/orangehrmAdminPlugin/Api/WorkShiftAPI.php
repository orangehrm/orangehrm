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


use OrangeHRM\Admin\Api\Model\WorkShiftModel;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Admin\Service\WorkShiftService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\WorkShift;

class WorkShiftAPI extends EndPoint implements CrudEndpoint
{
    protected ?WorkShiftService $workShiftService = null;

    /**
     * @return WorkShiftService
     */
    public function getWorkShiftService(): WorkShiftService 
    {
        if (is_null($this->workShiftService)) {
            $this->workShiftService = new WorkShiftService();
        }
        return $this->workShiftService;
    }

    /**
     * @param WorkShiftService $workShiftService
     */
    public function setWorkShiftService(WorkShiftService $workShiftService): void 
    {
        $this->workShiftService = $workShiftService;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult 
    {
        $workShiftSearchFilterParams = new WorkShiftSearchFilterParams();
        $this->setSortingAndPaginationParams($workShiftSearchFilterParams);
        $workShifts = $this->getWorkShiftService()->getWorkShiftList($workShiftSearchFilterParams);
        $count = $this->getWorkShiftService()->getWorkShiftCount($workShiftSearchFilterParams);
        return new EndpointCollectionResult(
            WorkShiftModel::class,
            $workShifts,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection 
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(WorkShiftSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $workShift = $this->getWorkShiftService()->getWorkShiftById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($workShift, WorkShift::class);
        return new EndpointResourceResult(WorkShiftModel::class, $workShift);
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
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult 
    {
        // TODO: Implement create() method.
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForCreate() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult 
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }
}
