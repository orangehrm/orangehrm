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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_job_interview_interviewer")
 * @ORM\Entity
 */
class InterviewInterviewer
{
    /**
     * @var Interview
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Interview", cascade={"persist"})
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", nullable=false)
     */
    private Interview $interview;

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", cascade={"persist"})
     * @ORM\JoinColumn(name="interviewer_id", referencedColumnName="emp_number", nullable=false)
     */
    private Employee $interviewer;
}
