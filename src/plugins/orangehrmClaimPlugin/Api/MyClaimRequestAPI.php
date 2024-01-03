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
use OrangeHRM\Claim\Api\Model\ClaimRequestSummaryModel;
use OrangeHRM\Claim\Api\Model\MyClaimRequestModel;
use OrangeHRM\Claim\Dto\ClaimRequestSearchFilterParams;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class MyClaimRequestAPI extends EmployeeClaimRequestAPI
{
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => MyClaimRequestModel::class,
        self::MODEL_SUMMARY => ClaimRequestSummaryModel::class,
    ];

    /**
     * @OA\Post(
     *     path="/api/v2/claim/requests",
     *     tags={"Claim/My Requests"},
     *     summary="Create My Claim Request",
     *     operationId="create-my-claim-request",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="claimEventId", type="integer"),
     *             @OA\Property(property="currencyId", type="string"),
     *             @OA\Property(property="remarks", type="string"),
     *             required={"claimEventId", "currency"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-RequestModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        return parent::create();
    }

    /**
     * @param int $empNumber
     * @return bool
     */
    protected function isSelfByEmpNumber(int $empNumber): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getCommonParamRuleCollection();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/requests/{id}",
     *     tags={"Claim/My Requests"},
     *     summary="Get My Claim Request",
     *     operationId="get-my-claim-request",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Claim Request Id",
     *         @OA\Schema(type="integer"),
     *         required=true
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-RequestModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="allowedActions", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="employee", type="object",
     *                     @OA\Property(property="empNumber", type="integer"),
     *                     @OA\Property(property="lastName", type="string"),
     *                     @OA\Property(property="firstName", type="string"),
     *                     @OA\Property(property="middleName", type="string"),
     *                     @OA\Property(property="employeeId", type="string"),
     *                     @OA\Property(property="terminationId", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        return parent::getOne();
    }

    /**
     * @inheritDoc
     */
    protected function getEmpNumber(): int
    {
        return $this->getAuthUser()->getEmpNumber();
    }

    /**
     * @return ParamRuleCollection
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
     * @OA\Get(
     *     path="/api/v2/claim/requests",
     *     tags={"Claim/My Requests"},
     *     summary="List My Claim Requests",
     *     operationId="list-my-claim-requests",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ClaimRequestSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="referenceId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"INITIATED", "SUBMITTED", "APPROVED", "REJECTED", "CANCELLED", "PAID"})
     *     ),
     *     @OA\Parameter(
     *         name="eventId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="DateTime")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="DateTime")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={\OrangeHRM\Claim\Api\MyClaimRequestAPI::MODEL_DEFAULT, \OrangeHRM\Claim\Api\MyClaimRequestAPI::MODEL_SUMMARY},
     *             default=\OrangeHRM\Claim\Api\MyClaimRequestAPI::MODEL_DEFAULT
     *         )
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
     *                 @OA\Items(oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Claim-MyClaimRequestModel"),
     *                     @OA\Schema(ref="#/components/schemas/Claim-ClaimRequestSummaryModel"),
     *                 })
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        return parent::getAll();
    }

    /**
     * @return ClaimRequestSearchFilterParams
     */
    protected function getClaimRequestSearchFilterParams(): ClaimRequestSearchFilterParams
    {
        return new ClaimRequestSearchFilterParams();
    }

    /**
     * @inheritDoc
     */
    protected function getEndPointCollectionResult(
        array $claimRequests,
        int $count,
        string $model
    ): EndpointCollectionResult {
        if ($model === EmployeeClaimRequestAPI::MODEL_SUMMARY) {
            return new EndpointCollectionResult(
                ClaimRequestSummaryModel::class,
                $claimRequests,
                new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
            );
        }
        return new EndpointCollectionResult(
            MyClaimRequestModel::class,
            $claimRequests,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    protected function setEmpNumbers(ClaimRequestSearchFilterParams $claimRequestSearchFilterParams): void
    {
        $loggedInEmpNumber = $this->getAuthUser()->getEmpNumber();
        $claimRequestSearchFilterParams->setEmpNumbers([$loggedInEmpNumber]);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $paramRuleCollection = $this->getCommonParamRuleCollectionGetAll();
        $sortFieldParamRules = $this->getSortingAndPaginationParamsRules(
            ClaimRequestSearchFilterParams::ALLOWED_SORT_FIELDS
        );
        foreach ($sortFieldParamRules as $sortFieldParamRule) {
            $paramRuleCollection->addParamValidation($sortFieldParamRule);
        }

        return $paramRuleCollection;
    }
}
