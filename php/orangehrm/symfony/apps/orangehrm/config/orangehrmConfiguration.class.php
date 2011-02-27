<?php

class orangehrmConfiguration extends sfApplicationConfiguration
{
  public function configure() {
      ProjectConfiguration::getActive()->loadHelpers(array('I18N'));
  }
  
  /**
   * Configure doctrine connections to use tablename prefix hs_hr_
   */
   public function configureDoctrine(Doctrine_Manager $manager) {
	$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

        //
        // If using encryption, enable dql callbacks. Needed by EncryptionListener
        //        
        if ( KeyHandler::keyExists()) {
            $manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
        }

       //$manager->setAttribute(Doctrine::ATTR_TBLNAME_FORMAT, 'hs_hr_%s');
   }
   
}
