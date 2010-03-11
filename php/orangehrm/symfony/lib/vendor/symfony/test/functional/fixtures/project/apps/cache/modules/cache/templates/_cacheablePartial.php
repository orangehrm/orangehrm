<?php use_stylesheet('partial_css') ?>
<?php use_javascript('partial_js') ?>

<div class="cacheablePartial_<?php echo isset($varParam) ? $varParam : '' ?>_<?php echo $sf_params->get('param') ?>">OK</div>

<div id="anotherCacheablePartial"><?php include_partial('cache/anotherCacheablePartial') ?></div>

<?php slot('partial') ?>Partial<?php end_slot() ?>
