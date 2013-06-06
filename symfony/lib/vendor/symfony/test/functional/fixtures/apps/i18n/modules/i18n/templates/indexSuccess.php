<?php use_helper('I18N') ?>

<div id="action"><?php echo $test ?></div>
<div id="template"><?php echo __('an english sentence') ?></div>

<div id="action_local"><?php echo $localTest ?></div>
<div id="template_local"><?php echo __('a local english sentence') ?></div>

<div id="action_other"><?php echo $otherTest ?></div>
<div id="template_other"><?php echo __('an english sentence', array(), 'other') ?></div>

<div id="action_other_local"><?php echo $otherLocalTest ?></div>
<div id="template_other_local"><?php echo __('a local english sentence', array(), 'other') ?></div>
