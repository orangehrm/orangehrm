<h1><?php echo $var ?></h1>
<h2><?php echo $sf_data->get('var') ?></h2>

<?php include_partial('escaping/partial1', array('var' => $var, 'arr' => array())) ?>
