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

namespace Orangehrm\Rest\Api\User\Model;

use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\Entity\Serializable;
use Orangehrm\Rest\Api\Model\ModelTrait;
use sfContext;

class UserModel implements Serializable
{
    use ModelTrait;

    public function __construct(User $user)
    {
        $this->setEntity($user);
    }

    public function toArray()
    {
        return [
            'userName' => $this->entity->getUserName(),
            'userRole' => $this->entity->getUserRole()->getName(),
            'isSupervisor' => $this->getUserAttribute('auth.isSupervisor'),
            'isProjectAdmin' => $this->getUserAttribute('auth.isProjectAdmin'),
            'isManager' => $this->getUserAttribute('auth.isManager'),
            'isDirector' => $this->getUserAttribute('auth.isDirector'),
            'isAcceptor' => $this->getUserAttribute('auth.isAcceptor'),
            'isOfferer' => $this->getUserAttribute('auth.isOfferer'),
            'isHiringManager' => $this->getUserAttribute('auth.isHiringManager'),
            'isInterviewer' => $this->getUserAttribute('auth.isInterviewer'),
        ];
    }

    protected function getUserAttribute(string $name)
    {
        return sfContext::getInstance()->getUser()->getAttribute($name);
    }
}
