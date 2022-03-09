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

namespace OrangeHRM\Maintenance\Controller;

use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\Request;

class AdministratorAccessController extends AbstractVueController
{
    use AuthUserTrait;
    use EntityManagerHelperTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('admin-access');

        if ($this->getAuthUser()->isAuthenticated()) {
            $userId = $this->getAuthUser()->getUserId();
            $username = $this->getRepository(User::class)->find($userId)->getUsername();
            $component->addProp(
                new Prop('username', Prop::TYPE_STRING, $username)
            );
        }

//        $csrfTokenManager = new CsrfTokenManager();
//        $component->addProp(
//            new Prop('token', Prop::TYPE_STRING, $csrfTokenManager->getToken('login')->getValue())
//        );

        $this->setComponent($component);
    }
}
