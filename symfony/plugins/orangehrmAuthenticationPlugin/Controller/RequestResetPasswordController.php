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
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class RequestResetPasswordController extends AbstractController implements PublicControllerInterface
{
    use UserServiceTrait;

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
        $csrfTokenManager = new CsrfTokenManager();
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isValid('request-reset-password', $token)) {
            throw AuthenticationException::invalidCsrfToken();
        }
        $username = $request->request->get('username');
        if ($this->getResetPasswordService()->searchForUserRecord($username) instanceof User) {
            $user = $this->getUserService()->getSystemUserDao()->getUserByUserName($username);
            if ($user instanceof User) {
                $this->getResetPasswordService()->logPasswordResetRequest($user);
            }
        }
        return $this->redirect('auth/sendPasswordReset');
    }
}
