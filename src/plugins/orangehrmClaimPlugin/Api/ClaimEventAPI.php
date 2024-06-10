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

namespace OrangeHRM\Claim\Api;

use OpenApi\Annotations as OA;
use OrangeHRM\Claim\Api\Model\ClaimEventModel;
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ClaimEvent;

class ClaimEventAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use AuthUserTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_ID = 'id';
    public const PARAMETER_EVENT_ID = 'eventId';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_CAN_CLAIM_EVENT_EDIT = 'canEdit';
    public const DESCRIPTION_MAX_LENGTH = 1000;
    public const NAME_MAX_LENGTH = 100;

    /**
     * @OA\Post(
     *     path="/api/v2/claim/events",
     *     tags={"Claim/Events"},
     *     summary="Create a Claim Event",
     *     operationId="create-a-clam-event",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ClaimEventModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $claimEvent = new ClaimEvent();
        $claimEvent->setName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME)
        );
        $this->setClaimEvent($claimEvent);
        return new EndpointResourceResult(ClaimEventModel::class, $claimEvent);
    }

    /**
     * @param ClaimEvent $claimEvent
     */
    public function setClaimEvent(ClaimEvent $claimEvent): void
    {
        $claimEvent->setDescription(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DESCRIPTION)
        );
        $claimEvent->setStatus(
            $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS, true)
        );
        $userId = $this->getAuthUser()->getUserId();
        $claimEvent->getDecorator()->setUserByUserId($userId);
        $this->getClaimService()->getClaimDao()->saveEvent($claimEvent);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/events",
     *     tags={"Claim/Events"},
     *     summary="List All Claim Events",
     *     operationId="list-all-claim-events",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ClaimEventSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Claim-ClaimEventModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     */
    public function getAll(): EndpointResult
    {
        $claimEventSearchFilterParams = new ClaimEventSearchFilterParams();
        $this->setSortingAndPaginationParams($claimEventSearchFilterParams);
        $claimEventSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_NAME)
        );
        $claimEventSearchFilterParams->setStatus(
            $this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_STATUS)
        );
        $claimEventSearchFilterParams->setId(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_EVENT_ID)
        );
        $claimEvents = $this->getClaimService()->getClaimDao()->getClaimEventList($claimEventSearchFilterParams);
        $count = $this->getClaimService()->getClaimDao()->getClaimEventCount($claimEventSearchFilterParams);
        return new EndpointCollectionResult(
            ClaimEventModel::class,
            $claimEvents,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EVENT_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(ClaimEventSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                $this->getNameRule($this->getClaimEventCommonUniqueOption()),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::DESCRIPTION_MAX_LENGTH]),
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_VAL)
            )
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule
     */
    protected function getNameRule(?EntityUniquePropertyOption $uniqueOption = null): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::NAME_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [ClaimEvent::class, 'name', $uniqueOption])
        );
    }

    /**
     * @return EntityUniquePropertyOption
     */
    private function getClaimEventCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreValues(['isDeleted' => true]);
        return $uniqueOption;
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/claim/events",
     *     tags={"Claim/Events"},
     *     summary="Delete Claim Events",
     *     operationId="delete-claim-events",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getClaimService()->getClaimDao()->getExistingClaimEventIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getClaimService()->getClaimDao()->deleteClaimEvents($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/events/{id}",
     *     tags={"Claim/Events"},
     *     summary="Get a Claim Event",
     *     operationId="get-a-claim-event",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ClaimEventModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="canEdit", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws InvalidParamException
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $claimEvent = $this->getClaimService()->getClaimDao()->getClaimEventById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($claimEvent, ClaimEvent::class);
        $isUsed = $this->getClaimService()->getClaimDao()->isClaimEventUsed($id);
        return new EndpointResourceResult(
            ClaimEventModel::class,
            $claimEvent,
            new ParameterBag([self::PARAMETER_CAN_CLAIM_EVENT_EDIT => !$isUsed])
        );
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/claim/events/{id}",
     *     tags={"Claim/Events"},
     *     summary="Update a Claim Event",
     *     operationId="update-a-claim-event",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ClaimEventModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     * @throws InvalidParamException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $claimEvent = $this->getClaimService()->getClaimDao()->getClaimEventById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($claimEvent, ClaimEvent::class);
        $canEditName = !$this->getClaimService()->getClaimDao()->isClaimEventUsed($id);
        $name = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!$canEditName && $name !== null) {
            throw $this->getInvalidParamException(self::PARAMETER_NAME);
        }
        if ($canEditName && $name !== null) {
            $claimEvent->setName($name);
        }
        $this->setClaimEvent($claimEvent);
        return new EndpointResourceResult(ClaimEventModel::class, $claimEvent);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = $this->getClaimEventCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::DESCRIPTION_MAX_LENGTH]),
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                $this->getNameRule($uniqueOption),
            ),
        );
    }
}
