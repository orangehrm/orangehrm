<?php use_stylesheet('executeDbChangeSuccess.css') ?>
<?php use_javascript('jquery-1.3.2.js') ?>
<?php use_javascript('executeDbChangeSuccess.js') ?>
<h2>Upgrade Current Database</h2>

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
                        
                    </td>
                    <td>
                        <input type="button" name="dbChangeStartBtn" id="dbChangeStartBtn" value="Start"/>
                        <input type="submit" name="dbChangeProceedBtn" id="dbChangeProceedBtn" value="Proceed"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
    var upgraderControllerUrl = '<?php echo url_for('upgrade/dbChangeControl');?>';
    var tasks = new Array();
    <?php
        for($i=0; $i < count($schemaIncremantArray) ; $i++)
                {
            echo "tasks[$i]='".$schemaIncremantArray[$i]."';\n";
        }
     ?>
</script>
