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
class listMailConfigurationAction extends sfAction {

    public function execute($request) {
        
        $this->_checkAuthentication();
        
        $emailConfigurationService = new EmailConfigurationService();
        $emailConfiguration = $emailConfigurationService->getEmailConfiguration();
        $this->mailAddress = $emailConfiguration->getSentAs();
        $this->sendMailPath = $emailConfiguration->getSendmailPath();
        $this->smtpAuth = $emailConfiguration->getSmtpAuthType();
        $this->smtpSecurity = $emailConfiguration->getSmtpSecurityType();
        $this->smtpHost = $emailConfiguration->getSmtpHost();
        $this->smtpPort = $emailConfiguration->getSmtpPort();
        $this->smtpUser = $emailConfiguration->getSmtpUsername();
        $this->smtpPass = $emailConfiguration->getSmtpPassword();
        $this->emailType = $emailConfiguration->getMailType();

        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        }
        
    }
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('auth/login');
		}
        
    }    

}