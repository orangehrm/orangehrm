<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php use_stylesheet('/sf/sf_default/css/screen.css', 'last') ?>
<?php include_stylesheets() ?>
<?php include_javascripts() ?>

<link rel="shortcut icon" href="/favicon.ico" />

<!--[if lt IE 7.]>
<?php echo stylesheet_tag('/sf/sf_default/css/ie.css') ?>
<![endif]-->

</head>
<body>
<div class="sfTContainer">
  <?php echo link_to(image_tag('/sf/sf_default/images/sfTLogo.png', array('alt' => 'symfony PHP Framework', 'class' => 'sfTLogo', 'size' => '186x39')), 'http://www.symfony-project.org/') ?>
  <?php echo $sf_content ?>
</div>
</body>
</html>
