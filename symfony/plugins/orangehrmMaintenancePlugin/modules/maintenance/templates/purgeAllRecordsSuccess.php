<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 6/9/18
 * Time: 10:29 AM
 */
?>
<div class="box">
    <?php include_partial('global/flash_messages'); ?>

    <div class="head">
        <h1><?php echo __('Purge Employee Records'); ?></h1>
    </div>
    <div class="inner">
        <form id="frmPurgeEmployee" method="post" action="<?php echo url_for('maintenance/purgeEmployee'); ?>">
            <fieldset>
                <div class="input-field col s12 m12 l4">
                    <ol>
                        <?php echo $form->render(); ?>
                    </ol>
                </div>
            </fieldset>
            <div class="input-field col s12 m12 l4">
                <br>
                <input type="submit" value="PURGE">

            </div>
        </form>
    </div>
</div>

