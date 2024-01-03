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

namespace OrangeHRM\Admin\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\PayGradeCurrency;

/**
 * @OA\Schema(
 *     schema="Admin-PayGradeCurrencyModel",
 *     type="object",
 *     @OA\Property(property="minSalary", type="number"),
 *     @OA\Property(property="maxSalary", type="number"),
 *     @OA\Property(
 *         property="currencyType",
 *         type="object",
 *         @OA\Property(property="id", type="string"),
 *         @OA\Property(property="name", type="string")
 *     )
 * )
 */
class PayGradeCurrencyModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param PayGradeCurrency $payGradeCurrency
     */
    public function __construct(PayGradeCurrency $payGradeCurrency)
    {
        $this->setEntity($payGradeCurrency);
        $this->setFilters(
            [
                'minSalary',
                'maxSalary',
                ['getCurrencyType', 'getId'],
                ['getCurrencyType', 'getName'],
            ]
        );
        $this->setAttributeNames(
            [
                'minSalary',
                'maxSalary',
                ['currencyType', 'id'],
                ['currencyType', 'name'],
            ]
        );
    }
}
