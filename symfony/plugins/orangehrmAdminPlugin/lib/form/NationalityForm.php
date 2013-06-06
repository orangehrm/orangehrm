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
class NationalityForm extends BaseForm {
    private $nationalityService;

    public function getNationalityService() {
        if (is_null($this->nationalityService)) {
            $this->nationalityService = new NationalityService();
            $this->nationalityService->setNationalityDao(new NationalityDao());
        }
        return $this->nationalityService;
    }

    public function configure() {

        $this->setWidgets(array(
            'nationalityId' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'nationalityId' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 100))
        ));

        $this->widgetSchema->setNameFormat('nationality[%s]');
    }

    public function save() {

        $nationalityId = $this->getValue('nationalityId');
        if (!empty($nationalityId)) {
            $nationality = $this->getNationalityService()->getNationalityById($nationalityId);
        } else {
            $nationality = new Nationality();
        }
        $nationality->setName($this->getValue('name'));
        $nationality->save();
    }

    public function getNationalityListAsJson() {

        $list = array();
        $nationalityList = $this->getNationalityService()->getNationalityList();
        foreach ($nationalityList as $nationality) {
            $list[] = array('id' => $nationality->getId(), 'name' => $nationality->getName());
        }
        return json_encode($list);
    }
}

