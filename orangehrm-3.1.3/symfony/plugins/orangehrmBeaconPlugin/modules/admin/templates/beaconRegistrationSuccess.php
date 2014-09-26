<?php

?>
<script type="text/css">
    #registrationList label {
        width: 300px;
    }
</script>

<div class = "box">
    <div class="head">
        <h1><?php echo __('BEACON');?></h1>
    </div>
     <div class="inner" id="addEmployeeTbl">
         
         
        <?php include_partial('global/flash_messages'); ?>     
        
        <form id="frmAddEmp" method="post" action="<?php echo url_for('admin/beaconRegistration'); ?>" 
              enctype="multipart/form-data">
            
              <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                         <h3 style="float: left;"><?php echo __('Contribute to OrangeHRM by sending us your usage data');?></h3>
         <p style="float: left;"><?php echo __('The usage statistics that will be collected will help us understand user requirements better and help streamline the application. 
                We will not collect any user specific data nor attempt to gain knowledge of your organization via the data collected.');?></p>
                    </li>
                    <li id = "registrationList">
                        <?php echo $form['registration']->render(); ?>
                        <?php echo $form['registration']->renderLabel(); ?>
                        
                    </li>
                </ol>
            </fieldset>
            <p><input type="submit" value="Save"/></p>
        </form>
     </div>
</div>

