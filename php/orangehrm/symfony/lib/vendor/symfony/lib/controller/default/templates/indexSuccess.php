<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTMessage"> 
  <?php echo image_tag('/sf/sf_default/images/icons/ok48.png', array('alt' => 'ok', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Symfony Project Created</h1>
    <h5>Congratulations! You have successfully created your symfony project.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>Project setup successful</dt>
  <dd>This project uses the symfony libraries. If you see no image in this page, you may need to configure your web server so that it gains access to the <code>symfony_data/web/sf/</code> directory.</dd>

  <dt>This is a temporary page</dt>
  <dd>This page is part of the symfony <code>default</code> module. It will disappear as soon as you define a <code>homepage</code> route in your <code>routing.yml</code>.</dd>

  <dt>What's next</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTDatabaseMessage">Create your data model</li>
      <li class="sfTColorMessage">Customize the layout of the generated templates</li>
      <li class="sfTLinkMessage"><?php echo link_to('Learn more from the online documentation', 'http://www.symfony-project.org/doc') ?></li>
    </ul>
  </dd>
</dl>
