<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unfreezes symfony libraries.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectUnfreezeTask.class.php 13212 2008-11-21 19:53:12Z FabianLange $
 */
class sfProjectUnfreezeTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('unfreeze');
    $this->namespace = 'project';
    $this->name = 'unfreeze';
    $this->briefDescription = 'Unfreezes symfony libraries';

    $this->detailedDescription = <<<EOF
The [project:unfreeze|INFO] task removes all the symfony core files from
the current project:

  [./symfony project:unfreeze|INFO]

The task also changes [config/config.php|COMMENT] to switch to the
old symfony files used before the [project:freeze|COMMENT] command was used.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // remove lib/symfony and data/symfony directories
    if (!is_dir('lib/symfony'))
    {
      throw new sfCommandException('You can unfreeze only if you froze the symfony libraries before.');
    }

    // change symfony path in ProjectConfiguration.class.php
    $config = sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php';
    $content = file_get_contents($config);
    if (preg_match('/^# FROZEN_SF_LIB_DIR\: (.+?)$/m', $content, $match))
    {
      $publishAssets = new sfPluginPublishAssetsTask($this->dispatcher, $this->formatter);
      $publishAssets->setCommandApplication($this->commandApplication);

      $symfonyLibDir = $match[1];

      $content = str_replace("# FROZEN_SF_LIB_DIR: {$match[1]}\n\n", '', $content);
      // need to escape windows pathes "symfony\1.2" -> "symfony\\1.2"
      // because preg_replace would then use \1 as group identifier resulting in "symfony.2"
      $content = preg_replace('#^require_once.+?$#m', sprintf("require_once '%s/autoload/sfCoreAutoload.class.php';", str_replace('\\', '\\\\', $symfonyLibDir)), $content, 1);
      file_put_contents($config, $content);

      // re-publish assets
      $publishAssets->run(array(), array('--symfony-lib-dir='.$symfonyLibDir));

      // remove files
      $finder = sfFinder::type('any');
      $this->getFilesystem()->remove($finder->in(sfConfig::get('sf_lib_dir').'/symfony'));
      $this->getFilesystem()->remove(sfConfig::get('sf_lib_dir').'/symfony');
      $this->getFilesystem()->remove($finder->in(sfConfig::get('sf_data_dir').'/symfony'));
      $this->getFilesystem()->remove(sfConfig::get('sf_data_dir').'/symfony');
      $this->getFilesystem()->remove($finder->in(sfConfig::get('sf_web_dir').'/sf'));
      $this->getFilesystem()->remove(sfConfig::get('sf_web_dir').'/sf');
    }
  }
}
