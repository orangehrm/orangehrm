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
 * configPimAction
 *
 */
class configurePimAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        //authentication
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 'Yes') {
            $this->forward('leave', 'viewMyLeaveList');
        }

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        OrangeConfig::getInstance()->loadAppConf();

        $param = array('orangeconfig' => OrangeConfig::getInstance());


        $this->setForm(new ConfigPimForm(array(), $param, false));
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $post = $this->form->getValues();

                $this->_saveConfigValue($post, 'chkDeprecateFields', Config::KEY_PIM_SHOW_DEPRECATED);
                $this->_saveConfigValue($post, 'chkShowSSN', Config::KEY_PIM_SHOW_SSN);
                $this->_saveConfigValue($post, 'chkShowSIN', Config::KEY_PIM_SHOW_SIN);
                $this->_saveConfigValue($post, 'chkShowTax', Config::KEY_PIM_SHOW_TAX_EXEMPTIONS);

                $this->getUser()->setFlash('templateMessage', array('success', __('PIM Configuration Saved Successfully')));
                $this->redirect('pim/configurePim');
            }
        }
    }

    /**
     *
     * @param type $post array of POST variables
     * @param type $postVar Post variable containing config value
     * @param type $configKey Key used in config table
     */
    private function _saveConfigValue($post, $postVar, $configKey) {

        $value = false;
        if (isset($post[$postVar]) && $post[$postVar] == 'on') {
            $value = true;
        }
        OrangeConfig::getInstance()->setAppConfValue($configKey, $value);
    }

}

?>
