<?php echo stylesheet_tag('../orangehrmPimPlugin/css/addEmployeeSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/addEmployeeSuccess'); ?>
<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_firstNameRequired = "<?php echo __("First Name is required"); ?>";
    var lang_lastNameRequired = "<?php echo __("Last Name is required"); ?>";
    var lang_userNameRequired = "<?php echo __("User Name should have at least 5 characters"); ?>";
    var lang_passwordRequired = "<?php echo __("Password should have at least 4 characters"); ?>";
    var lang_unMatchingPassword = "<?php echo __("Password and confirm password should be same"); ?>";
    var lang_statusRequired = "<?php echo __("Please select a status");?>";
    var cancelNavigateUrl = "<?php echo public_path("../../index.php?menu_no_top=hr");?>";
    var createUserAccount = "<?php echo $createUserAccount;?>";

    //]]>
</script>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 700px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Add Employee'); ?></h2></div>
    <div>
        <form id="frmAddEmp" method="post" action="<?php echo url_for('pim/addEmployee'); ?>" enctype="multipart/form-data">
            <?php echo $form['_csrf_token']; ?> 
            <?php echo $form['empNumber']->render(); ?>
            <table width="97%" border="0" align="center">
                <tr>
                    <td width="123">&nbsp;&nbsp;<?php echo __('Full Name'); ?></td>
                    <td valign="top"><?php echo $form['firstName']->render(array("class" => "formInputText", "maxlength" => 30)); ?><br class="clear" /></td>
                    <td valign="top"><?php echo $form['middleName']->render(array("class" => "formInputText", "maxlength" => 30)); ?></td>
                    <td valign="top"><?php echo $form['lastName']->render(array("class" => "formInputText", "maxlength" => 30)); ?><br class="clear" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="helpText"><?php echo __('First Name'); ?><span class="required">*</span></td>
                    <td class="helpText"><?php echo __('Middle Name'); ?></td>
                    <td class="helpText"><?php echo __('Last Name'); ?><span class="required">*</span></td>
                </tr>
            </table>
            
            <div>
                <?php echo $form['employeeId']->renderLabel(__('Employee Id')); ?>
                <?php echo $form['employeeId']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
                <br class="clear" />

                <?php echo $form['photofile']->renderLabel(__('Photograph')); ?>
                <?php echo $form['photofile']->render(array("class" => "duplexBox")); ?><span class="helpText">(<?php echo __("Maximum File Size: 1 MB"); ?>)</span>
                <br class="clear" />
            </div>
            <input type="checkbox" id="chkLogin" /> <label id="chkLoginLbl" style="width:117px;"><?php echo __('Create Login <br />Details');?></label>
            
            <div class="hrLine" id="lineSeperator"></div>
            
            <!-- create login section starts here -->
            <table width="95%" border="0" id="loginSection" align="center">
                <tr>
                    <td width="115"><?php echo __('User Name'); ?><span class="required">*</span></td>
                    <td><?php echo $form['user_name']->render(array("class" => "formInputText", "maxlength" => 20)); ?><br class="clear" /></td>
                    <td><?php echo __('Status'); ?><span class="required">*</span></td>
                    <td><?php echo $form['status']->render(array("class" => "formInputText")); ?><br class="clear" /></td>
                </tr>
                <tr>
                    <td><?php echo __('Password'); ?><span class="required">*</span></td>
                    <td><?php echo $form['user_password']->render(array("class" => "formInputText", "maxlength" => 20)); ?><br class="clear" /></td>
                    <td><?php echo __('Confirm Password'); ?><span class="required">*</span></td>
                    <td><?php echo $form['re_password']->render(array("class" => "formInputText", "maxlength" => 20)); ?><br class="clear" /></td> 
                </tr>
            </table>


            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>"  />
                <input type="button" class="savebutton" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
</div>
<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>