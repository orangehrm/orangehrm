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

namespace OrangeHRM\Core\Authorization\Service;

use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class HomePageService
{
    use UserRoleManagerTrait;

    /**
     * @return string|null
     */
    public function getHomePagePath(): ?string
    {
        return $this->getUserRoleManager()->getHomePage();
    }

    /**
     * @return string|null
     */
    public function getTimeModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('time');
    }

    /**
     * @return string|null
     */
    public function getLeaveModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('leave');
    }

    /**
     * @return string|null
     */
    public function getAdminModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('admin');
    }

    /**
     * @return string|null
     */
    public function getPimModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('pim');
    }

    /**
     * @return string|null
     */
    public function getRecruitmentModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('recruitment');
    }

    /**
     * @return string|null
     */
    public function getPerformanceModuleDefaultPath(): ?string
    {
        return $this->getModuleDefaultPage('performance');
    }

    /**
     * @param string $module
     * @return string|null
     */
    public function getModuleDefaultPage(string $module): ?string
    {
        return $this->getUserRoleManager()->getModuleDefaultPage($module);
    }
}
