<?php
$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$formAction="{$formAction}&id={$this->getArr['uniqcode']}&capturemode=updatemode";
}
?>
<html>
<head>
<title>Customer</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php require_once ROOT_PATH . '/scripts/archive.js'; ?>
<?php require_once ROOT_PATH . '/scripts/octopus.js'; ?>

<script>

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }

    function addSave() {

        if(document.frmCustomor.txtcusid.value=='') {
            alert("Please specify the custormer Id");
            document.frmCustomor.txtcusid.focus();
            return;
        }

        if (document.frmCustomor.txtname.value == '') {
            alert ("Please specify the name");
            return false;
        }


        if (document.frmCustomor.txtcus_description.value == '') {
            alert ("Please specify the Description");
            return false;
        }

        document.frmCustomor.sqlState.value = "NewRecord";
        document.frmCustomor.submit();
    }

  function addUpdate() {

		if(document.frmCustomor.txtcusid.value=='') {
            alert("Please specify the custormer Id");
            document.frmCustomor.txtcusid.focus();
            return;
        }

	 	if (document.frmCustomor.txtname.value == '') {
            alert ("Please specify the name");
            return false;
        }
        if (document.frmCustomor.txtcus_description.value == '') {
            alert ("Please specify the Description");
            return false;
        }


		document.frmCustomor.sqlState.value  = "UpdateRecord";
		document.frmCustomor.submit();
	}

	function clearAll() {
		if(document.Edit.title!='Save')
			return;
			document.frmCustomor.txtcusid.value=='';
			document.frmCustomor.txtname.value == '';
			document.frmCustomor.txtcus_description.value == ''
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
    <h2>Customer</h2>
  <form name="frmCustomor" method="post" action="<?php echo $formAction; ?>">
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
      <?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
            <label for="customercode">Code</label>
            <input type="text" id="id" name="txtcusid" value= <?php echo $this->popArr['newID']; ?> tabindex="1"/>
            <br/>
			<label for="txtname">Name</label>
            <input type="text" id="name" name="txtname" tabindex="2"/>
			<br/>
            <label for="txtDescription">Description</label>
            <textarea name='txtcus_description' id="description" rows="3" cols="30" tabindex="3"></textarea>
            <br>
       <?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {

		$message = $this->popArr['editArr'];

	?>
		<label for="customercode">Code</label>
            <input type="text" id="id" name="txtcusid" value= <?php echo $message->getCustomerId(); ?> tabindex="1"/>
            <br/>
			<label for="txtname">Name</label>
            <input type="text" id="name" name="txtname" value= <?php echo $message->getCustomerName(); ?> tabindex="2"/>
			<br/>
            <label for="txtDescription">Description</label>
            <textarea name='txtcus_description' id="description" rows="3" cols="30" tabindex="3"><?php echo $message->getCustomerDescription(); ?></textarea><br>
			<br/>
		<?php } ?>
            <div align="center" >
            <img onClick="addUpdate();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">

            <img onClick="document.frmCustomor.reset();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg">

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