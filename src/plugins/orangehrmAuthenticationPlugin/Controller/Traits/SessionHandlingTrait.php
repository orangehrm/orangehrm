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

namespace OrangeHRM\Authentication\Controller\Traits;

use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;

trait SessionHandlingTrait
{
    use AuthUserTrait;
    use ServiceContainerTrait;

    /**
     * @return string
     */
    public function handleSessionTimeoutRedirect(): ?string
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);

        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);
        //Recreate the session
        $session->migrate(true);

        if ($this->getAuthUser()->hasAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL)) {
            $redirectUrl = $this->getAuthUser()->getAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            $this->getAuthUser()->removeAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            $logoutUrl = $urlGenerator->generate('auth_logout', [], UrlGenerator::ABSOLUTE_URL);

            if ($redirectUrl !== $loginUrl || $redirectUrl !== $logoutUrl) {
                return $redirectUrl;
            }
        }
        return null;
    }
}
