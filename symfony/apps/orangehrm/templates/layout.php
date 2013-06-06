<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
$cultureElements = explode('_', $sf_user->getCulture()); 
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $cultureElements[0]; ?>" lang="<?php echo $cultureElements[0]; ?>">
  <head>
    <!-- Mimic Internet Explorer 8 -->  
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" >  
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
        <link href="<?php echo theme_path('images/favicon.ico')?>" rel="icon" type="image/gif"/>
        
	<link href="<?php echo public_path('../../themes/orange/css/style.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo public_path('../../themes/orange/css/layout.css')?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo public_path('../../themes/orange/css/message.css')?>" rel="stylesheet" type="text/css"/>
	<!--[if lte IE 6]>
	<link href="<?php echo public_path('../../themes/orange/css/IE6_style.css')?>" rel="stylesheet" type="text/css"/>
	<![endif]-->
	<!--[if IE]>
	<link href="<?php echo public_path('../../themes/orange/css/IE_style.css')?>" rel="stylesheet" type="text/css"/>
	<![endif]-->
    <!--[if IE 9]>
        <link href="<?php echo public_path('../../themes/orange/css/IE9_style.css')?>" rel="stylesheet" type="text/css"/>
    <![endif]-->
    <!--[if IE 8]>
        <link href="<?php echo public_path('../../themes/orange/css/IE8_style.css')?>" rel="stylesheet" type="text/css"/>
    <![endif]-->
	<script type="text/javascript" src="<?php echo public_path('../../themes/orange/scripts/style.js');?>"></script>
	
	<script type="text/javascript" src="<?php echo public_path('../../scripts/archive.js');?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.form.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.tablesorter.js')?>"></script>
    <?php echo javascript_include_tag('orangehrm.validate.js'); ?>

    <?php echo include_javascripts();?>
    <?php echo include_stylesheets();?>
    
<!-- Elements of new UI: Begin -->    
<link href="<?php echo public_path('../../symfony/web/themes/default/css/header.css')?>" rel="stylesheet" type="text/css"/>      
<!-- Elements of new UI: End -->    
    
  </head>
  <body>
      
        <div id="wrapper">

            <div id="branding">
                <img src="<?php echo public_path('../../symfony/web/themes/default/images/logo.png')?>" width="283" height="56" alt="OrangeHRM">
                <a href="#" class="subscribe">Join OrangeHRM Community</a>
                <a href="#" id="welcome"><?php echo __('Welcome') . ' ' . $sf_user->getAttribute('auth.firstName'); ?></a>
                <div id="welcome-menu">
                    <ul>
                        <li><a href="<?php echo url_for('admin/changeUserPassword'); ?>">Change Password</a></li>
                        <li><a href="<?php echo url_for('auth/logout'); ?>">Logout</a></li>
                    </ul>
                </div>
                <div style="color:#FF0000; font-size: 20px; display: inline; margin-left: 200px"><b>NOT CONVERTED</b></div>
                <a href="#" id="help">Help &amp; Training</a>
                <div id="help-menu">
                    <ul>
                        <li><a href="#">Link 1</a></li>
                        <li><a href="#">Link 2</a></li>
                        <li><a href="#">Link 3</a></li>
                        <li><a href="#">Link 4</a></li>
                    </ul>
                </div>
            </div> <!-- branding -->      
            
            <?php include_component('core', 'mainMenu'); ?>

            <div id="content-old-screens" style="padding-top:35px;min-height: 250px;">

                  <?php echo $sf_content ?>

            </div> <!-- content -->
          
        </div> <!-- wrapper -->      
     
    <script type="text/javascript">
    //<![CDATA[
    <?php $skipRoundBorder = $sf_request->getAttribute('skipRoundBorder');
    if (!isset($skipRoundBorder)) { ?>
	
    	if (document.getElementById && document.createElement) {
	 			roundBorder('outerbox');
		}
            
        $(document).ready(function() {        

            $("#welcome").click(function () {
                $("#welcome-menu").slideToggle("fast");
                $(this).toggleClass("activated");
                return false;
            });
            
            /* Highlighting firstLevelMenu: Begins */
            $(".firstLevelMenu").click(function () {

                $(".firstLevelMenu").each(function(){
                    $(this).parent('li').removeClass('current');
                });

                $(this).parent('li').addClass('current');

            });
            /* Highlighting firstLevelMenu: Ends */
            

        });            
	    
    <?php } ?>
    //]]>
    </script>
  </body>
</html>
