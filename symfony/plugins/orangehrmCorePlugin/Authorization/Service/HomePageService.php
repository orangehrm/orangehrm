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

use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;

class HomePageService
{
    /**
     * @var AbstractUserRoleManager|null
     */
    protected ?AbstractUserRoleManager $userRoleManager = null;

    /**
     * @return AbstractUserRoleManager
     * @throws DaoException
     * @throws ServiceException
     */
    public function getUserRoleManager(): AbstractUserRoleManager
    {
        if (!$this->userRoleManager instanceof AbstractUserRoleManager) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }
        return $this->userRoleManager;
    }

    /**
     * @param AbstractUserRoleManager $userRoleManager
     */
    public function setUserRoleManager(AbstractUserRoleManager $userRoleManager): void
    {
        $this->userRoleManager = $userRoleManager;
    }

    public function getHomePagePath()
    {
        return $this->getUserRoleManager()->getHomePage();
    }

    public function getTimeModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('time');
    }

    public function getLeaveModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('leave');
    }

    public function getAdminModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('admin');
    }

    public function getPimModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('pim');
    }

    public function getRecruitmentModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('recruitment');
    }

    public function getPerformanceModuleDefaultPath()
    {
        return $this->getModuleDefaultPage('performance');
    }

    public function getModuleDefaultPage($module)
    {
        return $this->getUserRoleManager()->getModuleDefaultPage($module);
    }
}
