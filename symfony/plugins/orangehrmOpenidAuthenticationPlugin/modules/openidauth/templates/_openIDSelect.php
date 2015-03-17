<?php
/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
 */
?>
<div style="text-align: center;padding: 10px;">
    <form id="openIDForm" method="post" action="<?php echo url_for('openidauth/openIdCredentials'); ?>" style="text-align: center">

        <?php if (isset($form)) {
            echo $form['_csrf_token']->render();
            echo __('Alternative Login');
            ?> : <?php echo $form['openIdProvider']->render(); ?>

            <input type="button" value="<?php echo __('Login'); ?>" name="openIdLogin" id="openIdLogin"/>
        <?php } ?>
    </form>
</div>

<style>
    #openID{
        text-align: center;
        padding: 5px;
        margin-bottom: 5px;
    }
    #openIDForm{

    }
    #openIDForm input {
        background-color: red;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('#openIdLogin').attr("disabled", "disabled");
        $('#openIdProvider').change(function() {
            if ($("#openIdProvider option:selected").val() == '') {
                $('#openIdLogin').attr("disabled", "disabled");
            } else {
                $('#openIdLogin').removeAttr('disabled');
            }
        });
        
        $('#openIdLogin').click(function(){          
            $('#openIDForm').submit();
        });
    });
</script>