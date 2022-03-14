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

namespace OrangeHRM\Authentication\Controller;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class AdministratorAccessController extends AbstractVueController
{
    use AuthUserTrait;
    use EntityManagerHelperTrait;
    use UserServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('auth-admin-access');

        $forwardUrl = $request->query->get('forward');
        if (isset($forwardUrl)) {
            $this->getAuthUser()->setAttribute(AuthUser::ADMIN_ACCESS_FORWARD_URL, $forwardUrl);
        }

        $backUrl = $request->query->get('back');
        if (isset($backUrl)) {
            $this->getAuthUser()->setAttribute(AuthUser::ADMIN_ACCESS_BACK_URL, $backUrl);
        } else {
            $backUrl = $this->getAuthUser()->getAttribute(AuthUser::ADMIN_ACCESS_BACK_URL);
        }

        $component->addProp(
            new Prop('back-url', Prop::TYPE_STRING, $backUrl)
        );

        if ($this->getAuthUser()->hasFlash(AuthUser::FLASH_VERIFY_ERROR)) {
            $error = $this->getAuthUser()->getFlash(AuthUser::FLASH_VERIFY_ERROR);
            $component->addProp(
                new Prop(
                    'error',
                    Prop::TYPE_OBJECT,
                    $error[0] ?? []
                )
            );
        }

        if ($this->getAuthUser()->isAuthenticated()) {
            $userId = $this->getAuthUser()->getUserId();
            $systemUser = $this->getUserService()->getSystemUser($userId);
            if (isset($systemUser)) {
                $username = $systemUser->getUserName();
                $component->addProp(
                    new Prop('username', Prop::TYPE_STRING, $username)
                );
            }
            $component->addProp(
                new Prop('backUrl', Prop::TYPE_STRING, $backUrl)
            );
        }

        $csrfTokenManager = new CsrfTokenManager();
        $component->addProp(
            new Prop('token', Prop::TYPE_STRING, $csrfTokenManager->getToken('administrator-access')->getValue())
        );

        $this->setTemplate('no_header.html.twig');
        $this->setComponent($component);
    }
}
