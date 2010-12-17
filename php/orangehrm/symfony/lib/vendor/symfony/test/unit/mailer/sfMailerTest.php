<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/classes/Swift.php';
Swift::registerAutoload();
sfMailer::initialize();
require_once dirname(__FILE__).'/fixtures/TestMailerTransport.class.php';
require_once dirname(__FILE__).'/fixtures/TestSpool.class.php';
require_once dirname(__FILE__).'/fixtures/TestMailMessage.class.php';

$t = new lime_test(34);

$dispatcher = new sfEventDispatcher();

// __construct()
$t->diag('__construct()');
try
{
  new sfMailer($dispatcher, array('delivery_strategy' => 'foo'));

  $t->fail('__construct() throws an InvalidArgumentException exception if the strategy is not valid');
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException exception if the strategy is not valid');
}

// main transport
$mailer = new sfMailer($dispatcher, array(
  'logging'           => true,
  'delivery_strategy' => 'realtime',
  'transport'         => array('class' => 'TestMailerTransport', 'param' => array('foo' => 'bar', 'bar' => 'foo')),
));
$t->is($mailer->getTransport()->getFoo(), 'bar', '__construct() passes the parameters to the main transport');

// spool
$mailer = new sfMailer($dispatcher, array(
  'logging'           => true,
  'delivery_strategy' => 'spool',
  'spool_class'       => 'TestSpool',
  'spool_arguments'   => array('TestMailMessage'),
  'transport'         => array('class' => 'Swift_SmtpTransport', 'param' => array('username' => 'foo')),
));
$t->is($mailer->getRealTimeTransport()->getUsername(), 'foo', '__construct() passes the parameters to the main transport');

try
{
  $mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'spool'));

  $t->fail('__construct() throws an InvalidArgumentException exception if the spool_class option is not set with the spool delivery strategy');
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException exception if the spool_class option is not set with the spool delivery strategy');
}

$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'spool', 'spool_class' => 'TestSpool'));
$t->is(get_class($mailer->getTransport()), 'Swift_SpoolTransport', '__construct() recognizes the spool delivery strategy');
$t->is(get_class($mailer->getTransport()->getSpool()), 'TestSpool', '__construct() recognizes the spool delivery strategy');

// single address
try
{
  $mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'single_address'));

  $t->fail('__construct() throws an InvalidArgumentException exception if the delivery_address option is not set with the spool single_address strategy');
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException exception if the delivery_address option is not set with the spool single_address strategy');
}

$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'single_address', 'delivery_address' => 'foo@example.com'));
$t->is($mailer->getDeliveryAddress(), 'foo@example.com', '__construct() recognizes the single_address delivery strategy');

// logging
$mailer = new sfMailer($dispatcher, array('logging' => false));
$t->is($mailer->getLogger(), null, '__construct() disables logging if the logging option is set to false');
$mailer = new sfMailer($dispatcher, array('logging' => true));
$t->ok($mailer->getLogger() instanceof sfMailerMessageLoggerPlugin, '__construct() enables logging if the logging option is set to true');

