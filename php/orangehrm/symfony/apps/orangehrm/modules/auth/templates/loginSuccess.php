<?php
$styleSheet = 'orange';
$imagePath = public_path("../../themes/{$styleSheet}/images/login");
?>
<style type="text/css">
    <!--
    body {
        background-color: #FFFFFF;
        height: 700px;
    }

    img {
        border: none;
    }

    input:not([type="image"]) {
        background-color: transparent;
        border: none;
    }

    input:focus, select:focus, textarea:focus {
        background-color: transparent;
        border: none;
    }

    .textInputContainer {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #666666;
    }

    #divLogin {
        background: transparent url(<?php echo "{$imagePath}/login.png"; ?>) no-repeat center top;
        height: 520px;
        width: 100%;
        border-style: hidden;
        margin: auto;
        padding-left: 10px;
    }

    #divUsername {
        padding-top: 153px;
        padding-left: 50%;
    }

    #divPassword {
        padding-top: 35px;
        padding-left: 50%;
    }

    #txtUsername {
        width: 240px;
        border: 0px;
        background-color: transparent;
    }

    #txtPassword {
        width: 240px;
        border: 0px;
        background-color: transparent;
    }

    #txtUsername, #txtPassword {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #666666;
        height: 16px;
        vertical-align: middle;
        padding-top:0;
    }
    
    #divLoginHelpLink {
        width: 270px;
        background-color: transparent;
        height: 20px;
        margin-top: 12px;
        margin-right: 0px;
        margin-bottom: 0px;
        margin-left: 50%;
    }

    #divLoginButton {
        padding-top: 2px;
        padding-left: 50%;
        float: left;
        width: 280px;
    }

    #btnLogin {
        background: url(<?php echo "{$imagePath}/Login_button.png"; ?>) no-repeat;
        cursor:pointer;
        width: 94px;
        height: 23px;
        border: none;
    }

    #divLink {
        padding-left: 230px;
        padding-top: 105px;
        float: left;
    }

    #divLogo {
        padding-left: 30%;
        padding-top: 70px;
    }

    #spanMessage {
        background: transparent url(<?php echo "{$imagePath}/mark.png"; ?>) no-repeat;
        padding-left: 18px; 
        padding-top: 0px;
        color: #DD7700;
        font-weight: bold;
    }

</style>

<div id="divLogin">
    <div id="divLogo">
        <img src="<?php echo "{$imagePath}/logo.png"; ?>" />
    </div>

    <form id="frmLogin" method="post" action="<?php echo url_for('auth/validateCredentials'); ?>">
        <input type="hidden" name="actionID"/>
        <input type="hidden" name="hdnUserTimeZoneOffset" id="hdnUserTimeZoneOffset" value="0" />

        <div id="divUsername" class="textInputContainer">
            <?php echo $form['Username']->render(); ?>
        </div>
        <div id="divPassword" class="textInputContainer">
            <?php echo $form['Password']->render(); ?>
        </div>
        <div id="divLoginHelpLink"><?php
            include_component('core', 'ohrmPluginPannel', array(
                'location' => 'login-page-help-link',
            ));
            ?></div>
        <div id="divLoginButton">
            <input type="submit" name="Submit" class="button" id="btnLogin" value=" " />
            <?php if (!empty($message)) : ?>
            <span id="spanMessage"><?php echo __($message); ?></span>
            <?php endif; ?>
        </div>
    </form>

</div>

<?php include_partial('core/footer'); ?>

<script type="text/javascript">
    
    function calculateUserTimeZoneOffset() {
        var myDate = new Date();
        var offset = (-1) * myDate.getTimezoneOffset() / 60;
        return offset;
    }
            
    function addHint(inputObject, hintImageURL) {
        if (inputObject.val() == '') {
            inputObject.css('background', "url('" + hintImageURL + "') no-repeat 10px 3px");
        }
    }
            
    function removeHint(inputObject) {
        inputObject.css('background', '');
    }
    
    function showMessage(message) {
        if ($('#spanMessage').size() == 0) {
            $('<span id="spanMessage"></span>').insertAfter('#btnLogin');
        }

        $('#spanMessage').html(message);
    }
    
    function validateLogin() {
        var isEmptyPasswordAllowed = false;
        
        if ($('#txtUsername').val() == '') {
            showMessage('<?php echo __('Username cannot be empty'); ?>');
            return false;
        }
        
        if (!isEmptyPasswordAllowed) {
            if ($('#txtPassword').val() == '') {
                showMessage('<?php echo __('Password cannot be empty'); ?>');
                return false;
            }
        }
        
        return true;
    }
    
    $(document).ready(function() {
        
        addHint($('#txtUsername'), '<?php echo "{$imagePath}/username-hint.png"; ?>');
        addHint($('#txtPassword'), '<?php echo "{$imagePath}/password-hint.png"; ?>');
        
        $('#txtUsername').focus(function() {
            removeHint($(this));
            removeHint($("#txtPassword"));
        });
        
        $('#txtPassword').focus(function() {
            removeHint($(this));
            removeHint($("#txtUsername"));
        });
        
        $('#hdnUserTimeZoneOffset').val(calculateUserTimeZoneOffset().toString());
        
        $('#frmLogin').submit(validateLogin);
    });
</script>
