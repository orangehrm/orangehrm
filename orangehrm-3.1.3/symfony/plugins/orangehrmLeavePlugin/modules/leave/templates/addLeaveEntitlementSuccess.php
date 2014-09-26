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
?>

<?php
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/addLeaveEntitlementSuccess.css'));
use_javascripts_for_form($form);
use_stylesheets_for_form($form);

?>

<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>

<div class="box" id="add-leave-entitlement">
    <div class="head">
        <h1><?php echo $addMode ? __("Add Leave Entitlement") : __('Edit Leave Entitlement');?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <form id="frmLeaveEntitlementAdd" name="frmLeaveEntitlementAdd" method="post" action="">

            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>            
                
                <p>
                    <input type="button" id="btnSave" value="<?php echo __("Save") ?>"/>
                    <input type="button" id="btnCancel" class="cancel" value="<?php echo __("Cancel") ?>"/>
                </p>                
            </fieldset>
            
        </form>
        
    </div> <!-- inner -->
    
</div> <!-- employee-information -->

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="noselection">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo 'OrangeHRM - ' . __('No matching employees'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __('No employees match the selected filters'); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->
<div class="modal hide" id="preview" style="width:500px">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo 'OrangeHRM - ' . __('Matching Employees'); ?></h3>
  </div>
  <div class="modal-body">
      <span><?php echo __('The selected leave entitlement will be applied to the following employees.');?></span>
      
      <div id="employee_list">  

      </div>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogConfirmBtn" value="<?php echo __('Confirm'); ?>" />
    <input type="button" class="cancel" data-dismiss="modal" id="bulkAssignCancelBtn" value="<?php echo __('Cancel'); ?>" />
    <div id="employee_loading" class="loading_message"><?php echo __('Loading') . '...';?></div>
  </div>
</div>

<!-- Confirmation box for employee entitlement-->
<div class="modal hide" id="employeeEntitlement" style="width:500px">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo 'OrangeHRM - ' . __('Updating Entitlement'); ?></h3>
  </div>
  <div class="modal-body">
      
      <ol id="employee_entitlement_update">  
          <li><?php echo __('Loading') . '...';?></li>
      </ol>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogUpdateEntitlementConfirmBtn" value="<?php echo __('Confirm'); ?>" />
    <input type="button" class="cancel" data-dismiss="modal" id="dialogUpdateEntitlementCancelBtn" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>

<!-- Confirmation box for employee entitlement-->
<div class="modal hide" id="bulkAssignWaitDlg" style="width:500px">
  <div class="modal-header">
    <h3><?php echo 'OrangeHRM - ' . __('Updating Entitlement'); ?></h3>
  </div>
  <div class="modal-body">
      <p id="buildAssignWait" class="loading_message"></p>
  </div>
</div>

<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_employee  = '<?php echo __("Employee") ?>';
    var lang_old_entitlement  = '<?php echo __("Old Entitlement") ?>';
    var lang_new_entitlement  = '<?php echo __("New Entitlement") ?>';
    var listUrl = '<?php echo url_for('leave/viewLeaveEntitlements?savedsearch=1');?>';
    var getCountUrl = '<?php echo url_for('leave/getFilteredEmployeeCountAjax');?>';
    var getEmployeeUrl = '<?php echo url_for('leave/getFilteredEmployeesEntitlementAjax');?>';
    var getEmployeeEntitlementUrl = '<?php echo url_for('leave/getEmployeeEntitlementAjax');?>';
    var lang_matchesOne = '<?php echo __('Matches one employee');?>';
    var lang_matchesMany = '<?php echo __('Matches %count% employees');?>';
    var lang_matchesNone = '<?php echo __('No matching employees');?>';
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_invalid = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_number = '<?php echo __("Should be a number with upto %count% decimal places", array('%count%' => 2)); ?>';
    var lang_valid_entitlement = '<?php echo __("Used amount exceeds the current amount"); ?>';
    var validEntitlemnetUrl =  '<?php echo url_for('leave/isValidEntitlemnetAjax');?>';
    var lang_Loading = '<?php echo __('Loading');?>';
    var lang_Employees = '<?php echo __('Employees');?>';
    var lang_NoResultsFound = '<?php echo __("No Records Found");?>';
    var lang_BulkAssignPleaseWait = '<?php echo __('Bulk Assigning Leave Entitlement to %count% Employees. Please Wait');?>';
    var lang_PleaseWait = '<?php echo __('Assigning Leave Entitlement. Please Wait');?>';
        
    var filterMatchingEmployees = 0;
    
    var mode = '<?php echo ($addMode)?'add':'update'; ?>';
    
</script>
