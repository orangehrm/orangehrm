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

class ohrmValidatorPassword extends sfValidatorPassword {

    private $passwordHelper;

    /**
     * Configures the current validator.
     *
     * Available options:
     *
     *  * required_strength: The minimum password strength required
     *
     * Available error codes:
     *
     *  * required_strength
     *
     * @param array $options   An array of options
     * @param array $messages  An array of error messages
     *
     * @see sfValidatorBase
     */
    protected function configure($options = array(), $messages = array()) {
        parent::configure($options, $messages);
        $this->addMessage('required_strength', __('The password is too weak.'));
    }

    protected function doClean($value) {
        $password = parent::doClean($value);
        $strength = $this->getPasswordHelper()->getPasswordStrength($password);
        if(isset($password) && !empty($password)){
            if(!$this->getPasswordHelper()->isPasswordStrongWithEnforcement($password)){
                throw new sfValidatorError($this, 'required_strength', array('strength' => $strength));
            }
        }
        return $password;
    }

    public function getPasswordHelper(){
        if (!($this->passwordHelper instanceof PasswordHelper)) {
            $this->passwordHelper = new PasswordHelper();
        }
        return $this->passwordHelper;
    }

}
