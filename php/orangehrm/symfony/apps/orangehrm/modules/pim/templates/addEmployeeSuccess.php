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

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<div class="formpage2col">

	<div class="outerbox">
		<div class="mainHeading"><h2><?php echo __("PIM : Add Employee"); ?></h2></div>
	    <?php echo message()?>

    <?php include_partial('global/form_errors', array('form' => $form));?>

	    <form id="frmEmp" method="post" action="<?php echo url_for('pim/addEmployee'); ?>">

        <?php echo $form['_csrf_token'];
              echo $form['txtEmployeeId']->renderLabel(__("Code"));
              echo $form['txtEmployeeId']->render(array('class'=>'formInputText'));
        ?>

		<br class="clear"/>

		<?php echo $form['txtEmpLastName']->renderLabel(__("Last Name") . '<span class="required">*</span>');
		      echo $form['txtEmpLastName']->render(array('class'=>'formInputText')); ?>

		<?php echo $form['txtEmpFirstName']->renderLabel(__("First Name") .'<span class="required">*</span>');
		      echo $form['txtEmpFirstName']->render(array('class'=>'formInputText')); ?>

		<br class="clear" />
		<?php echo $form['txtEmpMiddleName']->renderLabel(__("Middle Name"));
		      echo $form['txtEmpMiddleName']->render(array('class'=>'formInputText')); ?>

		<?php echo $form['txtEmpMiddleName']->renderLabel(__("Nick Name"));
		      echo $form['txtEmpNickName']->render(array('class'=>'formInputText')); ?>

		<?php echo $form['photofile']->renderLabel(__("Photo")); ?>
			  <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1000000" />
		<?php echo $form['photofile']->render(array('class'=>'fileselect')); ?>


	    <br class="clear"/>

        <div class="formbuttons">
			<input type="button" class="savebutton" id="btnEdit"
				onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo __("Save");?>" title="<?php echo __("Save");?>" />
			<input type="button" class="resetbutton" id="btnReset"
				onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				 value="<?php echo __("Reset");?>" />
        </div>
        </form>
	</div>
	<div class="requirednotice"><?php echo __('Fields marked with an asterisk #star are required', array('#star'=>'<span class="required">*</span>')); ?>.</div>
</div>
<script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {

		//Validate the form
		 $("#frmEmp").validate({

			 rules: {
			    txtEmpFirstName: { required: true },
			    txtEmpLastName: { required: true }
		 	 },
		 	 messages: {
		 		txtEmpFirstName: "<?php echo __('First Name Empty!')?>",
		 		txtEmpLastName: "<?php echo __('Last Name Empty!')?>"
		 	 },
		 	errorClass: "error2col",

		 	// needs improvement to handle two column layouts
		 	wrapper: "span",
		 	errorPlacement: function(error, element) {
				  var br = element.nextAll("br").eq(0);
				  var next = br.next();

				  if ( next.hasClass("error2col") ) {
					  error.insertAfter(next).css("clear", "both");
				  } else {
					  error.insertAfter(br).css("clear", "both").append("<br class=\"clear\"/>");
				  }
		 	},

		 	submitHandler: function(form) {

		        // check middle name
    			var confirmed = false;
    			var msg;
    			var middleName = $("#txtEmpMiddleName").get(0);

    			if (!(middleName.value == '') && !alpha(middleName)) {
    				msg = '<?php echo __("Middle Name contains numbers. Do you want to continue?");?>';
    			} else if ((middleName.value == '')) {
    				msg = '<?php echo __("Middle Name Empty. Do you want to continue?");?>';
    			}

    			if (!confirm(msg)) {
    				middleName.focus();
    				return;
    			}
		    	form.submit();
		    }

		 });

    	// Save button
		$("#btnEdit").click(function() {
			$('#frmEmp').submit();
		});

    	// Reset button
		$("#btnReset").click(function() {
			$('#frmEmp').reset();
		 });
     });
//]]>
</script>