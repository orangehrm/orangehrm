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

namespace OrangeHRM\Pim\Api;

use Exception;
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
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Pim\Api\Model\TerminationReasonConfigurationModel;
use OrangeHRM\Pim\Dto\TerminationReasonConfigurationSearchFilterParams;
use OrangeHRM\Pim\Service\TerminationReasonConfigurationService;

class TerminationReasonConfigurationAPI extends EndPoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    /**
     * @var TerminationReasonConfigurationService|null
     */
    protected ?TerminationReasonConfigurationService $terminationReasonConfigurationService = null;

    public function getTerminationReasonConfigurationService(): TerminationReasonConfigurationService
    {
        if (!$this->terminationReasonConfigurationService instanceof TerminationReasonConfigurationService) {
            $this->terminationReasonConfigurationService = new TerminationReasonConfigurationService();
        }
        return $this->terminationReasonConfigurationService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/termination-reasons",
     *     tags={"PIM/Termination Reason Configuration"},
     *     summary="List All Termination Reasons",
     *     operationId="list-all-termination-reasons",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=TerminationReasonConfigurationSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Pim-TerminationReasonConfigurationModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     * @inheritDoc
     * @throws Exception
     */
    public function getAll(): EndpointResult
    {
        $terminationReasonConfigurationParamHolder = new TerminationReasonConfigurationSearchFilterParams();
        $this->setSortingAndPaginationParams($terminationReasonConfigurationParamHolder);
        $terminationReasons = $this->getTerminationReasonConfigurationService()->getTerminationReasonList($terminationReasonConfigurationParamHolder);
        $count = $this->getTerminationReasonConfigurationService()->getTerminationReasonCount($terminationReasonConfigurationParamHolder);
        return new EndpointCollectionResult(
            TerminationReasonConfigurationModel::class,
            $terminationReasons,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(TerminationReasonConfigurationSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/termination-reasons",
     *     tags={"PIM/Termination Reason Configuration"},
     *     summary="Create a Termination Reason",
     *     operationId="create-a-termination-reason",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\TerminationReasonConfigurationAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-TerminationReasonConfigurationModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResult
    {
        $terminationReason = new TerminationReason();
        $terminationReason = $this->saveTerminationReason($terminationReason);
        return new EndpointResourceResult(TerminationReasonConfigurationModel::class, $terminationReason);
    }

    /**
     * @param TerminationReason $terminationReason
     * @return TerminationReason
     */
    public function saveTerminationReason(TerminationReason $terminationReason): TerminationReason
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $terminationReason->setName($name);
        return $this->getTerminationReasonConfigurationService()->saveTerminationReason($terminationReason);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getNameRule()
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/termination-reasons",
     *     tags={"PIM/Termination Reason Configuration"},
     *     summary="Delete Termination Reasons",
     *     operationId="delete-termination-reasons",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getTerminationReasonConfigurationService()->getTerminationReasonDao()->getExistingTerminationReasonIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getTerminationReasonConfigurationService()->deleteTerminationReasons($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $reasonIdsInUse = $this->getTerminationReasonConfigurationService()->getReasonIdsInUse();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(Rules::POSITIVE),
                            new Rule(Rules::NOT_IN, [$reasonIdsInUse])
                        )
                    ]
                )
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/termination-reasons/{id}",
     *     tags={"PIM/Termination Reason Configuration"},
     *     summary="Get a Termination Reason",
     *     operationId="get-a-termination-reason",
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
     *                 ref="#/components/schemas/Pim-TerminationReasonConfigurationModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $terminationReason = $this->getTerminationReasonConfigurationService()->getTerminationReasonById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($terminationReason, TerminationReason::class);
        return new EndpointResourceResult(TerminationReasonConfigurationModel::class, $terminationReason);
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
     * @OA\Put(
     *     path="/api/v2/pim/termination-reasons/{id}",
     *     tags={"PIM/Termination Reason Configuration"},
     *     summary="Update a Termination Reason",
     *     operationId="update-a-termination-reason",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\TerminationReasonConfigurationAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-ReportingMethodConfigurationModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        $terminationReason = $this->getTerminationReasonConfigurationService()->getTerminationReasonById($this->getAttributeId());
        $this->throwRecordNotFoundExceptionIfNotExist($terminationReason, TerminationReason::class);
        $terminationReason = $this->saveTerminationReason($terminationReason);
        return new EndpointResourceResult(TerminationReasonConfigurationModel::class, $terminationReason);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getNameRule($uniqueOption)
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule
     */
    private function getNameRule(?EntityUniquePropertyOption $uniqueOption = null): ParamRule
    {
        return $this->getValidationDecorator()->requiredParamRule(
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [TerminationReason::class, 'name', $uniqueOption])
            )
        );
    }
}
