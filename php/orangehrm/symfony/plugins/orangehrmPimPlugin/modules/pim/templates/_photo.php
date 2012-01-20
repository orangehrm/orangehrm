<div id="currentImage" style="width:150px;height:180px;overflow:hidden;">
    <center>
        <a href="<?php echo url_for('pim/viewPhotograph?empNumber=' . $empNumber); ?>">
            <img alt="Employee Photo" src="<?php echo url_for("pim/viewPhoto?empNumber=". $empNumber); ?>" border="0" id="empPic" 
                 style="width:100%;visiblity:hidden;"/>
        </a>
<?php if ($editMode) { ?>
        <div class="smallHelpText">[<?php echo __('Dimensions'); ?> 150x180]</div>
<?php } ?>
        <span class="smallHelpText"><strong><?php echo $fullName;?></strong></span>
    </center>
</div>
<script type="text/javascript">
    //<![CDATA[
    function imageResize() {
        var imgHeight = $("#empPic").attr("height");
        var imgWidth = $("#empPic").attr("width");
        var newHeight = 0;
        var newWidth = 0;
        
        $('#currentImage').css('height', 'auto');

        //algorithm for image resizing
        //resizing by width - assuming width = 150,
        //resizing by height - assuming height = 180

        var propHeight = Math.floor((imgHeight/imgWidth) * 150);
        var propWidth = Math.floor((imgWidth/imgHeight) * 180);

        if (isNaN(propHeight) || (propHeight <= 180)) {
            newHeight = propHeight;
            newWidth = 150;
        }

        if (isNaN(propWidth) || (propWidth <= 150)) {
            newWidth = propWidth;
            newHeight = 180;
        }

        if(fileModified == 1) {
            newWidth = newImgWidth;
            newHeight = newImgHeight;
        }

        $("#empPic").attr("height", newHeight);
        $("#empPic").attr("width", newWidth);
        $("#empPic").attr("visibility", "visible");
    }
    
    $(document).ready(function() {
        imageResize();
    });
    
    //]]>
</script>