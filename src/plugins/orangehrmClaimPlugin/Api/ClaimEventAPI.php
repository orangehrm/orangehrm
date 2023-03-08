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
    public const PARAMETER_EVENTID = 'eventId';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_STATUS = 'status';
    public const DESCRIPTION_MAX_LENGTH = 1000;
    public const NAME_MAX_LENGTH = 100;

    /**
     * @OA\Post(
     *     path="/api/v2/claim/events",
     *     tags={"Claim/Events"},
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
    public function setClaimEvent(ClaimEvent $claimEvent)
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
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_EVENTID)
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
                    self::PARAMETER_EVENTID,
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
                $this->getNameRule(false),
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
     * @param bool $update
     * @return ParamRule
     */
    protected function getNameRule(bool $update): ParamRule
    {
        $entityProperties = new EntityUniquePropertyOption();
        $ignoreValues = ['isDeleted' => true];
        if ($update) {
            $ignoreValues['getId'] = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );
        }
        $entityProperties->setIgnoreValues($ignoreValues);
        return new ParamRule(
            self::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::NAME_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [ClaimEvent::class, 'name', $entityProperties])
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/claim/events",
     *     tags={"Claim/Events"},
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS);
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
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $claimEvent = $this->getClaimService()->getClaimDao()->getClaimEventById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($claimEvent, ClaimEvent::class);
        return new EndpointResourceResult(ClaimEventModel::class, $claimEvent);
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
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
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
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $claimEvent = $this->getClaimService()->getClaimDao()->getClaimEventById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($claimEvent, ClaimEvent::class);
        $this->setClaimEvent($claimEvent);
        return new EndpointResourceResult(ClaimEventModel::class, $claimEvent);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
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
            )
        );
    }
}
