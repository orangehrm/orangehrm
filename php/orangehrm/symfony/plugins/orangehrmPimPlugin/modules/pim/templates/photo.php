<div id="currentImage">
    <center>
        <a href="../../../../lib/controllers/CentralController.php?menu_no_top=hr&id=<?php echo $empNumber;?>&amp;capturemode=updatemode&amp;reqcode=EMP&pane=21">
            <img style="width:100px; height:120px;" alt="Employee Photo" src="<?php echo url_for("pim/viewPhoto?empNumber=". $empNumber); ?>" border="0"/>
        </a>
    <br />
    <span class="smallHelpText"><strong><?php echo $form->fullName; ?></strong></span>
    </center>
</div>