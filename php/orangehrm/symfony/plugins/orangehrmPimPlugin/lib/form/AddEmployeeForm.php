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
class AddEmployeeForm extends sfForm {

    private $employeeService;
    private $userService;
    private $widgets = array();
    public $createUserAccount = 0;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    private function getUserService() {

        if (is_null($this->userService)) {
            $this->userService = new SystemUserService();
        }

        return $this->userService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {

        $status = array('Enabled' => __('Enabled'), 'Disabled' => __('Disabled'));

        $idGenService = new IDGeneratorService();
        $idGenService->setEntity(new Employee());
        $empNumber = $idGenService->getNextID(false);
        $employeeId = str_pad($empNumber, 4, '0');

        $this->widgets = array(
            'firstName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
            'middleName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
            'lastName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
            'empty' => new ohrmWidgetDiv(),
            'fullNameLabel' => new ohrmWidgetDiv(),
            'firstNameLabel' => new ohrmWidgetDiv(),
            'middleNameLabel' => new ohrmWidgetDiv(),
            'lastNameLabel' => new ohrmWidgetDiv(),
            'employeeId' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 10, "colspan" => 3)),
            'photofile' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 'file_src' => ''), array("class" => "duplexBox", "colspan" => 3)),
//            'helpText' => new ohrmWidgetDiv(),
            'chkLogin' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1), array("style" => "vertical-align:top", "colspan" => 3)),
            'lineSeperator' => new ohrmWidgetDiv(array(), array("colspan" => 3)),
            'user_name' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'status' => new sfWidgetFormSelect(array('choices' => $status), array("class" => "formInputText", "br" => true)),
            'user_password' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText passwordRequired", "maxlength" => 20)),
            're_password' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText passwordRequired", "maxlength" => 20, "br" => true)),
            'empNumber' => new sfWidgetFormInputHidden(),
        );

        $this->widgets['empNumber']->setDefault($empNumber);
        $this->widgets['employeeId']->setDefault($employeeId);

        if ($this->getOption(('employeeId')) != "") {
            $this->widgets['employeeId']->setDefault($this->getOption(('employeeId')));
        }

        $this->widgets['firstName']->setDefault($this->getOption('firstName'));
        $this->widgets['middleName']->setDefault($this->getOption('middleName'));
        $this->widgets['lastName']->setDefault($this->getOption('lastName'));

        $this->widgets['chkLogin']->setDefault($this->getOption('chkLogin'));

        $this->widgets['user_name']->setDefault($this->getOption('user_name'));
        $this->widgets['user_password']->setDefault($this->getOption('user_password'));
        $this->widgets['re_password']->setDefault($this->getOption('re_password'));
        $this->widgets['status']->setDefault($this->getOption('status'));

        $this->setWidgets($this->widgets);

        $this->setValidators(array(
            'photofile' => new sfValidatorFile(array('max_size' => 1000000, 'required' => false)),
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true)),
            'empNumber' => new sfValidatorString(array('required' => false)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true)),
            'employeeId' => new sfValidatorString(array('required' => false, 'max_length' => 10)),
            'chkLogin' => new sfValidatorString(array('required' => false)),
            'user_name' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            'user_password' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            're_password' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            'status' => new sfValidatorString(array('required' => false))
        ));

        $this->getWidgetSchema()->setLabels($this->getFormLabels());

        sfWidgetFormSchemaFormatterAddEmployee::setNoOfColumns(4);
        //merge location dropdown
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'addEmployee', 'AddEmployeeForm');

        $this->widgetSchema->setFormFormatterName('AddEmployee');
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'photofile' => __('Photograph'),
            'firstName' => __('Full Name'),
            'middleName' => false,
            'lastName' => false,
            'empty' => false,
            'fullNameLabel' => ' ',
            'firstNameLabel' => '<span class="helpText">'. __('First Name') . '</span><span class="required">*</span>',
            'middleNameLabel' => '<span class="helpText">'. __('Middle Name') . '</span>',
            'lastNameLabel' => '<span class="helpText">'. __('Last Name') . '</span><span class="required">*</span>',
            'employeeId' => __('Employee Id'),
            'chkLogin' => __('Create Login Details'),
            'lineSeperator' => '<div class="hrLine" id="lineSeperator">&nbsp;</div>',
            'user_name' => __('User Name') . '<span class="required">*</span>',
            'user_password' => __('Password') . '<span id="password_required" class="required">*</span>',
            're_password' => __('Confirm Password') . '<span id="rePassword_required" class="required">*</span>',
            'status' => __('Status') . '<span class="required">*</span>',
