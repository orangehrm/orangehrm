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
 *
 */

namespace OrangeHRM\Claim\Api;

use OpenApi\Annotations as OA;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class MyClaimRequestAPI extends EmployeeClaimRequestAPI
{
    /**
     * @OA\Post(
     *     path="/api/v2/claim/requests",
     *     tags={"Claim/Requests"},
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
     *     tags={"Claim/Requests"},
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
     *                 @OA\Property(property="allowedActions", type="array", @OA\Items(type="object"))
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
}
