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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\WorkflowStateMachineDecorator;

/**
 * @method WorkflowStateMachineDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_workflow_state_machine")
 * @ORM\Entity
 */
class WorkflowStateMachine
{
    use DecoratorTrait;

    public const TIMESHEET_ACTION_VIEW = 0;
    public const TIMESHEET_ACTION_SUBMIT = 1;
    public const TIMESHEET_ACTION_APPROVE = 2;
    public const TIMESHEET_ACTION_REJECT = 3;
    public const TIMESHEET_ACTION_RESET = 4;
    public const TIMESHEET_ACTION_MODIFY = 5;
    public const TIMESHEET_ACTION_CREATE = 7;

    public const ATTENDANCE_ACTION_PUNCH_IN = 0;
    public const ATTENDANCE_ACTION_PUNCH_OUT = 1;
    public const ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME = 2;
    public const ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME = 3;
    public const ATTENDANCE_ACTION_CREATE = 4;
    public const ATTENDANCE_ACTION_PROXY_PUNCH_IN = 5;
    public const ATTENDANCE_ACTION_PROXY_PUNCH_OUT = 6;
    public const ATTENDANCE_ACTION_DELETE = 7;
    public const ATTENDANCE_ACTION_EDIT_PUNCH_TIME = 8;


    public const RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY = 1;
    public const RECRUITMENT_APPLICATION_ACTION_SHORTLIST = 2;
    public const RECRUITMENT_APPLICATION_ACTION_REJECT = 3;
    public const RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW = 4;
    public const RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED = 5;
    public const RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED = 6;
    public const RECRUITMENT_APPLICATION_ACTION_OFFER_JOB = 7;
    public const RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER = 8;
    public const RECRUITMENT_APPLICATION_ACTION_HIRE = 9;
    public const RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW = 10;

    public const EMPLOYEE_ACTION_ADD = 1;
    public const EMPLOYEE_ACTION_DELETE_ACTIVE = 2;
    public const EMPLOYEE_ACTION_TERMINATE = 3;
    public const EMPLOYEE_ACTION_REACTIVE = 4;
    public const EMPLOYEE_ACTION_DELETE_TERMINATED = 5;

    public const FLOW_TIME_TIMESHEET = 0;
    public const FLOW_ATTENDANCE = 1;
    public const FLOW_RECRUITMENT = 2;
    public const FLOW_EMPLOYEE = 3;
    public const FLOW_LEAVE = 4;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow", type="string", length=255)
     */
    private string $workflow;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private string $state;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private string $role;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private string $action;

    /**
     * @var string
     *
     * @ORM\Column(name="resulting_state", type="string", length=255)
     */
    private string $resultingState;

    /**
     * @var string|null
     *
     * @ORM\Column(name="roles_to_notify", type="text", nullable=true)
     */
    private ?string $rolesToNotify;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", options={"default":0})
     */
    private int $priority;

    public function __construct()
    {
        $this->priority = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getWorkflow(): string
    {
        return $this->workflow;
    }

    /**
     * @param string $workflow
     */
    public function setWorkflow(string $workflow): void
    {
        $this->workflow = $workflow;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getResultingState(): string
    {
        return $this->resultingState;
    }

    /**
     * @param string $resultingState
     */
    public function setResultingState(string $resultingState): void
    {
        $this->resultingState = $resultingState;
    }

    /**
     * @return string|null
     */
    public function getRolesToNotify(): ?string
    {
        return $this->rolesToNotify;
    }

    /**
     * @param string|null $rolesToNotify
     */
    public function setRolesToNotify(?string $rolesToNotify): void
    {
        $this->rolesToNotify = $rolesToNotify;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
