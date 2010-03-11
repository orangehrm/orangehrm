<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(9, new lime_output_color());

// setup
$temp1 = tempnam('/tmp/i18ndir', 'tmp');
unlink($temp1);
mkdir($temp1);

$temp2 = tempnam('/tmp/i18ndir', 'tmp');
unlink($temp2);
mkdir($temp2);

// copy fixtures to tmp directory
copy(dirname(__FILE__).'/fixtures/messages.fr.xml', $temp1.'/messages.fr.xml');
copy(dirname(__FILE__).'/fixtures/messages_bis.fr.xml', $temp2.'/messages.fr.xml');

$source = get_source($temp1, $temp2);
$source->setCulture('fr_FR');

// ->save()
$t->diag('->save()');
$t->is($source->save(), false, '->save() returns false if no message is saved');
$source->append('New message');
$t->is($source->save(), true, '->save() returns true if some messages are saved');
$source = get_source($temp1, $temp2);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'New message', '->save() saves new messages');

// test new culture
$source->setCulture('it');
$source->append('New message (it)');
$source->save();

$source = get_source($temp1, $temp2);
$source->setCulture('it');
$format = new sfMessageFormat($source);
$t->is($format->format('New message (it)'), 'New message (it)', '->save() saves new messages');

$source->setCulture('fr_FR');

// ->update()
$t->diag('->update()');
$t->is($source->update('New message', 'Nouveau message', 'Comments'), true, '->update() returns true if the message has been updated');
$source = get_source($temp1, $temp2);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'Nouveau message', '->update() updates a message translation');

// ->delete()
$t->diag('->delete()');
$t->is($source->delete('Non existant message'), false, '->delete() returns false if the message has not been deleted');
$t->is($source->delete('New message'), true, '->delete() returns true if the message has been deleted');
$source = get_source($temp1, $temp2);
$source->setCulture('fr_FR');
$format = new sfMessageFormat($source);
$t->is($format->format('New message'), 'New message', '->delete() deletes a message');

// teardown
sfToolkit::clearDirectory($temp1);
sfToolkit::clearDirectory($temp2);
rmdir($temp1);
rmdir($temp2);

function get_source($temp1, $temp2)
{
  $source1 = sfMessageSource::factory('XLIFF', $temp1);
  $source2 = sfMessageSource::factory('XLIFF', $temp2);

  return sfMessageSource::factory('Aggregate', array($source1, $source2));
}
