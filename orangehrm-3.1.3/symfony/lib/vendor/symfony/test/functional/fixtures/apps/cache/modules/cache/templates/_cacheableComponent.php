<?php use_stylesheet('component_css') ?>
<?php use_javascript('component_js') ?>

<div class="cacheableComponent_<?php echo isset($varParam) ? $varParam : '' ?>_<?php echo isset($componentParam) ? $componentParam : '' ?>_<?php echo isset($requestParam) ? $requestParam : '' ?>">OK</div>

<?php slot('component') ?>Component<?php end_slot() ?>
