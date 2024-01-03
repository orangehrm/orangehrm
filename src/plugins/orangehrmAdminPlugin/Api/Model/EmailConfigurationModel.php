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
use OrangeHRM\Entity\EmailConfiguration;

/**
 * @OA\Schema(
 *     schema="Admin-EmailConfigurationModel",
 *     type="object",
 *     @OA\Property(property="mailType", type="string"),
 *     @OA\Property(property="sentAs", type="string"),
 *     @OA\Property(property="smtpHost", type="string"),
 *     @OA\Property(property="smtpPort", type="integer"),
 *     @OA\Property(property="smtpUsername", type="string"),
 *     @OA\Property(property="smtpAuthType", type="string"),
 *     @OA\Property(property="smtpSecurityType", type="string")
 * )
 */
class EmailConfigurationModel implements Normalizable
{
    use ModelTrait;

    public function __construct(EmailConfiguration $emailConfiguration)
    {
        $this->setEntity($emailConfiguration);
        $this->setFilters(
            [
                'mailType',
                'sentAs',
                'smtpHost',
                'smtpPort',
                'smtpUsername',
                'smtpAuthType',
                'smtpSecurityType',
            ]
        );
        $this->setAttributeNames(
            [
                'mailType',
                'sentAs',
                'smtpHost',
                'smtpPort',
                'smtpUsername',
                'smtpAuthType',
                'smtpSecurityType',
            ]
        );
    }
}
