<?php use_javascript('../orangehrmPerformanceTrackerPlugin/js/addPerformanceTrackerLogSuccess'); ?>

<div id="performanceTrackerLog" class="box">

    <div class="head">
        <h1 id="formHeading">
            <?php echo __('manage Performance Tracker Log'); ?>
        </h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>

        <form action="#" id="frmAddperformanceTrackerLog" name="frmAddperformanceTrackerLog" class="content_inner" method="post">
            
            <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="saveBtn" value="<?php echo __('Save') ?>" tabindex="6" />
                    <input type="button" class="reset" id="resetBtn" 
                        value="<?php if (isset($reviewId)) echo __('Reset'); else echo __('Clear'); ?>" tabindex="7" />
                </p>
            </fieldset>
        </form>
    </div> <!-- inner -->

</div> <!-- Box -->

<!--this is ajax message place -->
<div id="ajaxCommentSaveMsg"></div>
<!-- end of ajax message place -->


<?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>

<!-- comment dialog -->
<div class="modal hide" id="commentDialog">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('Performance Tracker Log Comment'); ?></h3>
  </div>
  <div class="modal-body">
    <p>
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="trackLogId" />
        <input type="hidden" id="leaveOrRequest" />
        <textarea name="trackLogComment" id="trackLogComment" cols="40" rows="10" class="commentTextArea"></textarea>
        <br class="clear" />
        <div id="commentError"></div>

    </form>        
    </p>
  </div>
  <div class="modal-footer">

  </div>
</div>
<!-- end of comment dialog-->

<script type="text/javascript">
//<![CDATA[
    var trackId = '<?php echo $trackId;?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/performanceTracker/updateComment'); ?>';
    var lang_edit = '<?php echo __('Edit'); ?>';
    var lang_save = '<?php echo __('Save'); ?>';
    var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';    
    var lang_exceed150Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 150)); ?>';  
    var lang_exceed3000Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 3000)); ?>';  
    var lang_comment_successfully_saved = '<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_Close = '<?php echo __('Close');?>';
    var currentUser = <?php echo 'userId';?>;
    //]]>
</script>