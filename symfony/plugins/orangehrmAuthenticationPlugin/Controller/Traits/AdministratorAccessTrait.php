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

namespace OrangeHRM\Authentication\Controller\Traits;

use LogicException;
use OrangeHRM\Authentication\Controller\AdministratorAccessController;
use OrangeHRM\Authentication\Controller\AdminPrivilegeController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

trait AdministratorAccessTrait
{
    /**
     * @param Request $request Provide request from handle method in controller
     * @return Response
     */
    public function forwardToAdministratorAccess(Request $request): Response
    {
        if (!$this instanceof AdminPrivilegeController) {
            throw new LogicException(
                'Trait should be used in class that implements ' . AdminPrivilegeController::class
            );
        }
        if (!$this instanceof AbstractVueController) {
            throw new LogicException(
                'Trait should be used in class that extends ' . AbstractVueController::class
            );
        }

        $currentRequest = $this->getCurrentRequest();

        $forwardUrl = $currentRequest->getPathInfo();

        //TODO check referer consistency and limitations
        $backUrl = $request->headers->get('referer');
        $baseUrl = $currentRequest->getSchemeAndHttpHost() . $currentRequest->getBaseUrl();
        $formattedBackUrl = str_replace($baseUrl, '', $backUrl);

        return $this->forward(
            AdministratorAccessController::class . '::handle',
            [],
            ['forward' => $forwardUrl, 'back' => $formattedBackUrl]
        );
    }
}
