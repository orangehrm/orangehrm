<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 7/9/18
 * Time: 11:57 AM
 */
?>
<div class="box">
    <?php include_partial('global/flash_messages'); ?>

    <div class="head">
        <h1><?php echo __('Get All Employee Records'); ?></h1>
    </div>
    <div class="inner">
        <form id="frmPurgeEmployee" method="post" action="<?php echo url_for('purge/getEmployeeData'); ?>">
            <fieldset>
                <div class="input-field col s12 m12 l4">
                    <ol>
                        <?php echo $form->render() ?>
                    </ol>
                </div>
            </fieldset>
            <div class="input-field col s12 m12 l4">
                <br>
                <input type="submit" value="Download">
            </div>
        </form>
    </div>
</div>
