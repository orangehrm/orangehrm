<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

$mail = new sfMail();
$mail->initialize();

$mail->setBody('foo');
$mail->prepare();
$t->like($mail->getRawHeader(), '/^X\-Mailer\: PHPMailer/m');
$t->like($mail->getRawBody(), '/^\s*foo\s*$/');
