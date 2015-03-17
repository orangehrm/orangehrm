# LightOpenID Quick Start


## Sign-on with OpenID is a two step process:
  
  1. Step one is authentication with the provider:

     ```php
     $openid = new LightOpenID('my-host.example.org');
     
     $openid->identity = 'ID supplied by the user';
     
     header('Location: ' . $openid->authUrl());
     ```

     The provider then sends various parameters via GET, one of which is `openid_mode`.
 
  2. Step two is verification:

     ```php
     $openid = new LightOpenID('my-host.example.org');
     
     if ($openid->mode) {
       echo $openid->validate() ? 'Logged in.' : 'Failed!';
     }
     ```


### Notes:

  Change 'my-host.example.org' to your domain name. Do NOT use `$_SERVER['HTTP_HOST']`
  for that, unless you know what you're doing.
     
  Optionally, you can set `$returnUrl` and `$realm` (or `$trustRoot`, which is an alias).
  The default values for those are:

  ```php
  $openid->realm = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  $openid->returnUrl = $openid->realm . $_SERVER['REQUEST_URI'];
  ```

  If you don't know their meaning, refer to any OpenID tutorial, or specification.


## Basic configuration options:

<table>
  <tr>
    <th>name</th>
    <th>description</th>
  </tr>
  <tr>
    <td>identity</td>
    <td>
      Sets (or gets) the identity supplied by an user. Set it
      before calling authUrl(), and get after validate().
    </td>
  </tr>
  <tr>
    <td>returnUrl</td>
    <td>
      Users will be redirected to this url after they complete 
      authentication with their provider. Default: current url.
    </td>
  </tr>
  <tr>
    <td>realm</td>
    <td>
      The realm user is signing into. Providers usually say 
      "You are sgning into $realm". Must be in the same domain
      as returnUrl. Usually, this should be the host part of
      your site's url. And that's the default.
    </td>
  </tr>
  <tr>
    <td>required and optional</td>
    <td>
      Attempts to fetch more information about an user.
      See <a href="#common-ax-attributes">Common AX attributes</a>.
    </td>
  </tr>
  <tr>
    <td>verify_peer</td>
    <td>
      When using https, attempts to verify peer's certificate.
      See <a href="http://php.net/manual/en/function.curl-setopt.php">CURLOPT_SSL_VERIFYPEER</a>.
    </td>
  </tr>
  <tr>
    <td>cainfo and capath</td>
    <td>
      When verify_peer is true, sets the CA info file and directory.
      See <a href="http://php.net/manual/en/function.curl-setopt.php">CURLOPT_SSL_CAINFO</a>
      and <a href="http://php.net/manual/en/function.curl-setopt.php">CURLOPT_SSL_CAPATH</a>.
    </td>
  </tr>
</table>


## AX and SREG extensions are supported:

  To use them, specify `$openid->required` and/or `$openid->optional` before calling
  `$openid->authUrl()`. These are arrays, with values being AX schema paths (the 'path'
  part of the URL). For example:

  ```php
  $openid->required = array('namePerson/friendly', 'contact/email');
  $openid->optional = array('namePerson/first');
  ```

  Note that if the server supports only SREG or OpenID 1.1, these are automaticaly mapped 
  to SREG names, so that user doesn't have to know anything about the server.
  To get the values, use `$openid->getAttributes()`.


### Common AX attributes

  Here is a list of the more common AX attributes (from [axschema.org](http://www.axschema.org/types/)):

  Name                    | Meaning
  ------------------------|---------------
  namePerson/friendly     | Alias/Username
  contact/email           | Email
  namePerson              | Full name
  birthDate               | Birth date
  person/gender           | Gender
  contact/postalCode/home | Postal code
  contact/country/home    | Country
  pref/language           | Language
  pref/timezone           | Time zone

  Note that even if you mark some field as required, there is no guarantee that you'll get any
  information from a provider. Not all providers support all of these attributes, and some don't
  support these extensions at all.

  Google, for example, completely ignores optional parameters, and for the required ones, it supports,
  according to [it's website](http://code.google.com/apis/accounts/docs/OpenID.html):

  * namePerson/first (first name)
  * namePerson/last (last name)
  * contact/country/home
  * contact/email
  * pref/language

