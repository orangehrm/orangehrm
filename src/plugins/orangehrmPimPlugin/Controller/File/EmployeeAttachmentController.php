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

namespace OrangeHRM\Pim\Controller\File;

use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Pim\Service\EmployeeAttachmentService;

class EmployeeAttachmentController extends AbstractFileController
{
    use UserRoleManagerTrait;

    public const DATA_GROUP_JOB_DETAILS = 'job_details';

    /**
     * @var EmployeeAttachmentService|null
     */
    protected ?EmployeeAttachmentService $employeeAttachmentService = null;

    /**
     * @return EmployeeAttachmentService
     */
    public function getEmployeeAttachmentService(): EmployeeAttachmentService
    {
        if (!$this->employeeAttachmentService instanceof EmployeeAttachmentService) {
            $this->employeeAttachmentService = new EmployeeAttachmentService();
        }
        return $this->employeeAttachmentService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $empNumber = $request->attributes->get('empNumber');
        $attachId = $request->attributes->get('attachId');

        $response = $this->getResponse();

        if ($empNumber && $attachId) {
            $attachment = $this->getEmployeeAttachmentService()->getAccessibleEmployeeAttachment($empNumber, $attachId);
            if ($attachment instanceof EmployeeAttachment && $this->canReadAttachment($attachment)) {
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

    /**
     * The attachment is shown on a PIM screen; the user needs read permission on that screen's
     * attachments ({screen}_attachment) for the employee, the same check the screen makes.
     * The employment contract has no attachments data group of its own; it is part of the job
     * details on the Job screen (job_details).
     */
    private function canReadAttachment(EmployeeAttachment $attachment): bool
    {
        $screen = $attachment->getScreen();
        $dataGroup = $screen === EmployeeAttachment::SCREEN_JOB_CONTRACT
            ? self::DATA_GROUP_JOB_DETAILS
            : "{$screen}_attachment";
        return $this->getUserRoleManagerHelper()
            ->getDataGroupPermissionsForEmployee($dataGroup, $attachment->getEmployee()->getEmpNumber())
            ->canRead();
    }
}
