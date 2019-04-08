<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin','css/securityAuthenticationCommon.css')); ?>
<div class="box">
    <?php include_partial('securityAuthenticationHeader');?>
    <div class="head">
        <h1><?php echo __('Instruction Sent !'); ?></h1>
    </div>    
    <div id="divContent" class="inner">
        <p>
            <?php echo __('Instructions for resetting your password have been sent to Email'); ?>
        </p>
        </br>
        <p>
            <?php echo __('You will receive this email within 5 minutes. Be sure to check check your spam folder too.'); ?>
        </p>
    </div>
</div>
<?php include_partial('global/footer_copyright_social_links'); ?>