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

namespace OrangeHRM\Claim\Api;

use OrangeHRM\Claim\Service\ClaimEventService;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Entity\ClaimEvent;

class ClaimEventAPI extends Endpoint implements CrudEndpoint
{
    /**
     * @var ClaimEventService|null
     */
    private ?ClaimEventService $claimEventService=null;
    public const PARAMETER_NAME='name';
    public const PARAMETER_DESCRIPTION='description';
    public const PARAMETER_STATUS='status';

    public function create():EndpointResult
    {
        $claimEvent= new ClaimEvent();
        $this->setParamsToClaimEvent($claimEvent);
        $this->claimEventService->saveEvent($claimEvent);
        return EndpointResult::class();
    }

    public function setParamsToClaimEvent(ClaimEvent $claimEvent){
        $name= $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_NAME);
        $description= $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_DESCRIPTION);
        $status= $this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_STATUS);
        $claimEvent->setName($name);
        $claimEvent->setDescription($description);
        $claimEvent->setStatus($status);
    }

    public function getAll(): EndpointResult
    {
        return EndpointResult();
        // TODO: Implement getAll() method.
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetAll() method.
        return new ParamRuleCollection();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForCreate() method.
        return new ParamRuleCollection();
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
        return EndpointResult::class();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
        return ParamRuleCollection::class();
    }

    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
        return EndpointResult::class();
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
        return ParamRuleCollection::class();
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
        return EndpointResult::class();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
        return ParamRuleCollection::class();
    }

    /**
     * @return ClaimEventService|null
     */
    public function getClaimEventService(): ?ClaimEventService
    {
        if(is_null($this->claimEventService)){
            $this->setClaimEventService();
        }
        return $this->claimEventService;
    }

    /**
     * @param ClaimEventService|null $claimEventService
     */
    public function setClaimEventService(?ClaimEventService $claimEventService): void
    {
        $this->claimEventService = new ClaimEventService();
    }


}