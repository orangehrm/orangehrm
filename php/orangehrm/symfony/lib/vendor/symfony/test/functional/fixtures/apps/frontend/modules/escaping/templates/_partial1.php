<h3><?php echo $var ?></h3>
<h4><?php echo $sf_data->getRaw('var') ?></h4>

<span class="<?php echo $sf_data->getRaw('arr') ? 'yes' : 'no' ?>"></span>

<?php include_partial('escaping/partial2', array('var' => $var, 'arr' => $arr)) ?>
