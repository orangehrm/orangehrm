<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 27/8/18
 * Time: 5:30 PM
 */
//use_javascript(plugin_web_path('orangehrmMaintenancePlugin', 'js/passWordValidation'));
?>
<div class="box">
    <?php include_partial('global/flash_messages'); ?>

    <div class="head">
        <h1><?php echo __('Verify Password'); ?></h1>
    </div>
    <div class="inner">

        <form id="frmPurgeEmployeeAuthenticate" method="post" action="<?php echo url_for('maintenance/purgeEmployee'); ?>">


            <div class="row">
                <fieldset>
                    <div class="input-field col s12 m12 l4">
                        <ol>
                            <?php echo $form->render(); ?>
                        </ol>
                    </div>
                </fieldset>
                <div class="input-field col s12 m12 l4">
                    <br>
                    <input type="submit" value="VERIFY">
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('form[id="frmPurgeEmployeeAuthenticate"]').validate({
        rules: {
            confirm_password: {
                required: true,
                maxlength: 250,
            }
        },
        messages: {
            confirm_password: 'This field is required',
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
</script>