//            'helpText' => '<div class="helpText" style="width:160px;padding-top:5px;">' . __(CommonMessages::FILE_LABEL_IMAGE) . '</div>',
        );

        return $labels;
    }

    public function save() {

        $posts = $this->getValues();
        $file = $posts['photofile'];
//        print_r($file);die;
        //saving employee
        $employee = new Employee();
        $employee->firstName = $posts['firstName'];
        $employee->lastName = $posts['lastName'];
        $employee->middleName = $posts['middleName'];
        $employee->employeeId = $posts['employeeId'];

        $employeeService = $this->getEmployeeService();
        $employeeService->saveEmployee($employee);

        $empNumber = $employee->empNumber;

        //saving emp picture
        if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
            $empPicture = new EmpPicture();
            $empPicture->emp_number = $empNumber;
            $tempName = $file->getTempName();

            $empPicture->picture = file_get_contents($tempName);
            ;
            $empPicture->filename = $file->getOriginalName();
            $empPicture->file_type = $file->getType();
            $empPicture->size = $file->getSize();
            list($width, $height) = getimagesize($file->getTempName());
            $sizeArray = $this->pictureSizeAdjust($height, $width);
            $empPicture->width = $sizeArray['width'];
            $empPicture->height = $sizeArray['height'];
            $empPicture->save();
        }

        if ($this->createUserAccount) {
            $this->saveUser($empNumber);
        }

        //merge location dropdown
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->saveMergeForms($this, 'addEmployee', 'AddEmployeeForm');

        return $empNumber;
    }

    private function saveUser($empNumber) {

        $posts = $this->getValues();

        if (trim($posts['user_name']) != "") {
            $userService = $this->getUserService();

            if (trim($posts['user_password']) != "" && $posts['user_password'] == $posts['re_password']) {
                $user = new SystemUser();
                $user->setDateEntered(date('Y-m-d H:i:s'));
                $user->setCreatedBy(sfContext::getInstance()->getUser()->getAttribute('user')->getUserId());
                $user->user_name = $posts['user_name'];
                $user->user_password = md5($posts['user_password']);
                $user->emp_number = $empNumber;
                $user->setStatus(($posts['status'] == 'Enabled') ? '1' : '0');
                $user->setUserRoleId(2);
                $userService->saveSystemUser($user);
            }
            
            $this->_handleLdapEnabledUser($posts, $empNumber);            
        }
    }

    private function pictureSizeAdjust($imgHeight, $imgWidth) {

        if ($imgHeight > 180 || $imgWidth > 150) {
            $newHeight = 0;
            $newWidth = 0;

            $propHeight = floor(($imgHeight / $imgWidth) * 150);
            $propWidth = floor(($imgWidth / $imgHeight) * 180);

            if ($propHeight <= 180) {
                $newHeight = $propHeight;
                $newWidth = 150;
            }

            if ($propWidth <= 150) {
                $newWidth = $propWidth;
                $newHeight = 180;
            }
        } else {
            if ($imgHeight <= 180)
                $newHeight = $imgHeight;

            if ($imgWidth <= 150)
                $newWidth = $imgWidth;
        }
        return array('width' => $newWidth, 'height' => $newHeight);
    }

    protected function _handleLdapEnabledUser($postedValues, $empNumber) {
        
        $sfUser = sfContext::getInstance()->getUser();
        
        $password           = $postedValues['user_password'];
        $confirmedPassword  = $postedValues['re_password'];
        $check1             = (empty($password) && empty($confirmedPassword))?true:false;
        $check2             = $sfUser->getAttribute('ldap.available');
        
        if ($check1 && $check2) {

            $user = new SystemUser();
            $user->setDateEntered(date('Y-m-d H:i:s'));
            $user->setCreatedBy($sfUser->getAttribute('user')->getUserId());
            $user->user_name = $postedValues['user_name'];
            $user->user_password = md5('');
            $user->emp_number = $empNumber;
            $user->setUserRoleId(2);
            $this->getUserService()->saveSystemUser($user);            
            
        }
        
    }    
}