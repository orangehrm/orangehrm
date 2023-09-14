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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\EmpUsTaxExemption;

/**
 * @OA\Schema(
 *     schema="Pim-EmpUsTaxExemptionModel",
 *     type="object",
 *     @OA\Property(property="federalStatus", description="The employee's federal status", type="string"),
 *     @OA\Property(property="federalExemptions", description="The employee's federal tax exemption", type="integer"),
 *     @OA\Property(property="taxState", type="object",
 *         @OA\Property(property="code", description="The employee's tax state code", type="string"),
 *         @OA\Property(property="name", description="The employee's tax state name", type="string"),
 *     ),
 *     @OA\Property(property="stateStatus", description="The employee's status in the state", type="string"),
 *     @OA\Property(property="stateExemptions", description="The employee's tax exemption in the state", type="integer"),
 *     @OA\Property(property="unemploymentState", type="object",
 *         @OA\Property(property="code", description="The employee's unemployment state code", type="string"),
 *         @OA\Property(property="name", description="The employee's unemployment state name", type="string"),
 *     ),
 *     @OA\Property(property="workState", type="object",
 *         @OA\Property(property="code", description="The employee's work state code", type="string"),
 *         @OA\Property(property="name", description="The employee's work state name", type="string"),
 *     ),
 * )
 */
class EmpUsTaxExemptionModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmpUsTaxExemption $empUsTaxExemption
     */
    public function __construct(EmpUsTaxExemption $empUsTaxExemption)
    {
        $this->setEntity($empUsTaxExemption);
        $this->setFilters(
            [
                'federalStatus',
                'federalExemptions',
                'state',
                ['getDecorator', 'getTaxState'],
                'stateStatus',
                'stateExemptions',
                'unemploymentState',
                ['getDecorator', 'getUnemploymentState'],
                'workState',
                ['getDecorator', 'getWorkState'],
            ]
        );
        $this->setAttributeNames(
            [
                'federalStatus',
                'federalExemptions',
                ['taxState','code'],
                ['taxState','name'],
                'stateStatus',
                'stateExemptions',
                ['unemploymentState','code'],
                ['unemploymentState','name'],
                ['workState', 'code'],
                ['workState', 'name'],
            ]
        );
    }
}
