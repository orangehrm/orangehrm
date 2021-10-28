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

namespace OrangeHRM\Leave\Event;

use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Framework\Event\Event;

abstract class LeaveStatusChange extends Event
{
    /**
     * @var Leave[]
     */
    private array $leaves;

    /**
     * @var WorkflowStateMachine
     */
    private WorkflowStateMachine $workflow;

    /**
     * @var User
     */
    private User $performer;

    /**
     * @param Leave[] $leaves
     * @param WorkflowStateMachine $workflow
     * @param User $performer
     */
    public function __construct(array $leaves, WorkflowStateMachine $workflow, User $performer)
    {
        $this->leaves = $leaves;
        $this->workflow = $workflow;
        $this->performer = $performer;
    }

    /**
     * Leaves related to one leave request
     * @return Leave[]
     */
    public function getLeaves(): array
    {
        return $this->leaves;
    }


    /**
     * @return WorkflowStateMachine
     */
    public function getWorkflow(): WorkflowStateMachine
    {
        return $this->workflow;
    }

    /**
     * @return User
     */
    public function getPerformer(): User
    {
        return $this->performer;
    }
}
