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

/**
 * Actions class for PIM module viewAttachment
 */
class viewAttachmentAction extends sfAction {

   /**
     *
     * @return <type>
     */
    public function getRecruitmentAttachmentService() {
        if (is_null($this->recruitmentAttachmentService)) {
            $this->recruitmentAttachmentService = new RecruitmentAttachmentService();
            $this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
        }
        return $this->recruitmentAttachmentService;
    }

    /**
     *
     * @return <type>
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     * Add / update employee customFields
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {

        // this should probably be kept in session?
        $attachId = $request->getParameter('attachId');
        $screen = $request->getParameter('screen');
        $candidateService = $this->getRecruitmentAttachmentService();
        $attachment = $candidateService->getAttachment($attachId, $screen);

        $response = $this->getResponse();

        if (!empty($attachment)) {
            $contents = $attachment->getFileContent();
            $contentType = $attachment->getAttachmentType();
            $fileName = $attachment->getFileName();
            $fileLength = $attachment->getFileSize();

            $response->setHttpHeader('Pragma', 'public');

            $response->setHttpHeader('Expires', '0');
            $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
            $response->setHttpHeader("Cache-Control", "private", false);
            $response->setHttpHeader("Content-Type", $contentType);
            $response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
            $response->setHttpHeader("Content-Transfer-Encoding", "binary");
            $response->setHttpHeader("Content-Length", $fileLength);

            $response->setContent($contents);
            $response->send();
        } else {
            $response->setStatusCode(404, 'This attachment does not exist');
        }

        return sfView::NONE;
    }

}