// ->compose()
$t->diag('->compose()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$t->ok($mailer->compose() instanceof Swift_Message, '->compose() returns a Swift_Message instance');
$message = $mailer->compose('from@example.com', 'to@example.com', 'Subject', 'Body');
$t->is($message->getFrom(), array('from@example.com' => ''), '->compose() takes the from address as its first argument');
$t->is($message->getTo(), array('to@example.com' => ''), '->compose() takes the to address as its second argument');
$t->is($message->getSubject(), 'Subject', '->compose() takes the subject as its third argument');
$t->is($message->getBody(), 'Body', '->compose() takes the body as its fourth argument');

// ->composeAndSend()
$t->diag('->composeAndSend()');
$mailer = new sfMailer($dispatcher, array('logging' => true, 'delivery_strategy' => 'none'));
$mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
$t->is($mailer->getLogger()->countMessages(), 1, '->composeAndSend() composes and sends the message');
$messages = $mailer->getLogger()->getMessages();
$t->is($messages[0]->getFrom(), array('from@example.com' => ''), '->composeAndSend() takes the from address as its first argument');
$t->is($messages[0]->getTo(), array('to@example.com' => ''), '->composeAndSend() takes the to address as its second argument');
$t->is($messages[0]->getSubject(), 'Subject', '->composeAndSend() takes the subject as its third argument');
$t->is($messages[0]->getBody(), 'Body', '->composeAndSend() takes the body as its fourth argument');

// ->flushQueue()
$t->diag('->flushQueue()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
try
{
  $mailer->flushQueue();

  $t->fail('->flushQueue() throws a LogicException exception if the delivery_strategy is not spool');
}
catch (LogicException $e)
{
  $t->pass('->flushQueue() throws a LogicException exception if the delivery_strategy is not spool');
}

$mailer = new sfMailer($dispatcher, array(
  'delivery_strategy' => 'spool',
  'spool_class'       => 'TestSpool',
  'spool_arguments'   => array('TestMailMessage'),
  'transport'         => array('class' => 'TestMailerTransport'),
));
$transport = $mailer->getRealtimeTransport();
$spool = $mailer->getTransport()->getSpool();

$mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
$t->is($spool->getQueuedCount(), 1, '->flushQueue() sends messages in the spool');
$t->is($transport->getSentCount(), 0, '->flushQueue() sends messages in the spool');
$mailer->flushQueue();
$t->is($spool->getQueuedCount(), 0, '->flushQueue() sends messages in the spool');
$t->is($transport->getSentCount(), 1, '->flushQueue() sends messages in the spool');

// ->sendNextImmediately()
$t->diag('->sendNextImmediately()');
$mailer = new sfMailer($dispatcher, array(
  'logging'           => true,
  'delivery_strategy' => 'spool',
  'spool_class'       => 'TestSpool',
  'spool_arguments'   => array('TestMailMessage'),
  'transport'         => array('class' => 'TestMailerTransport'),
));
$transport = $mailer->getRealtimeTransport();
$spool = $mailer->getTransport()->getSpool();
$t->is($mailer->sendNextImmediately(), $mailer, '->sendNextImmediately() implements a fluid interface');
$mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
$t->is($spool->getQueuedCount(), 0, '->sendNextImmediately() bypasses the spool');
$t->is($transport->getSentCount(), 1, '->sendNextImmediately() bypasses the spool');
$transport->reset();
$spool->reset();

$mailer->composeAndSend('from@example.com', 'to@example.com', 'Subject', 'Body');
$t->is($spool->getQueuedCount(), 1, '->sendNextImmediately() bypasses the spool but only for the very next message');
$t->is($transport->getSentCount(), 0, '->sendNextImmediately() bypasses the spool but only for the very next message');

// ->getDeliveryAddress() ->setDeliveryAddress()
$t->diag('->getDeliveryAddress() ->setDeliveryAddress()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$mailer->setDeliveryAddress('foo@example.com');
$t->is($mailer->getDeliveryAddress(), 'foo@example.com', '->setDeliveryAddress() sets the delivery address for the single_address strategy');

// ->getLogger() ->setLogger()
$t->diag('->getLogger() ->setLogger()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$mailer->setLogger($logger = new sfMailerMessageLoggerPlugin($dispatcher));
$t->ok($mailer->getLogger() === $logger, '->setLogger() sets the mailer logger');

// ->getDeliveryStrategy()
$t->diag('->getDeliveryStrategy()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$t->is($mailer->getDeliveryStrategy(), 'none', '->getDeliveryStrategy() returns the delivery strategy');

// ->getRealtimeTransport() ->setRealtimeTransport()
$t->diag('->getRealtimeTransport() ->setRealtimeTransport()');
$mailer = new sfMailer($dispatcher, array('delivery_strategy' => 'none'));
$mailer->setRealtimeTransport($transport = new TestMailerTransport());
$t->ok($mailer->getRealtimeTransport() === $transport, '->setRealtimeTransport() sets the mailer transport');
