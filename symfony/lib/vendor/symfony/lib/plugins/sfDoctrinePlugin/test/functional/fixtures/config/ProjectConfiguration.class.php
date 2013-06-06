<?php

require_once dirname(__FILE__).'/../../../../../../autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enableAllPluginsExcept(array('sfPropelPlugin'));
  }

  public function initializeDoctrine()
  {
    chdir(sfConfig::get('sf_root_dir'));

    $task = new sfDoctrineBuildTask($this->dispatcher, new sfFormatter());
    $task->setConfiguration($this);
    $task->run(array(), array(
      'no-confirmation' => true,
      'db'              => true,
      'model'           => true,
      'forms'           => true,
      'filters'         => true,
    ));
  }

  public function loadFixtures($fixtures)
  {
    $path = sfConfig::get('sf_data_dir') . '/' . $fixtures;
    if ( ! file_exists($path)) {
      throw new sfException('Invalid data fixtures file');
    }
    chdir(sfConfig::get('sf_root_dir'));
    $task = new sfDoctrineDataLoadTask($this->dispatcher, new sfFormatter());
    $task->setConfiguration($this);
    $task->run(array($path));
  }

  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, true);

    $options = array('baseClassName' => 'myDoctrineRecord');
    sfConfig::set('doctrine_model_builder_options', $options);
  }

  public function configureDoctrineConnection(Doctrine_Connection $connection)
  {
  }

  public function configureDoctrineConnectionDoctrine2(Doctrine_Connection $connection)
  {
    $connection->setAttribute(Doctrine_Core::ATTR_VALIDATE, false);
  }
}
