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
use OrangeHRM\Authentication\Traits\Service\PasswordStrengthServiceTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class WeakPasswordResetController extends AbstractVueController implements PublicControllerInterface
{
    use PasswordStrengthServiceTrait;
    use CsrfTokenManagerTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $this->getAuthUser()->getFlash(AuthUser::FLASH_LOGIN_ERROR);
        $resetCode = $request->attributes->get('resetCode');
        $component = new Component('reset-weak-password');
        if ($this->getPasswordStrengthService()->validateUrl($resetCode)) {
            $username = $this->getPasswordStrengthService()->getUserNameByResetCode($resetCode);
            $component->addProp(
                new Prop('username', Prop::TYPE_STRING, $username)
            );
            $component->addProp(
                new Prop('code', Prop::TYPE_STRING, $resetCode)
            );
            $component->addProp(
                new Prop(
                    'token',
                    Prop::TYPE_STRING,
                    $this->getCsrfTokenManager()->getToken('reset-weak-password')->getValue()
                )
            );
            if ($this->getAuthUser()->hasFlash(AuthUser::FLASH_PASSWORD_ENFORCE_ERROR)) {
                $error = $this->getAuthUser()->getFlash(AuthUser::FLASH_PASSWORD_ENFORCE_ERROR);
                $component->addProp(
                    new Prop(
                        'error',
                        Prop::TYPE_OBJECT,
                        $error[0] ?? []
                    )
                );
            }
        } else {
            $component->addProp(
                new Prop(
                    'invalid-code',
                    Prop::TYPE_BOOLEAN,
                    true
                )
            );
        }
        $this->setComponent($component);
        $this->setTemplate('no_header.html.twig');
    }
}
