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
 *
 */
class SubscriberForm extends BaseForm {

    private $emailNotoficationService;
    private $notificationId;

    public function getEmailNotificationService() {
        if (is_null($this->emailNotoficationService)) {
            $this->emailNotoficationService = new EmailNotificationService();
            $this->emailNotoficationService->setEmailNotificationDao(new EmailNotificationDao());
        }
        return $this->emailNotoficationService;
    }

    public function configure() {

        $this->notificationId = $this->getOption('notificationId');

        $this->setWidgets(array(
            'subscriberId' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
             'email' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'subscriberId' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 100)),
            'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100, 'trim' => true))
        ));

        $this->widgetSchema->setNameFormat('subscriber[%s]');
    }

    public function save() {

        $subscriberId = $this->getValue('subscriberId');
        if (!empty($subscriberId)) {
            $subscriber = $this->getEmailNotificationService()->getSubscriberById($subscriberId);
        } else {
            $subscriber = new EmailSubscriber();
        }
        $subscriber->setNotificationId($this->notificationId);
        $subscriber->setName($this->getValue('name'));
        $subscriber->setEmail($this->getValue('email'));
        $subscriber->save();
    }

    public function getSubscriberListForNotificationAsJson() {

        $list = array();
        $subscriberList = $this->getEmailNotificationService()->getSubscribersByNotificationId($this->notificationId);
        foreach ($subscriberList as $subscriber) {
            $list[] = array('id' => $subscriber->getId(), 'email' => $subscriber->getEmail());
        }
        return json_encode($list);
    }

}

