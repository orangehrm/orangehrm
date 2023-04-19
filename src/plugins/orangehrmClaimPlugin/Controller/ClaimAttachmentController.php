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

namespace OrangeHRM\Claim\Controller;

use OrangeHRM\Claim\Service\ClaimService;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class ClaimAttachmentController extends AbstractFileController
{
    /**
     * @var ClaimService|null
     */
    protected ?ClaimService $claimService = null;

    /**
     * @return ClaimService
     */
    public function getClaimService(): ClaimService
    {
        if (!$this->claimService instanceof ClaimService) {
            $this->claimService = new ClaimService();
        }
        return $this->claimService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $requestId = $request->attributes->get('requestId');
        $attachId = $request->attributes->get('attachId');

        $response = $this->getResponse();

        if ($requestId && $attachId) {
            $attachment = $this->getClaimService()->getAccessibleClaimAttachment($requestId, $attachId);
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
