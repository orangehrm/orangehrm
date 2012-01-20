<?php use_helper('I18N') ?>

<div id="action"><?php echo $test ?></div>
<div id="template"><?php echo __('an english sentence from plugin') ?></div>

<div id="action_local"><?php echo $localTest ?></div>
<div id="template_local"><?php echo __('a local english sentence from plugin') ?></div>

<div id="action_other"><?php echo $otherTest ?></div>
<div id="template_other"><?php echo __('another english sentence from plugin') ?></div>

<div id="action_yetAnother"><?php echo $yetAnotherTest ?></div>
<div id="template_yetAnother"><?php echo __('yet another english sentence from plugin') ?></div>

<div id="action_testForPluginI18N"><?php echo $testForPluginI18N ?></div>
<div id="template_testForPluginI18N"><?php echo __('an english sentence from plugin - global') ?></div>
