<?php

class LoginForm extends sfForm {

    public function configure() {
        $installation = "";
        if (isset($_SESSION['Installation'])) {
            $installation = $_SESSION['Installation'];
        }
        $this->setWidgets(array(
            'Username' => new sfWidgetFormInputText(array(), array(
                'name' => 'txtUsername',
                'id' => 'txtUsername',
            )),
            'Password' => new sfWidgetFormInputPassword(array(), array(
                'name' => 'txtPassword',
                'id' => 'txtPassword',
                'autocomplete' => 'off',
            )),

            'Installation' => new sfWidgetFormInputHidden(array(), array(
                'name' => 'installation',
                'id' => 'installation',
            ))
        ));

        $this->widgetSchema['Password']->setOption('always_render_empty', false);

        $this->setDefaults(array(
            'Username' => $_SESSION['AdminUserName'],
            'Password' => $_SESSION['AdminPassword'],
            'Installation' => $installation,

        ));

    }

}

