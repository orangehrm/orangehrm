<?php use_stylesheet('main_css') ?>
<?php use_javascript('main_js') ?>

<div id="cacheablePartial"><?php include_partial('cache/cacheablePartial') ?></div>

<div id="contextualCacheablePartial"><?php include_partial('cache/contextualCacheablePartial') ?></div>

<div id="cacheableComponent"><?php include_component('cache', 'cacheableComponent') ?></div>

<div id="contextualCacheableComponent"><?php include_component('cache', 'contextualCacheableComponent') ?></div>
