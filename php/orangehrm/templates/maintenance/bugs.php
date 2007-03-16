<html>
<head>
<title>Bugs-Add</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php require_once ROOT_PATH . '/scripts/archive.js'; ?>
<?php require_once ROOT_PATH . '/scripts/octopus.js'; ?>

<script>

    function goBack() {
        location.href = "./CentralController.php?mtcode=<?php echo $this->getArr['mtcode']?>&VIEW=MAIN";
    }

    function addSave() {

        if(document.frmBugs.category_id.value=='100') {
            alert("Please select a bug category");
            document.frmBugs.cmbSource.focus();
            return;
        }

        if(document.frmBugs.cmbModule.value=='0') {
            alert("Please select a module");
            document.frmBugs.cmbModulse.focus();
            return;
        }

        if (document.frmBugs.summary.value == '') {
            alert ("Please specify the bug summary");
            return false;
        }

        if (document.frmBugs.txtDescription.value == '') {
            alert ("Please specify the bug description");
            return false;
        }

        // validate email if supplied
        var email = document.frmBugs.txtEmail.value;
        if (email != '') {
            if( !checkEmail(email) ){
                alert ("The email entered is not valid");
                return false;
            }
        }

        document.frmBugs.sqlState.value = "NewRecord";
        document.frmBugs.submit();
    }

</script>

    <link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 75px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10px;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 250px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .version_label {
        display: block;
        float: left;
        width: 150px;
        font-weight: bold;
        margin-left: 10px;
        margin-top: 10px;
    }

    .roundbox {
        margin-top: 50px;
        margin-left: 0px;
    }

    .roundbox_content {
        padding:15px;
    }
    -->
    </style>

    </head>

    <body>
    <h2>Report Bugs</h2>

    <form name="frmBugs" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mtcode=<?php echo $this->getArr['mtcode']?>">
        <input type="hidden" name="sqlState" value="">

        <font color="red" face="Verdana, Arial, Helvetica, sans-serif">
            <?php
            if (isset($this->getArr['message'])) {
                $expString  = $this->getArr['message'];
                $expString = explode ("%",$expString);
                $length = sizeof($expString);
                for ($x=0; $x < $length; $x++) {
                    echo " " . $expString[$x];
                }
            }
            ?>

        </font>


        <div class="roundbox">

            <label for="dummy1">Found in Release</label><div class="version_label">v2.1</div></br>
            <input type="hidden" readonly name="artifact_group_id" value="699847">

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" tabindex="1">
                <option VALUE="100">None</OPTION>
                <option VALUE="803416">Interface</OPTION>
                <OPTION VALUE="813016">PHP</OPTION>
                <OPTION VALUE="813015">Database</OPTION>
                <OPTION VALUE="864255">Language Pack</OPTION>
                <OPTION VALUE="883366">Web-Installer</OPTION>
            </select><br>

            <label for="cmbModule">Module</label>
            <select id="cmbModule" name="cmbModule" tabindex="2">
                <option value="0">--Select Module--</option>
                <?php  $module = $this->popArr['module'];
                for($c=0;$c < count($module);$c++)
                echo "<option>" . $module[$c][1] ."</option>";
                ?>
            </select><br>

            <label for="priority">Priority</label>
            <select id="priority" name="priority" tabindex="3">
                <option value="1">1 - Lowest</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5" selected="selected">5 - Medium</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9 - Highest</option>
            </select><br>

            <label for="summary">Summary</label>
            <input type="text" id="summary" name="summary" tabindex="4">

            <div style="float:right">
                <label for="txtEmail">Your Email</label>
                <input type="text" id="txtEmail" name="txtEmail" tabindex="5"
                    value="<?php echo isset($_POST['txtEmail']) ? $_POST['txtEmail'] : ''?>">
            </div><br>

            <label for="txtDescription">Description</label>
            <textarea name='txtDescription' id="txtDescription" rows="3" cols="30" tabindex="6"></textarea><br>

            <div align="center" >
            <img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">

            <img onClick="document.frmBugs.reset();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg">

            </div>
        </div>
        <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
        </script>
    </form>

</body>
</html>
