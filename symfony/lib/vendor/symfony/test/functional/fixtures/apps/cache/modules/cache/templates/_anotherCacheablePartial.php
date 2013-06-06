<?php use_stylesheet('another_partial_css') ?>
<?php use_javascript('another_partial_js') ?>

<div class="cacheablePartial_<?php echo isset($varParam) ? $varParam : '' ?>_<?php echo $sf_params->get('param') ?>">OK</div>
<?php use_stylesheet('another_partial_css') ?>
<?php use_javascript('another_partial_js') ?>

<div class="cacheablePartial_<?php echo isset($varParam) ? $varParam : '' ?>_<?php echo $sf_params->get('param') ?>">OK</div>

<?php slot('another_partial') ?>Another Partial<?php end_slot() ?>
