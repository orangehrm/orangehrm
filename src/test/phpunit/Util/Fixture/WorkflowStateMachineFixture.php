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

namespace OrangeHRM\Tests\Util\Fixture;

use OrangeHRM\Entity\WorkflowStateMachine;

class WorkflowStateMachineFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    protected function getContent(): array
    {
        /** @var WorkflowStateMachine[] $workflowStateMachines */
        $workflowStateMachines = $this->getEntityManager()->getRepository(WorkflowStateMachine::class)->findAll();
        $workflowStates = [];
        foreach ($workflowStateMachines as $reportGroup) {
            $workflowStates[] = [
                'id' => $reportGroup->getId(),
                'workflow' => $reportGroup->getWorkflow(),
                'state' => $reportGroup->getState(),
                'role' => $reportGroup->getRole(),
                'action' => $reportGroup->getAction(),
                'resultingState' => $reportGroup->getResultingState(),
                'rolesToNotify' => $reportGroup->getRolesToNotify(),
                'priority' => $reportGroup->getPriority(),
            ];
        }

        return ['WorkflowStateMachine' => $workflowStates];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'WorkflowStateMachine.yaml';
    }
}
