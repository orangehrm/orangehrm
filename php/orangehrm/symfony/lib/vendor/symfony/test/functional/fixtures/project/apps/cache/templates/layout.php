<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php echo include_http_metas() ?>
<?php echo include_metas() ?>

<?php echo include_title() ?>

<link rel="shortcut icon" href="/favicon.ico" />

</head>
<body>

<?php echo $sf_content ?>

<div id="component_slot_content"><?php echo get_slot('component') ?></div>
<div id="partial_slot_content"><?php echo get_slot('partial') ?></div>
<div id="another_partial_slot_content"><?php echo get_slot('another_partial') ?></div>

</body>
</html>
