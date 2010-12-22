<?php
/**
 *
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

class LeaveApplicationMailContent extends orangehrmLeaveMailContent {

    public function getSubjectTemplate() {

        if (empty($this->subjectTemplate)) {

            $this->subjectTemplate = trim($this->readFile($this->templateDirectoryPath . 'leaveApplicationSubject.txt'));

        }

        return $this->subjectTemplate;

    }

    public function getSubjectReplacements() {

        if (empty($this->subjectReplacements)) {

            $this->subjectReplacements = array('performerFullName' => $this->replacements['performerFullName'],
                                               'numberOfDays' => $this->replacements['numberOfDays'],
                                               'leaveType' => $this->replacements['leaveType']
                                               );

        }

        return $this->subjectReplacements;
        
    }

    public function getBodyTemplate() {

        if (empty($this->bodyTemplate)) {

            $this->bodyTemplate = $this->readFile($this->templateDirectoryPath . 'leaveApplicationBody.txt');

        }

        return $this->bodyTemplate;

    }

    public function getBodyReplacements() {

        if (empty($this->bodyReplacements)) {

            $this->bodyReplacements = array('recipientFirstName' => $this->replacements['recipientFirstName'],
                                            'performerFullName' => $this->replacements['performerFullName'],
                                            'leaveDetails' => $this->replacements['leaveDetails'],
                                            'performerFirstName' => $this->replacements['performerFirstName']
                                            );

        }

        return $this->bodyReplacements;
        
    }

    public function getSubscriberSubjectTemplate() {}
    public function getSubscriberSubjectReplacements() {}

    public function getSubscriberBodyTemplate() {

        if (empty($this->subscriberBodyTemplate)) {

            $this->subscriberBodyTemplate = $this->readFile($this->templateDirectoryPath . 'leaveApplicationSubscriberBody.txt');

        }

        return $this->subscriberBodyTemplate;

    }

    public function getSubscriberBodyReplacements() {

        if (empty($this->subscriberBodyReplacements)) {

            $this->subscriberBodyReplacements = array('performerFullName' => $this->replacements['performerFullName'],
                                                      'leaveDetails' => $this->replacements['leaveDetails']
                                                      );

        }

        return $this->subscriberBodyReplacements;

    }

    
}
