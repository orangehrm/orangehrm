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
<?php use_javascript('../orangehrmPerformanceTrackerPlugin/js/viewPerformanceTrackerLogListSuccess'); ?>

<div id="mainDiv"> 
    <?php include_component('core', 'ohrmList'); ?>	
</div> 

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
    var defineLeaveTypeUrl = '<?php echo url_for('performanceTracker/addPerformanceTracker'); ?>';    
    var lang_SelectLeaveTypeToDelete = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';  
    //]]>
</script>
