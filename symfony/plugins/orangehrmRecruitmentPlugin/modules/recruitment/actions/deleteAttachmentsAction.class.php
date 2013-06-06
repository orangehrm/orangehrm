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
class deleteAttachmentsAction extends sfAction {

    /**
     *
     * @param <type> $request 
     */
    public function execute($request) {

        $screen = $request->getParameter('screen');
        $param = array('screen' => $screen);
        $this->form = new RecruitmentAttachmentDeleteForm(array(), $param, true);

        $this->form->bind($request->getParameter($this->form->getName()));
        if ($this->form->isValid()) {
            $attachmentsToDelete = $request->getParameter('delAttachments', array());
            if ($attachmentsToDelete) {
                for ($i = 0; $i < sizeof($attachmentsToDelete); $i++) {
                    $service = new RecruitmentAttachmentService();
                    $attachment = $service->getAttachment($attachmentsToDelete[$i], $this->form->screen);
                    $attachment->delete();
                }
                $this->getUser()->setFlash('jobAttachmentPane.success', __(TopLevelMessages::DELETE_SUCCESS));
            }
        }

        $this->redirect($this->getRequest()->getReferer() . '#attachments');
    }

}