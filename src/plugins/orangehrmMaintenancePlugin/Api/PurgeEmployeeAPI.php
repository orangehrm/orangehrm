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

namespace OrangeHRM\Maintenance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Maintenance\Api\Model\PurgeEmployeeModel;
use OrangeHRM\Maintenance\Service\PurgeService;
use OrangeHRM\ORM\Exception\TransactionException;

class PurgeEmployeeAPI extends Endpoint implements CollectionEndpoint
{
    private ?PurgeService $purgeService = null;

    /**
     * @return PurgeService
     */
    public function getPurgeService(): PurgeService
    {
        if (is_null($this->purgeService)) {
            $this->purgeService = new PurgeService();
        }
        return $this->purgeService;
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/maintenance/purge",
     *     tags={"Maintenance/Purge Employee"},
     *     summary="Purge Employee",
     *     operationId="purge-employee",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="empNumber", type="integer"),
     *             required={"empNumber"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Maintenance-PurgeEmployeeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     * @inheritDoc
     * @throws BadRequestException|TransactionException
     */
    public function delete(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if ($this->getPurgeService()->getPurgeDao()->isEmployeePurgeable($empNumber)) {
            $this->getPurgeService()->purgeEmployeeData($empNumber);
            return new EndpointResourceResult(PurgeEmployeeModel::class, $empNumber);
        }
        throw $this->getBadRequestException("Employee Cannot Be Purged");
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS, [false]),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
