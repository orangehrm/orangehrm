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

<form name="frmDataImport" method="post" action="./">
    <input type="hidden" name="hdnState" value="oldConstraints" />
    <input type="submit" name="btnSubmit" value="Continue"  size="40" id="btnSubmit" />
</form>
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