<script src="js/jquery-1.3.2.js" type="text/javascript" charset="utf-8"></script>
<?php

?>
<style type="text/css">
<!--
#progressBar {
    background-color: #FF9900;
    display: block;
    height: 10px;
}

#btnSubmit {
    display:none;
}
-->
</style>

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

<script language="javascript" type="text/javascript">
var tasks = new Array();
tasks[0] = 0;
var count = 0;
var toBreak = false;
getJsonResponse(count, tasks.length);

function getJsonResponse(count, length) {
    $.get('UpgradeController.php?task='+count, function(data) {
        var myObject = JSON.parse(data);
        if((myObject.progress == 100)) {
            displayProgress(((count+1)*100)/length);
            if((count +1 ) < length) {
                getJsonResponse(count+1, length);
            }
        } else {
            alert('Falid to Update');
        }
    });
}
    
    
function displayProgress(percentage) {
    $("#divProgressBarContainer span span").width(percentage+'%');
    $("#spanProgressPercentage").html(percentage+'%');
}
</script>