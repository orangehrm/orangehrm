<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
        <div class="head">
            <h1><?php echo __('Photograph'); ?></h1>
        </div> <!-- head -->
        
        <div class="inner">
                     
            <?php if (($photographPermissions->canUpdate()) || ($photographPermissions->canDelete())) : ?>
            
            <?php include_partial('global/flash_messages'); ?>
            
            <form name="frmPhoto" id="frmPhoto" method="post" action="<?php echo url_for('pim/viewPhotograph'); ?>" enctype="multipart/form-data">
                
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['emp_number']->render();?>
                
                <fieldset>
                    
                    <ol>
                        <li>
                            <?php echo $form['photofile']->renderLabel(__('Select a Photograph')); ?>
                            <?php echo $form['photofile']->render(); ?> 
                            <label class="fieldHelpBottom"><?php echo __(CommonMessages::FILE_LABEL_IMAGE); ?>.
                            <?php echo __('Recommended dimensions: 200px X 200px'); ?>
                            </label>
                        </li>
                    </ol>
                    <p>
                        <?php if ($photographPermissions->canUpdate()) : ?>
                            <input type="button" id="btnSave" value="<?php echo __("Upload"); ?>" />
                        <?php endif; ?>    
                        <?php if ($photographPermissions->canDelete() && ($showDeleteButton == 1)) : ?>
                            <input type="button" class="delete" id="btnDelete" value="<?php echo __("Delete"); ?>" 
                            data-toggle="modal" data-target="#deleteConfModal" />
                        <?php endif; ?>
                    </p>
                
                </fieldset>
            </form>
            <?php endif; ?>
            
        </div> <!-- inner -->
    
</div> <!-- box pimPane -->

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __("Delete photograph?");?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="btnYes" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" id="btnNo" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
    //<![CDATA[

    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_photoRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var deleteUrl = "<?php echo url_for('pim/viewPhotograph?option=delete&empNumber=' . $empNumber); ?>";
    var showDeteleButton = "<?php echo $showDeleteButton; ?>";

    //]]>
</script>

<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/viewPhotographSuccess')); ?>