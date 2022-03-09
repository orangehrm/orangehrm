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

use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;

class VerifyController extends AbstractController
{
    use AuthUserTrait;

    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';

    protected ?AuthenticationService $authenticationService = null;

    public function getAuthenticationService(): AuthenticationService
    {
        if (!$this->authenticationService instanceof AuthenticationService) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function handle(Request $request): RedirectResponse
    {
        $username = $request->request->get(self::PARAMETER_USERNAME, '');
        $password = $request->request->get(self::PARAMETER_PASSWORD, '');

        var_dump([$username, $password]);
    }
}
