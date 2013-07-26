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
class attachmentsComponent extends sfComponent {

    /**
     * Get RecruitmentAttachmentService
     * @returns RecruitmentAttachmentService
     */
    public function getRecruitmentAttachmentService() {
        if (is_null($this->recruitmentAttachmentService)) {
            $this->recruitmentAttachmentService = new RecruitmentAttachmentService();
            $this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
        }
        return $this->recruitmentAttachmentService;
    }

    /**
     * Execute method of component
     *
     * @param type $request
     */
    public function execute($request) {

        $this->scrollToAttachments = false;

        if ($this->getUser()->hasFlash('attachmentMessage')) {

            $this->scrollToAttachments = true;
            list($this->attachmentMessageType, $this->attachmentMessage) = $this->getUser()->getFlash('attachmentMessage');
        }

        //$attachments = $this->getRecruitmentAttachmentService()->getVacancyAttachment($this->id);
        $attachments = $this->getRecruitmentAttachmentService()->getAttachments($this->id, $this->screen);
        $this->attachmentList = array();
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $this->attachmentList[] = $attachment;
            }
        }
        $param = array('screen' => $this->screen);
        if ($this->permissions->canUpdate()) {
            $this->form = new RecruitmentAttachmentForm(array(), $param, true);
            $this->deleteForm = new RecruitmentAttachmentDeleteForm(array(), $param, true);
        }
    }

}
