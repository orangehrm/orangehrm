<?php
use_stylesheet(plugin_web_path('orangehrmBeaconPlugin', 'css/_beaconAbout.css'));
if ($aboutEnabled) {
    ?>

    <a href="#" data-dismiss="modal" id="aboutDisplayLink"><?php echo __('About'); ?></a>

    <div class="modal hide" id="displayAbout">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3><?php echo __('About'); ?></h3>
        </div>
        <form name="frmSelectEmployees" id="frmSelectEmployees" method="post" action="<?php echo url_for('admin/beaconAboutAjax'); ?>" enctype="multipart/form-data">
            <div class="modal-body">
                <div id="companyInfo">
                    <ul>
                        <li>
                            <p><?php echo __('Company Name: ') . $companyName; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Version: ') . $version; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Active Employees: ') . $activeEmployeeCount; ?></p>
                        </li>
                        <li>
                            <p><?php echo __('Terminated Employees: ') . $terminatedEmployeeCount; ?></p>
                        </li>
                    </ul>
                </div>
                <ol></ol>
                <?php if ($beaconAcceptance != 'on') { ?>
                    <br>
                    <div id = "registration-section">
                        <div id="heartbeatDescription">
                            <p><span style="font-family: arial,helvetica,sans-serif;"><span>By enabling the <span>heartbeat</span> you allow</span>&nbsp; OrangeHRM to </span><span style="font-family: arial,helvetica,sans-serif;"><span style="font-family: arial,helvetica,sans-serif;">collect statistics about </span>usage in order to improve the software. It runs in the background and periodically sends data to the OrangeHRM Portal. OrangeHRM would like to keep track of the number of users it has around the world, with demographic information.</span><br /><br /></p>
                            <div>By enabling the heartbeat you will also be entitled to receive the following:<br /><br /></div>
                            <ul style="list-style-type: circle; margin-left: 30px;">
                                <li>Bug fixes and other patches</li>
                                <li>Upgrades</li>
                                <li>Security updates</li>
                                <li>Other useful information about OrangeHRM</li>
                            </ul>
                            <div>
                                <p>Information we get is primarily non-personally-identifying information and we do not collect any information that we could use to identify an individual.</p>
                                We collect this non-personally-identifying information in order to improve user experience and performance. For instance we log the time it takes to run database queries so that we can improve performance.</div>
                            <p><br />We take the private nature of your personal information very seriously, and are committed to protecting it. To do that, we've set up procedures to ensure that your information is handled responsibly and in accordance with applicable data protection and privacy laws. We're grateful for your trust, and we'll act that way.<br /><br />Please contact us with any questions or comments about this on <a class = "aboutLink" href="mailto:legal@orangehrm.com" target="_blank">legal@orangehrm.com</a>.</p>

                        </div>
                        <ol>
                            <li>
                                <?php echo $form->render(); ?>
                            </li>
                        </ol>
                    </div>

                    <div class="modal-footer">
                        <br class="clear"/>
                        <span id="messageToDisplayAbout" style="padding-left: 2px; display: none" class=""></span>
                        <input type="button" class="btn"  id="heartbeatSubmitBtn" data-dismiss="modal" value="<?php echo __('Ok'); ?>" />
                        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
                    </div></div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function() {

        $('#aboutDisplayLink').click(function(event) {
            event.stopImmediatePropagation();
            $('#messageToDisplayAbout').css(
                    'display', 'none');
            $('#displayAbout').modal();
            $('#help-menu.panelContainer').attr('style', 'display:block');
            var test = $('.panelContainer');
            $('#help-menu.panelContainer').css(
                    'display', 'block');

        });

        $('#heartbeatSubmitBtn').click(function(event) {
            event.stopImmediatePropagation();
            $(this).closest('form').ajaxSubmit(function() {
                $('#messageToDisplayAbout').html('Saved');
                $('#registration-section').css(
                        'display','none');
                $('#displayAbout').modal('hide');
                $('#messageToDisplayAbout').css(
                        'display', 'block');
            });
        });

        $('#displayAbout').click(function(event) {
            event.stopImmediatePropagation();
        });

    })
</script>

