<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<div id="predefinedReportsOuter">
<div class="outerbox" style="width: 600px;">
    <div class="maincontent">
        <div class="mainHeading">
            <h2><?php echo __("View Employee Reports"); ?></h2>
        </div>
        <br class="clear">
        <form action="<?php echo url_for("core/viewPredefinedReports"); ?>" id="searchForm" method="post">
            <div class="searchbox">
                <label for="search_search"><?php echo __('Report Name:') ?></label>
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
        $(".resetButton").click(function() {
            $("#search_search").val("");
            $('#searchForm').submit();
        });

        $("#search_search").autocomplete(reportList, {

			formatItem: function(item) {

				return item.name;
			}
			,matchContains:true
		}).result(function(event, item) {
		}
	);
    });

</script>
