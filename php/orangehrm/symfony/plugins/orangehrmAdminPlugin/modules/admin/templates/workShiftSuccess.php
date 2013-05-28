<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/workShiftSuccess')); ?>

<div id="workShift" class="box" <?php echo $hideForm ? "style='display:none'" : "";?> >
    
    <div class="head">
        <h1 id="workShiftHeading"><?php echo __("Work Shift"); ?></h1>
    </div>
    
    <div class="inner">

        <?php include_partial('global/form_errors', array('form' => $form)); ?>
        
        <form name="frmWorkShift" id="frmWorkShift" method="post" action="<?php echo url_for('admin/workShift'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>                    
                    <li>
                        <?php echo $form['name']->renderLabel(); ?>
                        <?php echo $form['name']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['workHours']->renderLabel(); ?>
                        <?php echo $form['workHours']->render(); ?>
                    </li>                   
                    <p id="selectManyTable">
                        <table border="0" width="45%" class="">
                            <tbody>
                                <tr>
                                    <td width="35%" style="font-weight:bold; height: 20px">
                                        <?php echo __("Available Employees"); ?>
                                    </td>
                                    <td width="30%"></td>
                                    <td width="35%" style="font-weight:bold;"><?php echo __("Assigned Employees"); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $form['availableEmp']->render(array("class" => "selectMany", "size" => 10, "style" => "width: 100%")); ?>	
                                    </td>
                                    <td align="center" style="vertical-align: middle">
                                        <a href="#" class="" id="btnAssignEmployee"><?php echo __("Add"). " >>"; ?></a><br /><br />
                                        <a href="#" class="delete" id="btnRemoveEmployee"><?php echo __("Remove") . " <<"; ?></a>
                                    </td>
                                    <td>
                                        <?php echo $form['assignedEmp']->render(array("class" => "selectMany", "size" => 10, "style" => "width: 100%")); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </p>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
        
    </div>
    
</div>

<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
	var employeeList = <?php echo $form->getEmployeeListAsJson();?>;
	var workShiftList = <?php echo $form->getWorkShiftListAsJson();?>;
        
        var defaultStartTime = '<?php echo $default['start_time'];?>';
        var defaultEndTime = '<?php echo $default['end_time'];?>';

	var workShiftInfoUrl = "<?php echo url_for("admin/getWorkShiftInfoJson?id="); ?>";
	var workShiftEmpInfoUrl = "<?php echo url_for("admin/getWorkShiftEmpInfoJson?id="); ?>";

	var lang_Required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var lang_addWorkShift = "<?php echo __("Add Work Shift"); ?>";
	var lang_editWorkShift = "<?php echo __("Edit Work Shift"); ?>";
	var lang_nameAlreadyExist = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
        var lang_FromTimeLessThanToTime = "<?php echo __('From time should be less than To time'); ?>";
</script>