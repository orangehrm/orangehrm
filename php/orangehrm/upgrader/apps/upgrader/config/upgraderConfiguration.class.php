<?php

class upgraderConfiguration extends sfApplicationConfiguration {
    
    public function configure() {
      ProjectConfiguration::getActive()->loadHelpers(array('I18N'));
    }
    
}
