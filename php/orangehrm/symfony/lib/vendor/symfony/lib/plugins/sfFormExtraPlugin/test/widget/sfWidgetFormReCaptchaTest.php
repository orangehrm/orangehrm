<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../bootstrap.php';
require_once dirname(__FILE__).'/../../lib/widget/sfWidgetFormReCaptcha.class.php';
require_once $_SERVER['SYMFONY'].'/util/sfDomCssSelector.class.php';

$t = new lime_test(7, new lime_output_color());

$REMOTE_ADDR = 'symfony.example.com';
$PUBLIC_KEY  = '6Lf2DQEAAAAAALB9pGaVdcjMiv4CAuOVkfCSVvGh';
$PRIVATE_KEY = '6Lf2DQEAAAAAALnEL0iEogIxZNYMlG7pmNhwEXjk';

// __construct()
$t->diag('__construct()');
try
{
  new sfWidgetFormReCaptcha();
}
catch (RuntimeException $e)
{
  $t->pass('__construct() expects a "public_key" option');
}

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormReCaptcha(array('public_key' => $PUBLIC_KEY));
$dom = new DomDocument('1.0', 'utf-8');
$dom->loadHTML($w->render('captcha'));
$c = new sfDomCssSelector($dom);
$t->is(count($c->matchSingle('script[src^="http://"]')->getNodes()), 1, '->render() uses the HTTP ReCaptcha URL by default');
$t->is(count($c->matchSingle(sprintf('script[src$="%s"]', $PUBLIC_KEY))->getNodes()), 1, '->render() embeds the ReCatpcha public key in the URL');
$t->is(count($c->matchSingle('iframe[src^="http://"]')->getNodes()), 1, '->render() uses the HTTP ReCaptcha URL by default');
$t->is(count($c->matchSingle(sprintf('iframe[src$="%s"]', $PUBLIC_KEY))->getNodes()), 1, '->render() embeds the ReCatpcha public key in the URL');

$w = new sfWidgetFormReCaptcha(array('public_key' => $PUBLIC_KEY, 'use_ssl' => true));
$dom = new DomDocument('1.0', 'utf-8');
$dom->loadHTML($w->render('captcha'));
$c = new sfDomCssSelector($dom);
$t->is(count($c->matchSingle('script[src^="https://"]')->getNodes()), 1, '->render() uses the HTTPS ReCaptcha URL is use_ssl is true');
$t->is(count($c->matchSingle('iframe[src^="https://"]')->getNodes()), 1, '->render() uses the HTTPS ReCaptcha URL is use_ssl is true');
