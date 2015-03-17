<?php
# Logging in with Google Apps accounts requires setting special identity
# and XRDS override, so this example shows how to do it.
require 'openid.php';

try {
    # Change 'example.org' to your domain name.
    $domain = 'example.org';
    $openid = new LightOpenID($domain);
    $openid->xrdsOverride = array(
        '#^http://' . $domain . '/openid\?id=\d+$#',
        'https://www.google.com/accounts/o8/site-xrds?hd=' . $domain
    );
    
    if (!$openid->mode) {
        if (isset($_GET['login'])) {
            $openid->identity = 'https://www.google.com/accounts/o8/site-xrds?hd=' . $domain;
            header('Location: ' . $openid->authUrl());
        }
?>
<form action="?login" method="post">
    <button>Login with Google</button>
</form>
<?php
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
