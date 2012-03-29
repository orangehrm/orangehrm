<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<div id="predefinedReportsOuter">
    <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
        <span style="font-weight: bold;"><?php echo isset($message) ? __($message) : ''; ?></span>
    </div>
    <div class="outerbox" style="width: 590px;">
        <div class="maincontent">
            <div class="mainHeading">
                <h2><?php echo __("Employee Reports"); ?></h2>
            </div>
            <br class="clear">
            <form action="<?php echo url_for("core/viewDefinedPredefinedReports"); ?>" id="searchForm" method="post">
                <div class="searchbox">
                    <label for="search_search"><?php echo __('Report Name') . ' :' ?></label>
                    <?php echo $searchForm['search']->render(); ?>
                    <input type="submit" class="searchButton" value="<?php echo __('Search') ?>" />
                    <input type="button" class="resetButton" value="<?php echo __('Reset') ?>" />
                    <?php echo $searchForm->renderHiddenFields(); ?>
                    <br class="clear"/>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
    <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>
    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

<?php include_component('core', 'ohrmList', $parmetersForListComponent); ?>

<style type="text/css">

    .searchButton,.resetButton{
        background: none repeat scroll 0 0 #999966 !important;
        border-color: #CCCC99 #666633 #666633 #CCCC99 !important;
        border-style: solid !important;
        border-width: 1px !important;
        color: #FFFFFF !important;
        cursor: default;
        font-size: 11px;
        font-weight: bold !important;
        min-width: 75px;
        width: auto;
    }

    table.data-table th, table.data-table tbody tr td {
        padding-left: 10px;
    }

    div#predefinedReportsOuter {
        width: 600px;
    }

</style>

<script type="text/javascript">

    var reportList = <?php echo str_replace('&quot;', "'", $reportJsonList); ?>;

    $(document).ready(function(){
        
        $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    
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
    
        $(".resetButton").click(function() {
            $("#search_search").val("");
            $('#searchForm').submit();
        });

        $("#search_search").autocomplete(reportList, {

            formatItem: function(item) {
                return unescapeHtml(item.name);
            }
            ,matchContains:true
        }).result(function(event, item) {
        }
    );
        
        $('#frmList_ohrmListComponent').submit(function (){
            $('#deleteConfirmation').dialog('open');
            return false;
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
            $("#deleteConfirmation").dialog("close");
            document.frmList_ohrmListComponent.submit();
        });
    
        $('#dialogCancelBtn').click(function() {
            $("#deleteConfirmation").dialog("close");
        });
        
    });
    
    function addPredefinedReport(){
        window.location.replace('<?php echo url_for('core/definePredefinedReport'); ?>');
    }

    function unescapeHtml(html) {
        var temp = document.createElement("div");
        temp.innerHTML = html;
        var result = temp.childNodes[0].nodeValue;
        temp.removeChild(temp.firstChild)
        return result;
    }
</script>
