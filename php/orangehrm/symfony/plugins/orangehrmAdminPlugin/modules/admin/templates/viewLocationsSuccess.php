<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewLocationsSuccess')); 
?>

<div id="location-information" class="box searchForm toggableForm">
    
    <div class="head">
        <h1 id="searchLocationHeading"><?php echo __("Locations") ?></h1>
    </div>
    
    <div class="inner">

        <form name="frmSearchLocation" id="frmSearchLocation" method="post" action="<?php echo url_for('admin/viewLocations'); ?>" >

            <fieldset>
                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>
                
                <p>
                    <input type="button" class="addbutton" name="btnSave" id="btnSearch" value="<?php echo __("Search"); ?>" title="<?php echo __("Search"); ?>"/>
                    <input type="button" class="reset" name="btnReset" id="btnReset" value="<?php echo __("Reset"); ?>" title="<?php echo __("Reset"); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
        
        <form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/viewLocations'); ?>">
            <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo; ?>" />
            <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
        </form>
        
    </div>
    
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
    
</div>

<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

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
    var addLocationUrl = '<?php echo url_for('admin/location'); ?>';
    var viewLocationUrl = '<?php echo url_for('admin/viewLocations'); ?>';
</script>
