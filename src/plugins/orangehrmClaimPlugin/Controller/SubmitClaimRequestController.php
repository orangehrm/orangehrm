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

namespace OrangeHRM\Claim\Controller;

use OrangeHRM\Claim\Api\Traits\ClaimRequestAPIHelperTrait;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Framework\Http\Request;

class SubmitClaimRequestController extends AbstractVueController implements CapableViewController
{
    use ServiceContainerTrait;
    use ClaimServiceTrait;
    use ClaimRequestAPIHelperTrait;
    use UserRoleManagerTrait;
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $id = $request->attributes->getInt('id');
        $component = new Component('submit-claim');
        $component->addProp(new Prop('id', Prop::TYPE_NUMBER, $id));
        $component->addProp(new Prop(
            'allowed-file-types',
            Prop::TYPE_ARRAY,
            $this->getConfigService()->getAllowedFileTypes()
        ));
        $component->addProp(new Prop(
            'max-file-size',
            Prop::TYPE_NUMBER,
            $this->getConfigService()->getMaxAttachmentSize()
        ));
        $this->setComponent($component);
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        $id = $request->attributes->getInt('id');
        $claimRequest = $this->getClaimService()->getClaimDao()->getClaimRequestById($id);
        if (
            !$claimRequest instanceof ClaimRequest ||
            !$this->getUserRoleManagerHelper()->isSelfByEmpNumber(
                $claimRequest->getEmployee()->getEmpNumber()
            )
        ) {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        return true;
    }
}
