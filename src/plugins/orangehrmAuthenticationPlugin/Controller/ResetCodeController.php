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

use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\Request;

class ResetCodeController extends AbstractVueController implements PublicControllerInterface
{
    use CsrfTokenManagerTrait;

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
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $resetCode = $request->attributes->get('resetCode');
        $user = $this->getResetPasswordService()->validateUrl($resetCode);

        if ($user instanceof User) {
            $component = new Component('reset-password');
            $component->addProp(
                new Prop('username', Prop::TYPE_STRING, $user->getUserName())
            );
            $component->addProp(
                new Prop(
                    'token',
                    Prop::TYPE_STRING,
                    $this->getCsrfTokenManager()->getToken('reset-password')->getValue()
                )
            );
        } else {
            $component = new Component('reset-password-error');
        }

        $this->setComponent($component);

        $this->setTemplate('no_header.html.twig');
    }
}
