<div id="partial"><?php include_partial('cache/partial') ?></div>

<div id="cacheablePartial"><?php include_partial('cache/cacheablePartial') ?></div>

<div id="cacheablePartialVarParam"><?php include_partial('cache/cacheablePartial', array('varParam' => 'varParam')) ?></div>


<div id="contextualPartial"><?php include_partial('cache/contextualPartial') ?></div>

<div id="contextualCacheablePartial"><?php include_partial('cache/contextualCacheablePartial') ?></div>

<div id="contextualCacheablePartialVarParam"><?php include_partial('cache/contextualCacheablePartial', array('varParam' => 'varParam')) ?></div>


<div id="component"><?php include_component('cache', 'component') ?></div>

<div id="componentVarParam"><?php include_component('cache', 'component', array('varParam' => 'varParam')) ?></div>

<div id="cacheableComponent"><?php include_component('cache', 'cacheableComponent') ?></div>

<div id="cacheableComponentVarParam"><?php include_component('cache', 'cacheableComponent', array('varParam' => 'varParam')) ?></div>


<div id="contextualComponent"><?php include_component('cache', 'contextualComponent') ?></div>

<div id="contextualComponentVarParam"><?php include_component('cache', 'contextualComponent', array('varParam' => 'varParam')) ?></div>

<div id="contextualCacheableComponent"><?php include_component('cache', 'contextualCacheableComponent') ?></div>

<div id="contextualCacheableComponentVarParam"><?php include_component('cache', 'contextualCacheableComponent', array('varParam' => 'varParam')) ?></div>
