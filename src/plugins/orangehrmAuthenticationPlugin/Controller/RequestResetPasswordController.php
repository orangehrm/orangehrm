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

use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class RequestResetPasswordController extends AbstractController implements PublicControllerInterface
{
    use CsrfTokenManagerTrait;
    use ConfigServiceTrait;

    protected ?ResetPasswordService $resetPasswordService = null;

    /**
     * @return ResetPasswordService
     */
    public function getResetPasswordService(): ResetPasswordService
    {
        if (!$this->resetPasswordService instanceof ResetPasswordService) {
            $this->resetPasswordService = new ResetPasswordService();
        }
        return $this->resetPasswordService;
    }

    /**
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $token = $request->request->get('_token');
        if (!$this->getCsrfTokenManager()->isValid('request-reset-password', $token)) {
            throw AuthenticationException::invalidCsrfToken();
        }
        if ($this->getConfigService()->getInstanceBaseUrl() === null) {
            // No reset email can be sent without the application URL. Answer before looking the user up,
            // so the response is the same for existing and unknown usernames.
            $this->getResetPasswordService()->logMissingInstanceBaseUrl();
            return $this->redirect("auth/sendPasswordResetFailure");
        }
        $username = $request->request->get('username');
        if (($user = $this->getResetPasswordService()->searchForUserRecord($username)) instanceof User) {
            // The service logs why an email could not be sent. The answer does not depend on it: a different page
            // when sending fails would tell existing usernames apart from unknown ones, which never send an email.
            $this->getResetPasswordService()->logPasswordResetRequest($user);
        }
        return $this->redirect("auth/sendPasswordReset");
    }
}
