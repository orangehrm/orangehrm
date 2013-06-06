<?php decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<div class="sfTMessageContainer sfTLock"> 
  <?php echo image_tag('/sf/sf_default/images/icons/lock48.png', array('alt' => 'login required', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Login Required</h1>
    <h5>This page is not public.</h5>
  </div>
</div>
<dl class="sfTMessageInfo">
  <dt>How to access this page</dt>
  <dd>You must proceed to the login page and enter your id and password.</dd>

  <dt>What's Next</dt>
  <dd>
    <ul class="sfTIconList">
      <li class="sfTLinkMessage"><?php echo link_to('Proceed to login', sfConfig::get('sf_login_module').'/'.sfConfig::get('sf_login_action')) ?></li>
      <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Back to previous page</a></li>
    </ul>
  </dd>
</dl>
