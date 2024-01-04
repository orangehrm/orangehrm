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

use Exception;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class ClaimAttachmentController extends AbstractFileController
{
    use ClaimServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $response = $this->getResponse();

        if ($request->attributes->has('requestId') && $request->attributes->has('attachId')) {
            $requestId = $request->attributes->get('requestId');
            $attachId = $request->attributes->get('attachId');

            $claimRequest = $this->getClaimService()->getClaimDao()
                ->getClaimRequestById($requestId);
            if (!($claimRequest instanceof ClaimRequest)) {
                return $this->handleBadRequest();
            }

            if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($claimRequest->getEmployee()->getEmpNumber())) {
                return $this->handleBadRequest();
            }

            try {
                $attachment = $this->getClaimService()->getClaimDao()
                    ->getClaimAttachmentFile($requestId, $attachId);
            } catch (Exception $e) {
                return $this->handleBadRequest();
            }

            if ($attachment instanceof ClaimAttachment) {
                $this->setCommonHeadersToResponse(
                    $attachment->getFilename(),
                    $attachment->getFileType(),
                    $attachment->getSize(),
                    $response
                );
                $response->setContent($attachment->getDecorator()->getAttachment());
                return $response;
            }
        }
        return $this->handleBadRequest();
    }
}
