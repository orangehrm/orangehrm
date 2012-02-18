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
class saveJobTitleAction extends sfAction {

    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');
        if (!($usrObj->isAdmin())) {
            $this->redirect('pim/viewPersonalDetails');
        }
        $this->getUser()->setAttribute('addScreen', true);
        $jobTitleId = $request->getParameter('jobTitleId');
        $values = array('jobTitleId' => $jobTitleId);

        $this->setForm(new JobTitleForm(array(), $values));

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
            $file = $request->getFiles($this->form->getName());
            if ($_FILES['jobTitle']['size']['jobSpec'] > 1024000) {
                $this->templateMessage = array('WARNING', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
            }
            if ($this->form->isValid()) {
                $result = $this->form->save();
                $this->getUser()->setFlash('templateMessage', array($result['messageType'], $result['message']));
                $this->redirect('admin/viewJobTitleList');
            }
        }
    }

}

