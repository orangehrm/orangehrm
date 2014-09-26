<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(11);

// setup
$temp = tempnam('/tmp/i18ndir', 'tmp');
unlink($temp);
mkdir($temp);

// copy fixtures to tmp directory
copy(dirname(__FILE__).'/fixtures/messages.fr.xml', $temp.'/messages.fr.xml');

$source = sfMessageSource::factory('XLIFF', $temp);
$source->setCulture('fr_FR');

// ->loadData()
$t->diag('->loadData()');
$messages = $source->loadData($source->getSource('messages.fr.xml'));
$t->is($messages['an english sentence'][0], 'une phrase en français', '->loadData() loads messages from a XLIFF file');

$t->is($source->loadData($source->getSource('invalid.xml')), false, '->loadData() returns false if it cannot load the messages from the file');

// ->save()
$t->diag('->save()');
$t->is($source->save(), false, '->save() returns false if no message is saved');
$source->append('New message');
$t->is($source->save(), true, '->save() returns true if some messages are saved');
$source = sfMessageSource::factory('XLIFF', $temp);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'New message', '->save() saves new messages');

// test new culture
$source->setCulture('it');
$source->append('New message & <more> (it)');
$source->save();

$source = sfMessageSource::factory('XLIFF', $temp);
$source->setCulture('it');
$format = new sfMessageFormat($source);
$t->is($format->format('New message & <more> (it)'), 'New message & <more> (it)', '->save() saves new messages');

$source->setCulture('fr_FR');

// ->update()
$t->diag('->update()');
$t->is($source->update('New message', 'Nouveau message', ''), true, '->update() returns true if the message has been updated');
$source = sfMessageSource::factory('XLIFF', $temp);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'Nouveau message', '->update() updates a message translation');

// ->delete()
$t->diag('->delete()');
$t->is($source->delete('Non existant message'), false, '->delete() returns false if the message has not been deleted');
$t->is($source->delete('New message'), true, '->delete() returns true if the message has been deleted');
$source = sfMessageSource::factory('XLIFF', $temp);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'New message', '->delete() deletes a message');

// teardown
sfToolkit::clearDirectory($temp);
rmdir($temp);
