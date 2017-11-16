<?php


/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

class ohrmWidgetFormInputPassword extends sfWidgetFormInputPassword
{
    private $configService = null;

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
      $html = parent::render($name, $value, $attributes, $errors);
      $helpText = __('For a strong password, please use a hard to guess combination of text with upper and lower case characters, symbols and numbers');
      $helpTextObjectAsHtml = "<span>$helpText</span>";

      $id = $this->generateId($name);
      $html .= content_tag('div', tag('span', array('id' => $id . '_help_text', 'class' => 'validation-error')));
      $html .= content_tag('div', tag('span', array('id' => $id . '_strength_meter', 'class' => 'passwordStrengthCheck')), array('class' => 'helpText'));
      $html .= content_tag('div', $helpTextObjectAsHtml, array('class' => 'helpText'));
      $passwordStrengthUrl = url_for("securityAuthentication/getPasswordStrengthAjax");
      $passwordStrengths = $this->getSecurityAuthenticationConfigService()->getPasswordStrengthsWithViewValues();
      foreach ($passwordStrengths as $key=>$passwordStrength){
          $passwordStrengths[$key] = __($passwordStrength);
      }


      $javascriptContent = sprintf("
        var passwordOptions = [
        '{$passwordStrengths['veryWeak']}', '{$passwordStrengths['weak']}', '{$passwordStrengths['better']}',
        '{$passwordStrengths['medium']}', '{$passwordStrengths['strong']}', '{$passwordStrengths['strongest']}'
    ];
        
        $(document).ready(function() {
           $('#{$id}').on('keyup', function(){
              showPasswordStrength($('#{$id}').val(), '{$passwordStrengthUrl}', '{$id}', passwordOptions );
           });         
        });
    ");
      $html .= content_tag('script', $javascriptContent, array('type' => 'text/javascript'));
    return $html;
  }


    public function getJavaScripts() {

        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/passwordStrengthMeterHelper.js');
        $javaScripts[] = plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/passwordStrengthMeterHelper.js');
        return $javaScripts;
    }

    public function getSecurityAuthenticationConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new SecurityAuthenticationConfigService();
        }
        return $this->configService;
    }
}
