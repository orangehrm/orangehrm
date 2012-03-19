<?php use_stylesheet('indexSuccess.css') ?>
<?php use_javascript('jquery-1.3.2.js') ?>
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
    $.get('<?php echo url_for('upgrade/upgraderControl');?>', function(data) {
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