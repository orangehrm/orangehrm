<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
      $html .= content_tag('div', tag('span', array('id' => $id . '_strength_meter', 'class' => 'passwordStrengthCheck')));
      $html .= content_tag('div', $helpTextObjectAsHtml);
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
