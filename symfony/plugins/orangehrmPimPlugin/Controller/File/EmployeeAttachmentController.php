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

namespace OrangeHRM\Pim\Controller\File;

use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Pim\Service\EmployeeAttachmentService;

class EmployeeAttachmentController extends AbstractFileController
{
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
     * @throws DaoException
     */
    public function handle(Request $request): Response
    {
        $empNumber = $request->get('empNumber');
        $attachId = $request->get('attachId');

        $response = $this->getResponse();

        if ($empNumber && $attachId) {
            $attachment = $this->getEmployeeAttachmentService()->getAccessibleEmployeeAttachment($empNumber, $attachId);
            if ($attachment instanceof EmployeeAttachment) {
                $response->headers->set("Content-Type", $attachment->getFileType());
                $response->headers->set("Content-Length", $attachment->getSize());
                $response->headers->set(
                    'Content-Disposition',
                    $this->makeAttachmentDisposition($attachment->getFilename())
                );

                $response->setPublic();
                $response->setMaxAge(0);
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->headers->addCacheControlDirective('post-check', 0);
                $response->headers->addCacheControlDirective('pre-check', 0);
                $response->headers->set("Content-Transfer-Encoding", "binary");
                $response->headers->set('Pragma', 'Public');
                $response->headers->set('Expires', '0');

                $response->setContent($attachment->getDecorator()->getAttachment());

                return $response;
            }
        }

        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $response;
    }
}
