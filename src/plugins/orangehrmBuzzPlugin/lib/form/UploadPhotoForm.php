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
 * Description of UploadPhotoForm
 *
 * @author aruna
 */
class UploadPhotoForm extends BaseForm {

    protected $widgets = array();

    public function configure() {

        $photographWidgets = $this->getPhotographWidgets();
        $photographValidators = $this->getPhotographValidators();

        $this->widgets = array_merge($this->widgets, $photographWidgets);
        $this->setWidgets($this->widgets);
        $this->setValidators($photographValidators);
        $this->widgetSchema->setLabel('photofile', false);
        $this->widgetSchema->setLabel('phototext', false);
    }

    /**
     * Get form widgets
     * @return \sfWidgetFormInputFileEditable 
     */
    private function getPhotographWidgets() {
        $fileInput = new sfWidgetFormInputFileEditable(array(
            'edit_mode' => false,
            'with_delete' => false,
            'file_src' => ''));
        $fileInput->setAttribute('multiple', true);
        $inputText = new sfWidgetFormTextarea();
        $placeholder = __("Say something about these photos");
        $inputText->setAttribute('placeholder', $placeholder);
        $inputText->setAttribute('rows', '1');
        $widgets = array(
            'phototext' => $inputText,
            'photofile' => $fileInput
        );
        return $widgets;
    }

    /**
     * Get validators
     * @return \sfValidatorFile 
     */
    private function getPhotographValidators() {
        $validators = array(
            'photofile' => new sfValidatorFile(
                    array(
                'max_size' => 5000000,
                'required' => true,
                    ))
        );
        return $validators;
    }

}
