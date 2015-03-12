<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */


class orangehrmPublishAPIsTask extends sfBaseTask {
    
    const RESOURCE_DIR_PREFIX = 'webres_';
    const RESOURCE_DIR_INC_FILE = 'resource_dir_inc.php';
    
    protected $resourceDir;
    
    protected function configure() {
        
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('plugin', null, sfCommandOption::PARAMETER_REQUIRED, 'plugin name', 'orangehrmCoreWebServicePlugin')
          ));
        
        $this->namespace = 'orangehrm';
        $this->name = 'publish-apis';

        $this->briefDescription = 'Publishes rest apis of OrangeHRM system';

        $this->detailedDescription = <<<EOF
The [plugin:publish-apis|INFO] Task will publish rest apis of OrangeHRM system.

  [./symfony orangehrm:publish-apis|INFO] --plugin=orangehrmAbcPlugin
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {    
        $pluginName = null;
        if (!empty($options['plugin'])) {
            $pluginName = $options['plugin'];
        } else if (isset($arguments['name'])) {
            $pluginName = $arguments['name'];
        }
        
        if (empty($pluginName)) {
            throw new sfCommandException('Plugin name must be specified as an argument');
        }
        $pluginsDir = sfConfig::get('sf_plugins_dir');
        $pluginDir = $pluginsDir . DIRECTORY_SEPARATOR . $pluginName;
        $resources = $pluginDir . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . "resources";
        if(is_dir($resources)){
            $this->mirrorDir($resources, $pluginsDir);
            $this->logSection('orangehrm', 'copied resources successfully');
        } else {
            throw new sfCommandException('There should be a resources folder inside plugin install folder');
        }
    }
    
    private function mirrorDir($src, $dest) {
        $finder = sfFinder::type('any');
        $filesystem = new sfFilesystem();                    
        $filesystem->mirror($src, $dest, $finder, array('override' => true));        
    }
}
?>
