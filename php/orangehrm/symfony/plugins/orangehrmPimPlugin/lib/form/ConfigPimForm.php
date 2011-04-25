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
 * ConfigPimForm
 *
 */
class ConfigPimForm extends sfForm {

    private $formWidgets = array();

    public function configure() {
        $orangeConfig = $this->getOption('orangeconfig');
        
        $showDeprecatedFields = $orangeConfig->getAppConfValue(Config::KEY_PIM_SHOW_DEPRECATED);
        $showSSN = $orangeConfig->getAppConfValue(Config::KEY_PIM_SHOW_SSN);
        $showSIN = $orangeConfig->getAppConfValue(Config::KEY_PIM_SHOW_SIN);
        $showTax = $orangeConfig->getAppConfValue(Config::KEY_PIM_SHOW_TAX_EXEMPTIONS);
        
        $this->formWidgets['chkDeprecateFields'] = new sfWidgetFormInputCheckbox();
        $this->formWidgets['chkShowSSN'] = new sfWidgetFormInputCheckbox();
        $this->formWidgets['chkShowSIN'] = new sfWidgetFormInputCheckbox();
        $this->formWidgets['chkShowTax'] = new sfWidgetFormInputCheckbox();
        
        
        if ($showDeprecatedFields) {
            $this->formWidgets['chkDeprecateFields']->setAttribute('checked', 'checked');
        }
        if ($showSSN) {
            $this->formWidgets['chkShowSSN']->setAttribute('checked', 'checked');
        }
        if ($showSIN) {
            $this->formWidgets['chkShowSIN']->setAttribute('checked', 'checked');
        }
        if ($showTax) {
            $this->formWidgets['chkShowTax']->setAttribute('checked', 'checked');
        }
            
        $this->setWidgets($this->formWidgets);

        $this->setValidators(array(
                'chkDeprecateFields' => new sfValidatorString(array('required' => false)),
                'chkShowSSN' => new sfValidatorString(array('required' => false)),
                'chkShowSIN' => new sfValidatorString(array('required' => false)),
                'chkShowTax' => new sfValidatorString(array('required' => false)),            
            ));

        $this->widgetSchema->setNameFormat('configPim[%s]');
    }
}
?>
