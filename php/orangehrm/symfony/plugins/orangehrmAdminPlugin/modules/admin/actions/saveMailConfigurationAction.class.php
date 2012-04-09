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
class saveMailConfigurationAction extends sfAction {

    public function execute($request) {

        $emailConfigurationService = new EmailConfigurationService();
        $this->form = new EmailConfigurationForm(array(), array(), true);
        $this->form->bind($request->getParameter($this->form->getName()));

        $emailConfiguration = $this->form->populateEmailConfiguration($request);
        $emailConfigurationService->saveEmailConfiguration($emailConfiguration);

        if ($request->getParameter('chkSendTestEmail')) {

            $emailService = new EmailService();
            $result = $emailService->sendTestEmail($request->getParameter('txtTestEmail'));

            if ($result) {
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', __('Successfully Saved. Test Email Sent')));
            } else {
                $this->getUser()->setFlash('templateMessage', array('WARNING', __("Successfully Saved. Test Email Not Sent")));
            }
        } else {
            $this->getUser()->setFlash('templateMessage', array('SUCCESS', __(TopLevelMessages::SAVE_SUCCESS)));
        }

        $this->redirect('admin/listMailConfiguration');
    }

}