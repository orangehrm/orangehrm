<?php

function getListClassHtml($action) {
    
    if ($action == sfContext::getInstance()->getActionName()) {
        return ' class="selected"';
    }
    
    return '';
    
}

function isTaxMenuEnabled() {
    
    $sfUser = sfContext::getInstance()->getUser();
    
    if (!$sfUser->hasAttribute('pim.leftMenu.isTaxMenuEnabled')) {
        $isTaxMenuEnabled = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS);
        $sfUser->setAttribute('pim.leftMenu.isTaxMenuEnabled', $isTaxMenuEnabled);
    }
    
    return $sfUser->getAttribute('pim.leftMenu.isTaxMenuEnabled');
    
}

$employeeService = new EmployeeService();
$userRoleManager = sfContext::getInstance()->getUserRoleManager();
$entities = array('Employee' => $empNumber);
$self = false;

if($empNumber == sfContext::getInstance()->getUser()->getAttribute('auth.empNumber')) {
    $self = true;
}

?>

<div id="sidebar">

    <?php 

        $empPicture = $employeeService->getEmployeePicture($empNumber);

        $width = '200';
        $height = '200';
        
        $photographPermissions = $userRoleManager->getDataGroupPermissions(array('photograph'), array(), array(), $self, $entities);
        
        if ((!empty($empPicture)) && ($photographPermissions->canRead())) {
            $width = $empPicture->width;
            $height = $empPicture->height;
        }    
                        
        include_partial('photo', array('empNumber' => $empNumber,
                        'width' => $width, 'height' => $height,
                        'editMode' => isset($editPhotoMode) ? $editPhotoMode : false,
                        'fullName' => htmlspecialchars($form->fullName), 'photographPermissions' => $photographPermissions));
       
    ?>        
        
    <ul id="sidenav">
        
        <?php // viewPersonalDetails
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('personal_information','personal_attachment','personal_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>
            <li<?php echo getListClassHtml('viewPersonalDetails'); ?>><a href="<?php echo url_for('pim/viewPersonalDetails?empNumber=' . $empNumber); ?>"><?php echo __("Personal Details"); ?></a></li>
        <?php endif; ?>

        <?php // contactDetails
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('contact_details','contact_attachment','contact_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>
            <li<?php echo getListClassHtml('contactDetails'); ?>><a href="<?php echo url_for('pim/contactDetails?empNumber=' . $empNumber); ?>"><?php echo __("Contact Details"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewEmergencyContacts
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('emergency_contacts','emergency_attachment','emergency_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewEmergencyContacts'); ?>><a href="<?php echo url_for('pim/viewEmergencyContacts?empNumber=' . $empNumber); ?>"><?php echo __("Emergency Contacts"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewDependents
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('dependents','dependents_attachment','dependents_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewDependents'); ?>><a href="<?php echo url_for('pim/viewDependents?empNumber=' . $empNumber); ?>"><?php echo __("Dependents"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewImmigration
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('immigration','immigration_attachment','immigration_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewImmigration'); ?>><a href="<?php echo url_for('pim/viewImmigration?empNumber=' . $empNumber); ?>"><?php echo __("Immigration"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewJobDetails
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('job_details','job_attachment','job_custom_fields'), array(), array(), $self, $entities);
            $employee = $employeeService->getEmployee($empNumber);
            $allowedActions = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_EMPLOYEE, $employee->getState());
            $allowActivate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_REACTIVE]);
            $allowTerminate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_TERMINATE]);
            
            if($dataGroupPermission->canRead() || $allowTerminate || $allowActivate) :
        ?>        
            <li<?php echo getListClassHtml('viewJobDetails'); ?>><a href="<?php echo url_for('pim/viewJobDetails?empNumber=' . $empNumber);?>"><?php echo __("Job"); ?></a></li>
        <?php endif; ?>        
        
        <?php // viewSalaryList
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('salary_details','salary_attachment','salary_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewSalaryList'); ?>><a href="<?php echo url_for('pim/viewSalaryList?empNumber=' . $empNumber);?>"><?php echo __("Salary"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewUsTaxExemptions
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('tax_exemptions','tax_attachment','tax_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead() && isTaxMenuEnabled()) :
        ?>        
            <li<?php echo getListClassHtml('viewUsTaxExemptions'); ?>><a href="<?php echo url_for('pim/viewUsTaxExemptions?empNumber=' . $empNumber);?>"><?php echo __("Tax Exemptions"); ?></a></li>
        <?php endif; ?>
        
        <?php // viewReportToDetails
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('supervisor','subordinates','report-to_attachment','report-to_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewReportToDetails'); ?>><a href="<?php echo url_for('pim/viewReportToDetails?empNumber=' . $empNumber);?>"><?php echo __("Report-to"); ?></a></li>
        <?php endif; ?>        
        
        <?php // viewQualifications
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('qualification_work','qualification_education','qualification_skills','qualification_languages','qualification_license','qualifications_attachment','qualifications_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewQualifications'); ?>><a href="<?php echo url_for('pim/viewQualifications?empNumber=' . $empNumber); ?>"><?php echo __("Qualifications"); ?></a></li>
        <?php endif; ?>        
        
        <?php // viewMemberships
            $dataGroupPermission = $userRoleManager->getDataGroupPermissions(array('membership','membership_attachment','membership_custom_fields'), array(), array(), $self, $entities);
            if($dataGroupPermission->canRead()) :
        ?>        
            <li<?php echo getListClassHtml('viewMemberships'); ?>><a href="<?php echo url_for('pim/viewMemberships?empNumber=' . $empNumber);?>"><?php echo __("Memberships"); ?></a></li>
        <?php endif; ?>
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'pim_left_menu_bottom')); ?>
    </ul>

</div> <!-- sidebar -->
