<?php

class orangehrmInstallPluginTask extends sfBaseTask{

  protected function configure()  {
    
    // for backwards compatibility, name is optional and we allow user to specify the name
    // using --plugin="orangehrmAbcPlugin"      
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::OPTIONAL, 'The plugin name'),
    ));      

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('plugin', null, sfCommandOption::PARAMETER_REQUIRED, 'plugin name', ''),
      
    ));

    $this->aliases = array('orangehrm:Install-plugin'); // for backwards compatibility
    $this->namespace        = 'orangehrm';
    $this->name             = 'install-plugin';
    $this->briefDescription = 'Installs the given OrangeHRM plugin';
    $this->detailedDescription = <<<EOF
The [orangehrm:install-plugin|INFO] task installs a plugin:

  [./symfony orangehrm:install-plugin orangehrmAbcPlugin|INFO]
EOF;
  
  }

  protected function execute($arguments = array(), $options = array()){
    
        $databaseManager    = new sfDatabaseManager($this->configuration);
        $connection         = $databaseManager->getDatabase($options['connection'])->getConnection();
        
        $pluginName = null;
        if (!empty($options['plugin'])) {
            $pluginName = $options['plugin'];
        } else if (isset($arguments['name'])) {
            $pluginName = $arguments['name'];
        }
        
        if (empty($pluginName)) {
            throw new sfCommandException('Plugin name must be specified as an argument');
        }

        define('SF_ROOT_DIR', realpath(dirname(__FILE__). DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
        define('SF_APP', 'orangehrm');
        define('SF_ENVIRONMENT', 'prod');
        define('SF_DEBUG', true);
        require_once(dirname(__FILE__). DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'ProjectConfiguration.class.php');
        #
        $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'prod', true);
        sfContext::createInstance($configuration);

        $installer = new PluginInstaller($this);
        $installer->installPlugin($pluginName);

        $this->logSection('orangehrm', $pluginName . " installed");   
  }
}
