<div class="box">

<?php if (!$authorized) { ?>

<div class="message warning">
    <?php echo __(CommonMessages::CREDENTIALS_REQUIRED) ?> 
</div>

<?php } else { ?>

    <div class="head">
        <h1><?php echo __('Add OAuth Client'); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>        
        <form id="oAuthClientRegistrationForm" method="post" action="<?php echo url_for('admin/registerOAuthClient'); ?>" 
              enctype="multipart/form-data">
            <fieldset>

                <ol>
                    <?php echo $form->render(); ?>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>




                </ol>
                <ol>
                    <dive style="float: left;width: 425px;margin-top: 9px;font-size: 12px;" >
                        <p> <b>API Documentation : </b> <a  target="_blank"  href="https://orangehrm.github.io/orangehrm-api-doc/"> API Documentation</a>.</p>
                        <br>
                        <p><b>PHP Sample App :</b><a  target="_blank"  href="https://github.com/orangehrm/api-sample-app-php"> Notification Dashboard</a>.</p>
                    </dive>

                </ol>

                <p>
                    <input type="submit" class="" id="btnSave" value="<?php echo __("Save"); ?>"  />
                </p>
            </fieldset>
        </form>
    </div>

<?php } ?>
    
</div>
