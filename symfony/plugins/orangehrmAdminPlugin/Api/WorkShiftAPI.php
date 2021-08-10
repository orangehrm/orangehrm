<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
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

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    public function create(): EndpointResult 
    {
        // TODO: Implement create() method.
    }

    public function getValidationRuleForCreate(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForCreate() method.
    }

    public function delete(): EndpointResult {
        // TODO: Implement delete() method.
    }

    public function getValidationRuleForDelete(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    public function update(): EndpointResult 
    {
        // TODO: Implement update() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection 
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }
}