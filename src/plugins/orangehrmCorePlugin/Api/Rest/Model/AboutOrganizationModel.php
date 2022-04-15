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

namespace OrangeHRM\Core\Api\Rest\Model;

use OrangeHRM\Admin\Dto\AboutOrganization;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class AboutOrganizationModel implements Normalizable
{
    use UserRoleManagerTrait;

    /**
     * @var AboutOrganization
     */
    private AboutOrganization $aboutOrganization;

    /**
     * @param AboutOrganization $aboutOrganization
     */
    public function __construct(AboutOrganization $aboutOrganization)
    {
        $this->aboutOrganization = $aboutOrganization;
    }

    /**
     * @return AboutOrganization
     */
    public function getAboutOrganization(): AboutOrganization
    {
        return $this->aboutOrganization;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $aboutOrganization = $this->getAboutOrganization();
        $employeeRole = $this->getUserRoleManager()->getUser()->getUserRole()->getName();
        $aboutOrg = [
            'companyName' => $aboutOrganization->getCompanyName(),
            'productName' => Config::PRODUCT_NAME,
            'version' => $aboutOrganization->getVersion(),
        ];
        if ($employeeRole == 'Admin') {
            $aboutOrg['numberOfActiveEmployee'] = $aboutOrganization->getNumberOfActiveEmployee();
            $aboutOrg['numberOfPastEmployee'] = $aboutOrganization->getNumberOfPastEmployee();
        }
        return $aboutOrg;
    }
}
