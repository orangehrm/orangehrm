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
class MembershipForm extends BaseForm {

    private $membershipService;

    public function getMembershipService() {
        if (is_null($this->membershipService)) {
            $this->membershipService = new MembershipService();
            $this->membershipService->setMembershipDao(new MembershipDao());
        }
        return $this->membershipService;
    }

    public function configure() {

        $this->setWidgets(array(
            'membershipId' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'membershipId' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 50))
        ));

        $this->widgetSchema->setNameFormat('membership[%s]');
    }

    public function save() {

        $membershipId = $this->getValue('membershipId');
        if (!empty($membershipId)) {
            $membership = $this->getMembershipService()->getMembershipById($membershipId);
        } else {
            $membership = new Membership();
        }
        $membership->setName($this->getValue('name'));
        $membership->save();
    }

    public function getMembershipListAsJson() {

        $list = array();
        $membershipList = $this->getMembershipService()->getMembershipList();
        foreach ($membershipList as $membership) {
            $list[] = array('id' => $membership->getId(), 'name' => $membership->getName());
        }
        return json_encode($list);
    }

}

