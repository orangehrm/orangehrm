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

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\Project;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Time\Controller\Traits\PermissionTrait;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class SaveProjectController extends AbstractVueController implements CapableViewController
{
    use PermissionTrait;
    use UserRoleManagerTrait;
    use ProjectServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('project-edit');
            $unselectableActivityIds = $this->getProjectService()
                ->getProjectDao()
                ->getActivityIdsOfProjectInTimesheetItems($request->attributes->getInt('id'));
            $component->addProp(new Prop('unselectable-ids', Prop::TYPE_ARRAY, $unselectableActivityIds));
            $component->addProp(new Prop('project-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $component = new Component('project-save');
        }
        $this->setComponent($component);
        $this->setPermissions(['time_projects', 'time_project_activities']);
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        if ($request->attributes->has('id')) {
            $id = $request->attributes->get('id');
            if (!$this->getProjectService()->getProjectDao()->getProjectById($id) instanceof Project) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }
            return $this->getUserRoleManager()->isEntityAccessible(Project::class, $id);
        }
        return true;
    }
}
