<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTMessage"> 
  <?php echo image_tag('/sf/sf_default/images/icons/ok48.png', array('alt' => 'module created', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Module "<?php echo $sf_params->get('module') ?>" created</h1>
    <h5>Congratulations! You have successfully created a symfony module.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>This is a temporary page</dt>
  <dd>This page is part of the symfony <code>default</code> module. It will disappear as soon as you override the <code>index</code> action in the new module.</dd>

  <dt>What's next</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTDirectoryMessage">Browse to the <code>apps/<?php echo sfContext::getInstance()->getConfiguration()->getApplication() ?>/modules/<?php echo $sf_params->get('module') ?>/</code> directory</li>
      <li class="sfTEditMessage">In <code>actions/actions.class.php</code>, edit the <code>executeIndex()</code> method and remove the final <code>forward</code></li>
      <li class="sfTColorMessage">Customize the <code>templates/indexSuccess.php</code> template</li>
      <li class="sfTLinkMessage"><?php echo link_to('Learn more from the online documentation', 'http://www.symfony-project.org/doc') ?></li>
    </ul>
  </dd>
</dl>
