<?php 

if (!empty($customFieldList) && (count($customFieldList) > 0)) {
$editMode = false;
$allowEdit = true;
$allowDel = true;
?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Custom Fields'); ?></h2></div>
<form name="frmEmpCustomFields" id="frmEmpCustomFields" method="post" 
      action="<?php echo url_for('pim/updateCustomFields?empNumber='.$employee->empNumber . '&screen='.$screen); ?>">
    
    <?php echo $form['_csrf_token']; ?>
    <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber;?>"/>

    <ul>
        <?php
        $disabled = $editMode ? '' : 'disabled="disabled"';
        foreach ($customFieldList as $customField){
            $fieldName = "custom" . $customField->field_num;
            $value = $employee[$fieldName];
            ?>
            <li>
                <label class="sizeL"><?php echo $customField->name;?></label>

                <div class="input_container">
                    <?php
                    if ($customField->type == CustomFields::FIELD_TYPE_SELECT){
                        $options = $customField->getOptions();?>
                        <select <?php echo $disabled;?> name="<?php echo $fieldName; ?>" class="formSelect" >
                                <?php
                                foreach($options as $option){
                                    $option = trim($option);
                                    $selected = ($option == $value) ? "selected='selected'" : "";?>
                                    <option <?php echo $selected; ?> value="<?php echo $option;?>"><?php echo $option;?></option>
                                <?php
                                }
                                ?>
                        </select>
                       <?php
                    }
                    else{?>
                        <input class="formInputText" type="text" size="20" <?php echo $disabled;?> name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>" value="<?php echo $value;?>"/>
                    <?php
                    }?>
                </div>
                <div class="clear"></div>
            </li>
        <?php
        }

        ?>
    </ul>
        <?php if (count($customFieldList) > 0)
        { ?>
<div class="formbuttons">
        <?php if ($allowEdit)
        { ?>
    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="btnEditCustom" id="btnEditCustom"
           value="<?php echo $editMode ? __("Save") : __("Edit");?>"
           title="<?php echo $editMode ? __("Save") : __("Edit");?>"
           onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            <?php } ?>
</div>
        <?php }

?>
</form>
</div>
</div>


<script type="text/javascript">
    //<![CDATA[
    
$(document).ready(function() {
    
        $("#frmEmpCustomFields").data('edit', <?php echo $editMode ? '0' : '1' ?>);

        $('#btnEditCustom').click(function() {
            var editMode = $("#frmEmpCustomFields").data('edit');
            if (editMode == 1) {
                $('#frmEmpCustomFields input:disabled').attr('disabled', '');
                $('#frmEmpCustomFields select:disabled').attr('disabled', '');
                $("#frmEmpCustomFields").data('edit', 0);
                this.value = "<?php echo __("Save");?>";
                this.title = "<?php echo __("Save");?>";
            } else {
                $('#frmEmpCustomFields').submit();
            }
        });
});
    //]]>
</script>

<?php } ?>

