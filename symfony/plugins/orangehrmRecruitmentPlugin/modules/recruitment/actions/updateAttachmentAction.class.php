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
class updateAttachmentAction extends sfAction {

    /**
     *
     * @param <type> $request 
     */
    public function execute($request) {

	$screen = $request->getParameter('screen');
	$param = array('screen' => $screen);
        $this->form = new RecruitmentAttachmentForm(array(), $param, true);

        if ($this->getRequest()->isMethod('post')) {

            if ($_FILES['recruitmentAttachment']['size']['ufile'] > 1024000 || $_FILES == null) {

                $this->getUser()->setFlash('attachmentMessage', array('warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE)));
                $this->redirect($this->getRequest()->getReferer() . '#attachments');
            }
            // Handle the form submission
            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

            if ($this->form->isValid()) {
                $this->form->save();
//                $this->getUser()->setFlash('attachmentMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
                $this->getUser()->setFlash('jobAttachmentPane.success', __(TopLevelMessages::SAVE_SUCCESS));
            }
        }
        $this->redirect($this->getRequest()->getReferer() . '#attachments');
    }

}
