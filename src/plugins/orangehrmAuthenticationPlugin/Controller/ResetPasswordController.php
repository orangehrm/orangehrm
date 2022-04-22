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

use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;

class ResetPasswordController extends AbstractController implements PublicControllerInterface
{
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
     * @return RedirectResponse
     * @throws DaoException
     */
    public function handle(Request $request): RedirectResponse
    {
        $csrfTokenManager = new CsrfTokenManager();
        $token = $request->request->get('_token');

        if (!$csrfTokenManager->isValid('reset-password', $token)) {
            throw AuthenticationException::invalidCsrfToken();
        }
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $credentials = new UserCredential($username, $password);
        $this->getResetPasswordService()->saveResetPassword($credentials);
        $session = $this->getContainer()->get(Services::SESSION);
        $session->invalidate();
        return $this->redirect("auth/login");
    }
}
