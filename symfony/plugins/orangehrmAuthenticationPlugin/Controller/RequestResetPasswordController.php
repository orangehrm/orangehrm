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

use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class RequestResetPasswordController extends AbstractController
{
    use EntityManagerHelperTrait;

    protected ?ResetPasswordService $resetPasswordService = null;

    public function getResetPasswordService(): ?ResetPasswordService
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
    public function handle(Request $request)
    {
        $username = $request->request->get('username');

        $this->getResetPasswordService()->searchForUserRecord($username);
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => $username]);
        if ($user instanceof User) {
            //TODO
            $this->getResetPasswordService()->logPasswordResetRequest($user);
        }
    }
}
