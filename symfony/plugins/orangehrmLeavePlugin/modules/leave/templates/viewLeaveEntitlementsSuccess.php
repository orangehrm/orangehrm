<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>

<?php $firstDate = isset($form['employee']) ? 3: 2;?>
<style type="text/css">
    form#search_form li:nth-child(<?php echo $firstDate;?>) {
        width: auto;
        margin-right: 10px;
    }
    
    
</style>

<?php

use_javascripts_for_form($form);
use_stylesheets_for_form($form);

?>

<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>

<div class="box searchForm toggableForm" id="leave-entitlementsSearch">
    <div class="head">
        <h1><?php echo __($title);?></h1>
    </div>
    <div class="inner">
        <?php 
        if (!$showResultTable) {
            include_partial('global/flash_messages'); 
        }
        ?>
        <form id="search_form" name="frmLeaveEntitlementSearch" method="post" action="">

            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>
                
                <p>
                    <input type="button" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                </p>                
            </fieldset>
            
        </form>
        
    </div> <!-- inner -->
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div> <!-- employee-information -->

<?php if ($showResultTable) { ?>
    <?php include_component('core', 'ohrmList'); ?>
<?php } ?>


<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
        
    $(document).ready(function() {        
        
        $("#searchBtn").click(function() {
            $('#search_form').submit();
        });
        $('#btnAdd').click(function() {
            location.href = "<?php echo url_for('leave/addLeaveEntitlement') ?>?savedsearch=1";
        });        
       
        $('#btnDelete').attr('disabled','disabled');
        $("#ohrmList_chkSelectAll").click(function() {
            if($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled','disabled');
            }
            else {
                if($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled','disabled');
                }
            }
        });
        
        $(':checkbox[name*="chkSelectRow[]"]').click(function() {
            if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        });
        
        /* Delete confirmation controls: Begin */
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        /* Delete confirmation controls: End */
        
        $('#search_form').validate({
                rules: {
                    'entitlements[employee][empName]': {
                        required: true,
                        no_default_value: function(element) {

                            return {
                                defaults: $(element).data('typeHint')
                            }
                        }
                    },
                    'entitlements[date_from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,                                
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'entitlements[date_to]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        },
                        date_range: function() {
                            return {
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat,
                                fromDate:$("#date_from").val()
                            }
                        }
                    }
                },
                messages: {
                    'entitlements[employee][empName]':{
                        required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                        no_default_value:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                    },
                    'entitlements[date_from]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'entitlements[date_to]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    }
            }

        });
        
    });

</script>
