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
 * Form class for EmailConfigurationForm
 */
class EmailConfigurationForm extends BaseForm {

    public function configure() {

        /* Widgests */

        /*$formWidgets['cmbMailType'] = new sfWidgetFormChoice(array('choices' => array('SMTP', 'Sendmail')));
        $formWidgets['txtSentAs'] = new sfWidgetFormInputText();

        $formWidgets['txtSmtpHost'] = new sfWidgetFormInputText();
        $formWidgets['txtSmtpPort'] = new sfWidgetFormInputText();
        $formWidgets['optSmtpAuth'] = new sfWidgetFormChoice(array('expanded' => true, 'choices' => array('No', 'Yes')));
        $formWidgets['txtSmtpUsername'] = new sfWidgetFormInputText();
        $formWidgets['txtSmtpPassword'] = new sfWidgetFormInputText();
        $formWidgets['optSmtpSecurity'] = new sfWidgetFormChoice(array('expanded' => true, 'choices' => array('No', 'SSL', 'TLS')));

        $formWidgets['txtSendmailPath'] = new sfWidgetFormInputText();

        $formWidgets['chkTestEmail'] = new sfWidgetFormChoice(array('expanded' => true, 'multiple' => true, 'choices' => array('Send Test Email')));
        $formWidgets['txtTestEmail'] = new sfWidgetFormInputText();*/

        /* Validators */

        /*$formValidators['cmbMailType'] = new sfValidatorChoice(array('choices' => array('SMTP', 'Sendmail')));
        $formValidators['txtSentAs'] = new sfValidatorString(array('required' => true));

        $formValidators['txtSmtpHost'] = new sfValidatorString(array('required' => false));
        $formValidators['txtSmtpPort'] = new sfValidatorString(array('required' => false));
        $formValidators['optSmtpAuth'] = new sfValidatorString(array('required' => false));
        $formValidators['txtSmtpUsername'] = new sfValidatorString(array('required' => false));
        $formValidators['txtSmtpPassword'] = new sfValidatorString(array('required' => false));
        $formValidators['optSmtpSecurity'] = new sfValidatorString(array('required' => false));

        $formValidators['txtSendmailPath'] = new sfValidatorString(array('required' => false));

        $formValidators['chkTestEmail'] = new sfValidatorString(array('required' => false));
        $formValidators['txtTestEmail'] = new sfValidatorString(array('required' => false));

    	$this->setWidgets($formWidgets);
    	$this->setValidators($formValidators);*/

        $this->widgetSchema->setNameFormat('emailConfigurationForm[%s]');

     }

    public function populateEmailConfiguration($request) {

        $emailConfiguration = new EmailConfiguration();


        $emailConfiguration->setMailType($request->getParameter('cmbMailSendingMethod'));
        $emailConfiguration->setSentAs($request->getParameter('txtMailAddress'));
        $emailConfiguration->setSmtpHost($request->getParameter('txtSmtpHost'));
        $emailConfiguration->setSmtpPort($request->getParameter('txtSmtpPort'));
        $emailConfiguration->setSmtpUsername($request->getParameter('txtSmtpUser'));
        $emailConfiguration->setSmtpPassword($request->getParameter('txtSmtpPass'));
        $emailConfiguration->setSmtpAuthType($request->getParameter('optAuth'));
        $emailConfiguration->setSmtpSecurityType($request->getParameter('optSecurity'));
        $emailConfiguration->setSendmailPath($request->getParameter('txtSendmailPath'));

        return $emailConfiguration;

    }





}

?>