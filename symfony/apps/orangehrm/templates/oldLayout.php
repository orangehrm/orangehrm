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
    
  </head>
  <body>
      

                  <?php echo $sf_content ?>

   
      
    <?php $skipRoundBorder = $sf_request->getAttribute('skipRoundBorder');
    if (!isset($skipRoundBorder)) { ?>
	<script type="text/javascript">
//<![CDATA[	    

    	if (document.getElementById && document.createElement) {
	 			roundBorder('outerbox');
		}
            
        
            
            
//]]>	
	</script>    
    <?php } ?>
  </body>
</html>
