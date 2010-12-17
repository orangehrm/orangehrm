<?php

/**
 * User form.
 *
 * @package    form
 * @subpackage User
 * @version    SVN: $Id: UserForm.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserForm extends BaseUserForm
{
  public function configure()
  {
    $profileForm = new ProfileForm($this->object->getProfile());
    unset($profileForm['id'], $profileForm['user_id']);

    $this->embedForm('Profile', $profileForm);
  }
}