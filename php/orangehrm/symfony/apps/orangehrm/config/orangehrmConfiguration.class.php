<?php

class orangehrmConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
  }
  
  /**
   * Configure doctrine connections to use tablename prefix hs_hr_
   */
   public function configureDoctrine(Doctrine_Manager $manager) {
       //$manager->setAttribute(Doctrine::ATTR_TBLNAME_FORMAT, 'hs_hr_%s');
   }
   
}
