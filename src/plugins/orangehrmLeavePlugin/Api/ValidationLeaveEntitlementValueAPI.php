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

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Api\Traits\LeaveEntitlementPermissionTrait;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class ValidationLeaveEntitlementValueAPI extends Endpoint implements ResourceEndpoint
{
    use LeaveEntitlementServiceTrait;
    use LeaveEntitlementPermissionTrait;

    public const PARAMETER_ENTITLEMENT = 'entitlement';

    public const PARAMETER_VALID = 'valid';
    public const PARAMETER_DAYS_USED = 'daysUsed';

    /**
     * @OA\Get(
     *     path="/api/v2/leave/leave-entitlements/{id}/validation/entitlements",
     *     tags={"Leave/Validation"},
     *     summary="Validate Leave Entitlement",
     *     operationId="validate-leave-entitlement",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="entitlement",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="valid", type="boolean"),
     *                     @OA\Property(property="daysUsed", type="integer"),
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         ),
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $leaveEntitlement = $this->getLeaveEntitlementService()->getLeaveEntitlementDao()->getLeaveEntitlement($id);
        $this->throwRecordNotFoundExceptionIfNotExist($leaveEntitlement, LeaveEntitlement::class);
        $this->checkLeaveEntitlementAccessible($leaveEntitlement);

        $entitlement = $this->getRequestParams()->getFloat(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ENTITLEMENT
        );

        $isValidEntitlement = true;
        if ($leaveEntitlement->getDaysUsed() > $entitlement) {
            $isValidEntitlement = false;
        }
        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_VALID => $isValidEntitlement,
                self::PARAMETER_DAYS_USED => $leaveEntitlement->getDaysUsed(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(self::PARAMETER_ENTITLEMENT, new Rule(Rules::ZERO_OR_POSITIVE))
        );
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
