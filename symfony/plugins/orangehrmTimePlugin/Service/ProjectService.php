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

namespace OrangeHRM\Time\Service;

use OrangeHRM\Time\Dao\ProjectActivityDao;
use OrangeHRM\Time\Dao\ProjectDao;

class ProjectService
{
    /**
     * @var ProjectDao|null
     */
    private ?ProjectDao $projectDao = null;

    /**
     * @var ProjectActivityDao|null
     */
    protected ?ProjectActivityDao $projectActivityDao = null;

    /**
     * @return ProjectDao
     */
    public function getProjectDao(): ProjectDao
    {
        if (is_null($this->projectDao)) {
            $this->projectDao = new ProjectDao();
        }
        return $this->projectDao;
    }

    /**
     * @return ProjectActivityDao
     */
    public function getProjectActivityDao(): ProjectActivityDao
    {
        if (!$this->projectActivityDao instanceof ProjectActivityDao) {
            $this->projectActivityDao = new ProjectActivityDao();
        }
        return $this->projectActivityDao;
    }
}
