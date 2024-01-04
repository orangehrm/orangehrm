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

namespace OrangeHRM\Authentication\Controller;

use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class AdministratorAccessController extends AbstractVueController
{
    use AuthUserTrait;
    use EntityManagerHelperTrait;
    use UserRoleManagerTrait;
    use CsrfTokenManagerTrait;

    protected ?HomePageService $homePageService = null;

    /**
     * @return HomePageService
     */
    public function getHomePageService(): HomePageService
    {
        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService();
        }
        return $this->homePageService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('auth-admin-access');

        $forwardUrl = $request->query->get('forward');
        if (!is_null($forwardUrl)) {
            $this->getAuthUser()->setAttribute(AuthUser::ADMIN_ACCESS_FORWARD_URL, $forwardUrl);
        }

        $backUrl = $this->getAuthUser()->getAttribute(AuthUser::ADMIN_ACCESS_BACK_URL);

        /*
         * IF user has a flash error, set the error prop only
         * This means the user has been redirected due to invalid credentials
         * Therefore, backUrl from query param will be null anyway
         * ELSE there is no flash error, get the backUrl from query parameter
         * In this case, they have accessed the page and not been redirected due to invalid credentials
         * If backUrl from query param is null, set backUrl as homepage path
         */
        if ($this->getAuthUser()->hasFlash(AuthUser::FLASH_VERIFY_ERROR)) {
            $error = $this->getAuthUser()->getFlash(AuthUser::FLASH_VERIFY_ERROR);
            $component->addProp(
                new Prop(
                    'error',
                    Prop::TYPE_OBJECT,
                    $error[0] ?? []
                )
            );
        } else {
            $queryBackUrl = $request->query->get('back');
            $backUrl = $queryBackUrl ?? '/' . $this->getHomePageService()->getHomePagePath();
            $this->getAuthUser()->setAttribute(AuthUser::ADMIN_ACCESS_BACK_URL, $backUrl);
        }

        $component->addProp(
            new Prop('back-url', Prop::TYPE_STRING, $backUrl)
        );

        $username = $this->getUserRoleManager()->getUser()->getUserName();
        $component->addProp(
            new Prop('username', Prop::TYPE_STRING, $username)
        );

        $component->addProp(
            new Prop(
                'token',
                Prop::TYPE_STRING,
                $this->getCsrfTokenManager()->getToken('administrator-access')->getValue()
            )
        );

        $this->setTemplate('no_header.html.twig');
        $this->setComponent($component);
    }
}
