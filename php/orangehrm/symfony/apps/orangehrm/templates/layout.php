<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>

	<link href="<?php echo public_path('../../themes/orange/css/style.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo public_path('../../themes/orange/css/layout.css')?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo public_path('../../themes/orange/css/message.css')?>" rel="stylesheet" type="text/css"/>
	<!--[if lte IE 6]>
	<link href="<?php echo public_path('../../themes/orange/css/IE6_style.css')?>" rel="stylesheet" type="text/css"/>
	<![endif]-->
	<!--[if IE]>
	<link href="<?php echo public_path('../../themes/orange/css/IE_style.css')?>" rel="stylesheet" type="text/css"/>
	<![endif]-->
	<script type="text/javascript" src="<?php echo public_path('../../themes/orange/scripts/style.js');?>"></script>
	
	<script type="text/javascript" src="<?php echo public_path('../../scripts/archive.js');?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.form.js')?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.tablesorter.js')?>"></script>
    <?php echo javascript_include_tag('orangehrm.validate.js'); ?>     
  </head>
  <body>
  
    <?php echo $sf_content ?>
    
	<script type="text/javascript">
//<![CDATA[	    

    	if (document.getElementById && document.createElement) {
	 			roundBorder('outerbox');
			}
//]]>	
	</script>    
  </body>
</html>
