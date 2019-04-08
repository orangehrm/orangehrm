<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin','css/securityAuthenticationCommon.css')); ?>
<div class="box">
    <?php include_partial('securityAuthenticationHeader');?>
    <div class="head">
        <h1><?php echo __('Password reset was successful. Go to login page and try to login'); ?></h1>
    </div>    
    <div id="divContent" class="inner">
        <input type="button" id="btnGoToLogin" class="btn" value="<?php echo __('Go to Login Page'); ?>" />
    </div>
</div>    
<script type="text/javascript">
$(document).ready(function() {
    $('#btnGoToLogin').click(function() {
        location.href = '<?php echo public_path('index.php/auth/login', true); ?>';
    });
});
</script>
<?php include_partial('global/footer_copyright_social_links'); ?>