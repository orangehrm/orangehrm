<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(47);

class MySessionStorage extends sfSessionTestStorage
{
  public function regenerate($destroy = false)
  {
    $this->sessionId = rand(1, 9999);

    return true;
  }
}

$dispatcher = new sfEventDispatcher();
$sessionPath = sys_get_temp_dir().'/sessions_'.rand(11111, 99999);
$storage = new MySessionStorage(array('session_path' => $sessionPath));

$user = new sfBasicSecurityUser($dispatcher, $storage);

// ->initialize()
$t->diag('->initialize()');
$t->todo('->initialize() times out the user if no request made for a long time');

// ->getCredentials()
$t->diag('->getCredentials()');
$user->clearCredentials();
$user->addCredential('user');
$t->is($user->getCredentials(), array('user'), '->getCredentials() returns user credentials as an array');

// ->setAuthenticated() ->isAuthenticated()
$t->diag('->setAuthenticated() ->isAuthenticated()');
$t->is($user->isAuthenticated(), false, '->isAuthenticated() returns false by default');
$user->setAuthenticated(true);
$t->is($user->isAuthenticated(), true, '->isAuthenticated() returns true if the user is authenticated');
$user->setAuthenticated(false);
$t->is($user->isAuthenticated(), false, '->setAuthenticated() accepts a boolean as its first parameter');

// session id regeneration
$user->setAuthenticated(false);
$id = $storage->getSessionId();
$user->setAuthenticated(true);
$t->isnt($id, $id = $storage->getSessionId(), '->setAuthenticated() regenerates the session id if the authentication changes');
$user->setAuthenticated(true);
$t->is($storage->getSessionId(), $id, '->setAuthenticated() does not regenerate the session id if the authentication does not change');
$user->addCredential('foo');
$t->isnt($id, $id = $storage->getSessionId(), '->addCredential() regenerates the session id if a new credential is added');
$t->is($id, $storage->getSessionId(), '->addCredential() does not regenerate the session id if the credential already exists');
$user->removeCredential('foo');
$t->isnt($id, $id = $storage->getSessionId(), '->removeCredential() regenerates the session id if a credential is removed');
$t->is($id, $storage->getSessionId(), '->removeCredential() does not regenerate the session id if the credential does not exist');

// ->setTimedOut() ->getTimedOut()
$user = new sfBasicSecurityUser($dispatcher, $storage);
$t->diag('->setTimedOut() ->isTimedOut()');
$t->is($user->isTimedOut(), false, '->isTimedOut() returns false if the session is not timed out');
$user->setTimedOut();
$t->is($user->isTimedOut(), true, '->isTimedOut() returns true if the session is timed out');

// ->hasCredential()
$t->diag('->hasCredential()');
$user->clearCredentials();
$t->is($user->hasCredential('admin'), false, '->hasCredential() returns false if user has not the credential');

$user->addCredential('admin');
$t->is($user->hasCredential('admin'), true, '->addCredential() takes a credential as its first argument');

// admin AND user
$t->is($user->hasCredential(array('admin', 'user')), false, '->hasCredential() can takes an array of credential as a parameter');

// admin OR user
$t->is($user->hasCredential(array(array('admin', 'user'))), true, '->hasCredential() can takes an array of credential as a parameter');

// (admin OR user) AND owner
$t->is($user->hasCredential(array(array('admin', 'user'), 'owner')), false, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('owner');
$t->is($user->hasCredential(array(array('admin', 'user'), 'owner')), true, '->hasCredential() can takes an array of credential as a parameter');

// [[root, admin, editor, [supplier, owner], [supplier, group], accounts]]
// root OR admin OR editor OR (supplier AND owner) OR (supplier AND group) OR accounts
$user->clearCredentials();
$credential = array(array('root', 'admin', 'editor', array('supplier', 'owner'), array('supplier', 'group'), 'accounts'));
$t->is($user->hasCredential($credential), false, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('admin');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');
$user->clearCredentials();
$user->addCredential('supplier');
$t->is($user->hasCredential($credential), false, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('owner');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');

// [[root, [supplier, [owner, quasiowner]], accounts]]
// root OR (supplier AND (owner OR quasiowner)) OR accounts
$user->clearCredentials();
$credential = array(array('root', array('supplier', array('owner', 'quasiowner')), 'accounts'));
$t->is($user->hasCredential($credential), false, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('root');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');
$user->clearCredentials();
$user->addCredential('supplier');
$t->is($user->hasCredential($credential), false, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('owner');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');
$user->addCredential('quasiowner');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');
$user->removeCredential('owner');
$t->is($user->hasCredential($credential), true, '->hasCredential() can takes an array of credential as a parameter');
$user->removeCredential('supplier');
$t->is($user->hasCredential($credential), false, '->hasCredential() can takes an array of credential as a parameter');

$user->clearCredentials();
$user->addCredential('admin');
$user->addCredential('user');
$t->is($user->hasCredential('admin'), true);
$t->is($user->hasCredential('user'), true);

$user->addCredentials('superadmin', 'subscriber');
$t->is($user->hasCredential('subscriber'), true);
$t->is($user->hasCredential('superadmin'), true);

// admin and (user or subscriber)
$t->is($user->hasCredential(array(array('admin', array('user', 'subscriber')))), true);

$user->addCredentials(array('superadmin1', 'subscriber1'));
$t->is($user->hasCredential('subscriber1'), true);
$t->is($user->hasCredential('superadmin1'), true);

// admin and (user or subscriber) and (superadmin1 or subscriber1)
$t->is($user->hasCredential(array(array('admin', array('user', 'subscriber'), array('superadmin1', 'subscriber1')))), true);

// numerical credentials
$user->clearCredentials();
$user->addCredentials(array('1', 2));
$t->is($user->hasCredential(1), true, '->hasCrendential() supports numerical credentials');
$t->is($user->hasCredential('2'), true, '->hasCrendential() supports numerical credentials');
$t->is($user->hasCredential(array('1', 2)), true, '->hasCrendential() supports numerical credentials');
$t->is($user->hasCredential(array(1, '2')), true, '->hasCrendential() supports numerical credentials');

// ->removeCredential()
$t->diag('->removeCredential()');
$user->removeCredential('user');
$t->is($user->hasCredential('user'), false);

// ->clearCredentials()
$t->diag('->clearCredentials()');
$user->clearCredentials();
$t->is($user->hasCredential('subscriber'), false);
$t->is($user->hasCredential('superadmin'), false);

// timeout
$user->setAuthenticated(true);
$user->shutdown();
$user = new sfBasicSecurityUser($dispatcher, $storage, array('timeout' => 0));
$t->is($user->isTimedOut(), true, '->initialize() times out the user if no request made for a long time');

$user = new sfBasicSecurityUser($dispatcher, $storage, array('timeout' => false));
$t->is($user->isTimedOut(), false, '->initialize() takes a timeout parameter which can be false to disable session timeout');

sfToolkit::clearDirectory($sessionPath);
