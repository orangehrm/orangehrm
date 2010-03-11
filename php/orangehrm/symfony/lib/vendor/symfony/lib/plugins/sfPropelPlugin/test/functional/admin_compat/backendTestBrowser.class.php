<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class backendTestBrowser extends sfTestBrowser
{
  protected $moduleName = 'article';

  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;
  }

  public function checkListCustomization($title, $listParams)
  {
    $this->test()->diag($title);

    return $this->
      customizeGenerator(array('list' => $listParams))->
      getAndCheck($this->moduleName, 'list');
  }

  public function checkEditCustomization($title, $editParams)
  {
    $this->test()->diag($title);

    return $this->
      customizeGenerator(array('edit' => $editParams))->

      get(sprintf('/%s/edit/id/1', $this->moduleName))->
      isStatusCode(200)->
      isRequestParameter('module', $this->moduleName)->
      isRequestParameter('action', 'edit')
    ;
  }

  public function customizeGenerator($params)
  {
    $params['model_class'] = 'Article';
    $params['moduleName']  = $this->moduleName;
    sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
    $generatorManager = new sfGeneratorManager($this->getContext()->getConfiguration());
    if (!is_dir(sfConfig::get('sf_config_cache_dir')))
    {
      mkdir(sfConfig::get('sf_config_cache_dir'), 0777);
    }
    file_put_contents(sprintf('%s/modules_%s_config_generator.yml.php', sfConfig::get('sf_config_cache_dir'), $this->moduleName), '<?php '.sfGeneratorConfigHandler::getContent($generatorManager, 'sfPropelAdminGenerator', $params));

    return $this;
  }
}
