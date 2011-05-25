<?php 

use_stylesheet('../orangehrmPimPlugin/css/listCustomFieldsSuccess');
use_stylesheet(public_path('../../themes/orange/cssmessage'));
use_stylesheet(public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'));

use_javascript(public_path('../../scripts/jquery/ui/ui.core.js'));
use_javascript(public_path('../../scripts/jquery/ui/ui.dialog.js'));

$cssClass = '';

if (isset($messageType)) {
    $cssClass = "messageBalloon_{$messageType}";
}
?>

<div id="messagebar" class="<?php echo $cssClass;?>">
    <?php echo isset($message) ? $message : ''; ?>
</div>   
<div class="outerbox">
    <div class="maincontent">       
        
        <div class="mainHeading"><h2><?php echo __("Custom Fields") ?></h2></div>

        <div class="actionbar">
            <div class="actionbuttons">
<?php 
    $fieldsInUse = count($listCustomField);
    $fieldsLeft = CustomFields::MAX_FIELD_NUM - $fieldsInUse;
    $fieldsLeftMsg = '';
    
    if ($fieldsLeft == 0) {
        $fieldsLeftMsg = __("The maximum number of custom fields have been defined. No fields left.");        
    } else if ($fieldsLeft == 1) {
        $fieldsLeftMsg = __("1 Custom field left.");
    } else if ($fieldsLeft > 1) {
        $fieldsLeftMsg = $fieldsLeft . ' ' . __("Custom fields left.");
    }
?>
<?php if ($fieldsLeft > 0 ) { ?>                
                <input type="button" class="plainbtn" id="buttonAdd"
                       value="<?php echo __("Add") ?>" />
<?php } ?>

                <input type="button" class="plainbtn" id="buttonRemove"
                       value="<?php echo __("Delete") ?>" />    
                
                <span id="fieldsleft"><?php echo $fieldsLeftMsg;?></span>

            </div>
            <div class="noresultsbar"></div>
            <div class="pagingbar"> </div>
            <br class="clear" />
        </div>
        <br class="clear" />
        <form name="standardView" id="standardView" method="post" action="<?php echo url_for('pim/deleteCustomFields') ?>">
            <?php echo $deleteForm['_csrf_token']; ?>
            <input type="hidden" name="mode" id="mode" value=""></input>
            <table cellpadding="0" cellspacing="0" class="data-table">
                <thead>
                    <tr>
                        <td scope="col" class="fieldCheck">
                            <input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
                        </td>

                        <td scope="col">
                            <?php echo $sorter->sortLink('name', __('Custom Field Name '), '@customfield_list', ESC_RAW); ?>
                        </td>  	  
                        <td scope="col">
                            <?php echo $sorter->sortLink('screen', __('Screen'), '@customfield_list', ESC_RAW); ?>
                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('type', __('Field Type'), '@customfield_list', ESC_RAW); ?>
                        </td>

                    </tr>
                </thead>

                <tbody>
                    <?php
                    $row = 0;
                    foreach ($listCustomField as $customField) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $row = $row + 1;
                        ?>
                        <tr class="<?php echo $cssClass ?>">
                            <td >
                                <input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' value='<?php echo $customField->getFieldNum() ?>' />
                            </td>
                            <td>
                                <a href="<?php echo public_path('../../lib/controllers/CentralController.php?id=' . $customField->getFieldNum() . '&amp;uniqcode=CTM&amp;capturemode=updatemode');?>" >
                                <?php echo $customField->getName() ?></a>
                                <!-- <a href="<?php echo url_for('pim/updateCustomField?id=' . $customField->getFieldNum()) ?>"><?php echo $customField->getName() ?></a> -->
                            </td>
                            <td>
                                <?php echo $customField->getScreen();?>
                            </td>
                            <td>
                                <?php 
                                $type = $customField->getType();
                                $typeDesc = '';
                                if ($type == CustomFields::FIELD_TYPE_STRING) {
                                    $typeDesc = __("Text or Number");
                                } else if ($type == CustomFields::FIELD_TYPE_SELECT) {
                                    $typeDesc = __("Drop Down");
                                }
                                echo $typeDesc;
                                ?>
                            </td>


                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
<div id="deleteConfirmation" title="OrangeHRM - Confirmation Required" style="display: none;">
    <span id="deleteConfirmMsg">Are you sure you want to delete selected custom field(s)?</span>
    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __("Delete");?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __("Cancel");?>" />
    </div>
</div>


<script type="text/javascript">

    $(document).ready(function() {

        //When click add button 
        $("#buttonAdd").click(function() {
            $('#messagebar').text('').attr('class', '');            
            location.href = "<?php echo public_path('../../lib/controllers/CentralController.php?uniqcode=CTM&capturemode=addmode');?>";

        });

        // When Click Main Tick box
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')){
			
                $('.innercheckbox').attr('checked','checked');
            }else{
                $('.innercheckbox').removeAttr('checked');
            }
        });

        $(".innercheckbox").click(function() {
            if($(this).attr('checked'))
            {
			
            }else
            {
                $('#allCheck').removeAttr('checked');
            }
        });

        //When click remove button
        $("#buttonRemove").click(function(event) {

            event.preventDefault();

            var checked = $('#standardView input.checkbox:checked').length;

            if ( checked == 0) {
                $('#messagebar').text("Please Select At Least One Custom Field To Delete").attr('class', 'messageBalloon_notice');
            } else {
                $('#messagebar').text('').attr('class', ''); 
                
                var fields = '';
                $('#standardView input.checkbox:checked').each(function(index) {
                    var name = $(this).parent().next().find('a').text().trim();
                    if (index == 0) {
                        fields = name;                      
                    } else {
                        fields = fields + ', ' + name;
                    }
                });
                
                var confirmMsg =  fields + ' <?php echo __("will be deleted from all employees' records. Do you want to continue?");?>';
                if (checked == 1) {
                    confirmMsg = '<?php echo __('Field ');?>' + confirmMsg;
                }
                else {
                    confirmMsg = '<?php echo __('Fields ');?>' + confirmMsg;
                }
                
                $('span#deleteConfirmMsg').text(confirmMsg);
                
                $('#deleteConfirmation').dialog('open');
                return false;
            }
        });

        $("#deleteConfirmation").dialog({
            autoOpen: false,
            modal: true,
            width: 325,
            height: 50,
            position: 'middle',
            open: function() {
              $('#dialogCancelBtn').focus();
            }
        });

        $('#dialogDeleteBtn').click(function() {
            $("#mode").attr('value', 'delete');
            $("#standardView").submit();
        });
        
        $('#dialogCancelBtn').click(function() {
            $("#deleteConfirmation").dialog("close");
        });
	  	
    });

</script>

