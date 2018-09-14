<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 6/9/18
 * Time: 10:29 AM
 */
?>
<form id="frmPurgeEmployee" method="post" action="<?php echo url_for('maintenance/purgeEmployee'); ?>">

    <div class="box">
        <?php include_partial('global/flash_messages'); ?>

        <div class="head">
            <h1><?php echo __('Purge Employee Records'); ?></h1>
        </div>

        <div class="inner">
            <fieldset>
                <div class="input-field col s12 m12 l4">
                    <ol>
                        <?php echo $form->render(); ?>
                    </ol>
                </div>
            </fieldset>
            <div class="input-field col s12 m12 l4">
                <br>
                <input type="button" value="Search">
            </div>

        </div>

    </div>
    <div class="box" id="selected_employee">
        <div class="head">
            <h1><?php echo __('Selected Employee'); ?></h1>
        </div>
        <div class="inner">
            <div class="row">
                <div class="col s12 m3 l3 empImage">
                    <img class="circle" style="width:128px; height:128px;"/>
                </div>
            </div>
            <div class="input-field col s12 m12 l4">
                <br>
                <input type="submit" value="Purge">
            </div>
        </div>
    </div>
</form>

<script>
var x =document.getElementById("selected_employee");
</script>
