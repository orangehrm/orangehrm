<?php use_stylesheet(plugin_web_path('orangehrmBeaconPlugin', 'css/_beaconAbout.css'));
    
    ?>

    <a href="#" data-dismiss="modal" id="aboutDisplayLink"><?php echo __('About'); ?></a>

    <div class="modal hide" id="displayAbout">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3><?php  echo __('About'); ?></h3>
        </div>
        <form name="frmSelectEmployees" id="frmSelectEmployees" method="post" action="<?php echo url_for('communication/beaconAboutAjax'); ?>" enctype="multipart/form-data">
            <div class="modal-body">
                <div id="companyInfo">
                    <ul>
                        <li>
                            <p><?php echo __('Company Name') .": ". $companyName; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Version') .": ". $version; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Active Employees') .": ". $activeEmployeeCount; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Employees Terminated') .": ". $terminatedEmployeeCount; ?></p>
                        </li>
                    </ul>
                </div>
                
                <?php if ($beaconAcceptance != 'on' && $isAdmin) { ?>
                
                    <br>
                    <div id = "registration-section">
                        <ol style="margin-top: 10px;"></ol>
                        <div id="heartbeatDescription">
                            <p  ><span style="font-family: arial,helvetica,sans-seif;"><span>By enabling the <span>heartbeat</span> you allow</span>&nbsp; OrangeHRM to </span><span style="font-family: arial,helvetica,sans-serif;"><span style="font-family: arial,helvetica,sans-serif;">collect statistics about </span>usage in order to improve the user experience and performance. This function runs in the background and periodically sends data to the OrangeHRM Portal. THE DATA ARE JUST NUMBER TOTALS AND THEY DO NOT INCLUDE ANY PERSONAL INFORMATION.  Heartbeat tracks the number of users around the world and logs the time it takes to run database queries.</span><br /><br /></p>
                            <div>Also by enabling Heartbeat you will receive the following:<br /></div>
                            <ul>
                                <li>Bug fixes and other patches</li>
                                <li>Upgrades</li>
                                <li>Security updates</li>
                                <li>Other useful information about OrangeHRM</li>
                            </ul>

                            <p><br />Please contact us with any questions or comments at <a href="mailto:legal@orangehrm.com" target="_blank">legal@orangehrm.com</a>.</p>

                        </div>
                        <ol>
                            <li>
                                <?php echo $form->render(); ?>
                            </li>
                        </ol>


                        <div class="modal-footer">
                            <br class="clear"/>
                            <span id="messageToDisplayAbout" style="padding-left: 2px; display: none" class=""></span>
                            <input type="button" class="btn"  id="heartbeatSubmitBtn" data-dismiss="modal" value="<?php echo __('Ok'); ?>" />
                            <input type="button" id="heartbeatCancelBtn" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
                        </div>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>


<script type="text/javascript">
    jQuery(document).ready(function() {
        
        <?php if($beaconRequired){ ?>
               $.ajax({
            url: '<?php echo url_for('communication/sendBeaconMessageAjax'); ?>',
            type: "GET",
            success: function(data) {
                //alert(data);
            }
        });
       <?php } ?>
       
        jQuery('#aboutDisplayLink').click(function(event) {
            event.stopImmediatePropagation();
            jQuery('#messageToDisplayAbout').css(
                    'display', 'none');
            jQuery('#displayAbout').modal();
            jQuery('#help-menu.panelContainer').attr('style', 'display:block');
            
            var test = jQuery('.panelContainer');
            jQuery('#help-menu.panelContainer').css(
                    'display', 'block');
             
        });

        jQuery('#heartbeatSubmitBtn').click(function(event) {
            event.stopImmediatePropagation();
            jQuery(this).closest('form').ajaxSubmit(function() {
                jQuery('#messageToDisplayAbout').html('Saved');

                if (jQuery('#register_registration').is(':checked')) {
                    jQuery('#registration-section').css(
                            'display', 'none');
                }
                jQuery('#displayAbout').modal('hide');
                jQuery('#messageToDisplayAbout').css(
                        'display', 'block');
                jQuery('#welcome-menu').css('display','none');
            });
        });

        jQuery('#displayAbout').click(function(event) {
            event.stopImmediatePropagation();
        });
        
        jQuery('#heartbeatCancelBtn').click(function(event) {
            event.stopImmediatePropagation();
             jQuery('#welcome-menu').css('display','none');
                 jQuery('#displayAbout').modal('hide');
        });

    })
</script>

