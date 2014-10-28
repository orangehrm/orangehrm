<?php

if (!empty($customFieldList) && (count($customFieldList) > 0)) {
    $editMode = false;
    $allowEdit = isset($permission) ? $permission->canUpdate() : true;
    $allowDel = true;
?>
<?php if ($form->hasErrors()): ?>
        <span class="error">
    <?php
        echo $form->renderGlobalErrors();

        foreach ($form->getWidgetSchema()->getPositions() as $widgetName) {
            echo $form[$widgetName]->renderError();
        }
    ?>
    </span>
<?php endif; ?>
        <a name="custom"> </a>
        <?php if ($permission->canRead()) { ?>
        <div class="single">
            <div class="head"><h1><?php echo __('Custom Fields'); ?></h1></div>
            
            <div class="inner">
                
                <?php include_partial('global/flash_messages'); ?>
            
            <form name="frmEmpCustomFields" id="frmEmpCustomFields" method="post"
                  action="<?php echo url_for('pim/updateCustomFields?empNumber=' . $employee->empNumber . '&screen=' . $screen); ?>">

        <?php echo $form['_csrf_token']; ?>
        <input type="hidden" name="EmpID" value="<?php echo $employee->empNumber; ?>"/>

        <ol>
            <?php
            $disabled = $editMode ? '' : 'disabled="disabled"';
            foreach ($customFieldList as $customField) {
                $fieldName = "custom" . $customField->getId();
                $value = $employee[$fieldName];
            ?>
                <li>
                    <label><?php echo $customField->name; ?></label>

                    <?php
                    if ($customField->type == CustomField::FIELD_TYPE_SELECT) {
                        $options = $customField->getOptions(); ?>
                        <select <?php echo $disabled; ?> name="<?php echo $fieldName; ?>" class="editable" >

                            <option value=""><?php echo "-- " . __('Select') . " --"; ?></option>
                        <?php
                        foreach ($options as $option) {
                            $option = trim($option);
                            $selected = ($option == $value) ? "selected='selected'" : ""; ?>
                            <option <?php echo $selected; ?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
                        <?php
                        }
                        ?>
                    </select>

                    <?php
                    } else {
                    ?>
                        <input class="formInputText editable" type="text" <?php echo $disabled; ?> name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>" value="<?php echo $value; ?>"/>
                    <?php } ?>
                
            </li>
            <?php
                }
            ?>
            </ol>
        <?php if (count($customFieldList) > 0) {
        ?>
                    <p>
        <?php if ($allowEdit) :?>
            <input type="button" name="btnEditCustom" id="btnEditCustom" value="<?php echo $editMode ? __("Save") : __("Edit"); ?>" />
        <?php endif; ?>
         </p>
        <?php }
        ?>
            </form>
                
            </div> <!-- inner -->    
            
        </div> <!-- single -->
        <?php }?>   

        <script type="text/javascript">
            //<![CDATA[

            $(document).ready(function() {

                $("#frmEmpCustomFields").data('edit', <?php echo $editMode ? '0' : '1' ?>);

                $('#btnEditCustom').click(function() {
                    var editMode = $("#frmEmpCustomFields").data('edit');
                    if (editMode == 1) {
                        $('#frmEmpCustomFields .editable').removeAttr("disabled");
                        $("#frmEmpCustomFields").data('edit', 0);
                        this.value = "<?php echo __("Save"); ?>";
                    } else {
                            $('#frmEmpCustomFields').submit();
                    }
                });

                $("#frmEmpCustomFields").validate();
                $("#frmEmpCustomFields .formInputText").each(function (item) {
                    $(this).rules("add", {
                        required: false,
                        maxlength: 250,
                        messages: {
                              maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>'
                          }
                    });
                });        
            });


            //]]>
        </script>

<?php } ?>

