<?php use_stylesheet('executeDbChangeSuccess.css') ?>
<?php use_javascript('jquery.js') ?>
<?php use_javascript('executeDbChangeSuccess.js') ?>
<div>
    <h2><?php echo __('Applying Database Changes')?></h2>
    <p>In this step, applying database changes is taking place. This may take some time. <br />Please don't close the window till progress become 100%.</p>
</div>
<div id="errorDisplay" class="messageBalloon_warning">
    <span><?php echo __("Error Occurred").": "?><a href=""><?php echo __("Show Details")?></a></span>
</div>
<div id="logContainer">
    <textarea></textarea>
</div>

<div id="divProgressBarContainer" class="statusValue">
    <span style="width:200px; display: block; float: left; height: 10px; border: solid 1px #000000;">
        <span id="progressBar" style="width: 0%;">&nbsp;</span>
    </span>
    &nbsp;<span id="spanProgressPercentage">0%</span>
</div>
<div>
    <form action="<?php echo url_for('upgrade/executeDbChange');?>" name="databaseChangeForm" method="post">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <input type="button" name="dbChangeStartBtn" id="dbChangeStartBtn" value="<?php echo __('Start')?>"/>
                        <input type="submit" name="dbChangeProceedBtn" id="dbChangeProceedBtn" value="<?php echo __('Proceed')?>"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
    var upgraderControllerUrl = '<?php echo url_for('upgrade/dbChangeControl');?>';
    var showLogUrl = '<?php echo url_for('upgrade/showLog');?>';
    var lang_failedToUpdate = '<?php echo __('Falid to Update')?>';
    var tasks = new Array();
    <?php
        for($i=0; $i < count($schemaIncremantArray) ; $i++)
                {
            echo "tasks[$i]='".$schemaIncremantArray[$i]."';\n";
        }
     ?>
</script>
