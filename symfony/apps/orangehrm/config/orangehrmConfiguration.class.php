<?php

class orangehrmConfiguration extends sfApplicationConfiguration
{
  public function configure() {
      // Cookie settings for increased security
      ini_set('session.use_only_cookies', "1");
      ini_set('session.cookie_httponly', "1");
      
      ProjectConfiguration::getActive()->loadHelpers(array('I18N', 'OrangeDate', 'Orange', 'Url'));
      sfWidgetFormSchema::setDefaultFormFormatterName('Default');
  }
  
  /**
   * Configure doctrine connections to use tablename prefix hs_hr_
   */
    public function configureDoctrine(Doctrine_Manager $manager) {
       
        $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
        $manager->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
        $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'ohrmDoctrineQuery');

        //
        // If using encryption, enable dql callbacks. Needed by EncryptionListener
        //        
        if ( KeyHandler::keyExists()) {
            $manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
        }

       //$manager->setAttribute(Doctrine::ATTR_TBLNAME_FORMAT, 'hs_hr_%s');
        
        // Allow running doctrine:build-schema without error
        $isCli = (php_sapi_name() == "cli");
        if (true == $isCli) {
            Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, false);
        }        
    }
   
}
