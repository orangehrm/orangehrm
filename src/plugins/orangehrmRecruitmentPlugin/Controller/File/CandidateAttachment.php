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

namespace OrangeHRM\Recruitment\Controller\File;

use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Recruitment\Traits\Service\RecruitmentAttachmentServiceTrait;

class CandidateAttachment extends AbstractFileController
{
    use RecruitmentAttachmentServiceTrait;

    public function handle(Request $request): Response
    {
        $candidateId = $request->attributes->get('candidateId');
        $response = $this->getResponse();

        if ($candidateId) {
            $attachment = $this->getRecruitmentAttachmentService()
                ->getRecruitmentAttachmentDao()
                ->getCandidateAttachmentByCandidateId($candidateId);
            if ($attachment instanceof \OrangeHRM\Entity\CandidateAttachment) {
                $this->setCommonHeadersToResponse(
                    $attachment->getFileName(),
                    $attachment->getFileType(),
                    $attachment->getFileSize(),
                    $response
                );
                $response->setContent($attachment->getDecorator()->getFileContent());
                return $response;
            }
        }
        return $this->handleBadRequest();
    }
}
