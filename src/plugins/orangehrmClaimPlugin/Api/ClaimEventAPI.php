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

use Exception;
use OrangeHRM\Claim\Api\Model\ClaimEventModel;
use OrangeHRM\Claim\Service\ClaimEventService;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\ORM\Exception\TransactionException;

class ClaimEventAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;

    private ?ClaimEventService $claimEventService = null;
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_STATUS = 'status';
    public const DESCRIPTION_MAX_LENGTH = 1000;

    public const NAME_MAX_LENGTH = 100;


    /**
     * @return EndpointResult
     * @throws BadRequestException
     * @throws InvalidParamException
     * @throws TransactionException
     * @throws NormalizeException
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $claimEvent = new ClaimEvent();
            $this->setParamsToClaimEvent($claimEvent);
            $this->claimEventService->saveEvent($claimEvent);
            return new EndpointResourceResult(ClaimEventModel::class, $claimEvent);
        }catch (InvalidParamException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param ClaimEvent $claimEvent
     * @return void
     */
    public function setParamsToClaimEvent(ClaimEvent $claimEvent)
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $description = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DESCRIPTION);
        $status = $this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS);
        $claimEvent->setName($name);
        $claimEvent->setDescription($description);
        $claimEvent->setStatus($status);
    }

    public function getAll(): EndpointResult
    {
        //return EndpointResult();
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetAll() method.
        //return new ParamRuleCollection();
        throw $this->getNotImplementedException();
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(self::NAME_MAX_LENGTH)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_DESCRIPTION,
                    new Rule(
                        Rules::STRING_TYPE
                    ),
                new Rule(self::DESCRIPTION_MAX_LENGTH)
            ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_STATUS,
                    new Rule(Rules::BOOL_TYPE)
                )
            ),
            //...$this->getCommonBodyValidationRules(),
        );
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
        //return EndpointResult::class();
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
        //return ParamRuleCollection::class();
        throw $this->getNotImplementedException();
    }

    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
        //return EndpointResult::class();
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
        //return ParamRuleCollection::class();
        throw $this->getNotImplementedException();
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
        //return EndpointResult::class();
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
        //return ParamRuleCollection::class();
        throw $this->getNotImplementedException();
    }

    /**
     * @return ClaimEventService|null
     */
    public function getClaimEventService(): ?ClaimEventService
    {
        if (is_null($this->claimEventService)) {
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