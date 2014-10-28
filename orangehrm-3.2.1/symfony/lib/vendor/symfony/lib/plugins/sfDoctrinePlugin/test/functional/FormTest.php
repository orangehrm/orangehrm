<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$t = new lime_test(17);

// test for ticket #4935
$user = new User();
$profile = $user->getProfile();

$userForm = new UserForm($user);
$profileForm = new ProfileForm($profile);
unset($profileForm['id'], $profileForm['user_id']);

$userForm->embedForm('Profile', $profileForm);

$data = array('username' => 'jwage',
              'password' => 'changeme',
              'Profile'  => array(
                  'first_name' => 'Jonathan',
                  'last_name'  => 'Wage'
                ));

$userForm->bind($data);
$userForm->save();

$t->is($user->getId() > 0, true);
$t->is($user->getId(), $profile->getUserId());
$t->is($user->getUsername(), 'jwage');
$t->is($profile->getFirstName(), 'Jonathan');

$userCount = Doctrine_Query::create()
  ->from('User u')
  ->count();

$t->is($userCount, 1);

$profileCount = Doctrine_Query::create()
  ->from('Profile p')
  ->count();

$t->is($profileCount, 1);

$widget = new sfWidgetFormDoctrineChoice(array('model' => 'User'));
$t->is($widget->getChoices(), array(1 => 1));

$widget = new sfWidgetFormDoctrineChoice(array('model' => 'User', 'key_method' => 'getUsername', 'method' => 'getPassword'));
$t->is($widget->getChoices(), array('jwage' => '4cb9c8a8048fd02294477fcb1a41191a'));

$widget = new sfWidgetFormDoctrineChoice(array('model' => 'User', 'key_method' => 'getUsername', 'method' => 'getPassword'));
$t->is($widget->getChoices(), array('jwage' => '4cb9c8a8048fd02294477fcb1a41191a'));

$methods = array(
  'widgetChoiceTableMethod1',
  'widgetChoiceTableMethod2',
  'widgetChoiceTableMethod3'
);

foreach ($methods as $method)
{
  $widget = new sfWidgetFormDoctrineChoice(array('model' => 'User', 'table_method' => $method));
  $t->is($widget->getChoices(), array(1 => 1));
}

$widget = new sfWidgetFormDoctrineChoice(array('model' => 'User', 'table_method' => 'widgetChoiceTableMethod4'));
$t->is($widget->getChoices(), array());

$user = new User();
$user->Groups[]->name = 'User Group 1';
$user->Groups[]->name = 'User Group 2';

class UserGroupForm extends GroupForm
{
  public function configure()
  {
    parent::configure();
    $this->useFields(array('name'));
  }
}

$userForm = new UserForm($user);
$userForm->embedRelation('Groups', 'UserGroupForm');

$data = array(
  'username' => 'jonwage',
  'password' => 'changeme',
  'Groups'  => array(
    0 => array(
      'name' => 'New User Group 1 Name'
    ),
    1 => array(
      'name' => 'New User Group 2 Name'
    )
  )
);

$userForm->bind($data);
$t->is($userForm->isValid(), true);

if ($userForm->isValid())
{
  $userForm->save();
}

$t->is($user->Groups[0]->name, 'New User Group 1 Name');
$t->is($user->Groups[1]->name, 'New User Group 2 Name');

$form = new DefaultValueTestForm();
$validatorSchema = $form->getValidatorSchema();
$t->is($validatorSchema['name']->getOption('required'), false);