Zxcvbn-PHP is a password strength estimator using pattern matching and minimum entropy calculation. Zxcvbn-PHP is based on the Javascript zxcvbn project from [Dropbox and @lowe](https://tech.dropbox.com/2012/04/zxcvbn-realistic-password-strength-estimation/). "zxcvbn" is bad password, just like "qwerty" and "123456". 

>zxcvbn attempts to give sound password advice through pattern matching and conservative entropy calculations. It finds 10k common passwords, common American names and surnames, common English words, and common patterns like dates, repeats (aaa), sequences (abcd), and QWERTY patterns.

[![Build Status](https://travis-ci.org/bjeavons/zxcvbn-php.png?branch=master)](https://travis-ci.org/bjeavons/zxcvbn-php)
[![Coverage Status](https://coveralls.io/repos/bjeavons/zxcvbn-php/badge.png?branch=master)](https://coveralls.io/r/bjeavons/zxcvbn-php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/bjeavons/zxcvbn-php/v/stable.png)](https://packagist.org/packages/bjeavons/zxcvbn-php)

## Installation

The library can be installed with [Composer](http://getcomposer.org) by adding it as a dependency to your composer.json file.

```json
{
    "require": {
        "bjeavons/zxcvbn-php": "^0.2"
    }
}
```

After running `php composer.phar update` on the command line, include the
autoloader in your PHP scripts so that the ZxcvbnPhp class is available.

```php
require_once 'vendor/autoload.php';
```

## Usage

```php
use ZxcvbnPhp\Zxcvbn;

$userData = array(
  'Marco',
  'marco@example.com'
);

$zxcvbn = new Zxcvbn();
$strength = $zxcvbn->passwordStrength('password', $userData);
echo $strength['score'];
// will print 0

$strength = $zxcvbn->passwordStrength('correct horse battery staple');
echo $strength['score'];
// will print 4
```

### Acknowledgements
Thanks to @lowe for the original [Javascript Zxcvbn](https://github.com/lowe/zxcvbn)
and [@Dreyer's port](https://github.com/Dreyer/php-zxcvbn) for reference.
